<?php
session_start();
require_once('database/conn.php');

$tasks = [];

$sql = $pdo->query("SELECT * FROM task ORDER BY completed ASC");

if ($sql->rowCount() > 0) {
    $tasks = $sql->fetchAll(PDO::FETCH_ASSOC);
}

$columns = [
    'todo' => 'A Fazer',
    'doing' => 'Em Progresso',
    'done' => 'Concluído'
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>

    <!-- Font Awesome & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="src/styles/style.css">
</head>
<body>
    <div id="to-do">
        <div id="calendar"></div>

        <h1>Tarefas</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="actions/create.php" method="POST" class="to-do-form">
            <input type="text" name="description" placeholder="Escreva sua tarefa aqui" required>
            <input type="date" name="date" required>
            <select name="status" required>
                <option value="todo">A Fazer</option>
                <option value="doing">Em Progresso</option>
                <option value="done">Concluído</option>
            </select>
            <button type="submit" class="form-button">
                <i class="fa-solid fa-plus"></i>
            </button>
        </form>

        <div class="kanban-board">
            <?php foreach ($columns as $key => $title): ?>
                <div class="kanban-column" data-status="<?= $key ?>">
                    <h2><?= $title ?></h2>
                    <div class="task-list" id="list-<?= $key ?>">
                        <?php foreach ($tasks as $task): ?>
                            <?php if ($task['status'] === $key): ?>
                                <div class="task" data-id="<?= $task['id'] ?>">
                                    <input 
                                        type="checkbox" 
                                        class="progress <?= $task['completed'] ? 'done' : '' ?>"
                                        data-task-id="<?= $task['id']?>"
                                        <?= $task['completed'] ? 'checked' : '' ?>
                                    >

                                    <p class="task-description">
                                        <?= $task['description'] ?>
                                        <?php if (!empty($task['date'])): ?>
                                            <span class="task-date"> - <?= date('d/m/Y', strtotime($task['date'])) ?></span>
                                        <?php endif; ?>
                                    </p>

                                    <div class="task-actions">
                                        <a class="action-button edit-button">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="actions/delete.php?id=<?= $task['id']?>" class="action-button delete-button">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </div>

                                    <form action="actions/update.php" method='POST' class="to-do-form edit-task hidden">
                                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                        <input type="text" name="description" value="<?= $task['description'] ?>" required>
                                        <button type="submit" class="form-button confirm-button">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Calendário + Drag-and-Drop -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // FullCalendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            editable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: 'actions/calendar-feed.php',
            eventClick: function(info) {
                alert('📝 Tarefa: ' + info.event.title + '\n📅 Data: ' + info.event.start.toLocaleDateString());
            },
            eventDrop: function(info) {
                const id = info.event.id;
                const newDate = info.event.startStr;

                $.ajax({
                    url: 'actions/update-date.php',
                    method: 'POST',
                    data: { id: id, date: newDate },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.success) {
                            alert('Erro ao atualizar a data.');
                            info.revert();
                        }
                    },
                    error: function() {
                        alert('Erro de comunicação.');
                        info.revert();
                    }
                });
            }
        });
        calendar.render();

        // SortableJS para Kanban
        document.querySelectorAll('.task-list').forEach(list => {
            new Sortable(list, {
                group: 'shared-tasks',
                animation: 150,
                onAdd: function (evt) {
                    const taskId = evt.item.dataset.id;
                    const newStatus = evt.to.closest('.kanban-column').dataset.status;

                    $.ajax({
                        url: 'actions/update-status.php',
                        method: 'POST',
                        data: { id: taskId, status: newStatus },
                        dataType: 'json',
                        success: function (response) {
                            if (!response.success) {
                                alert('Erro ao atualizar o status da tarefa.');
                            }
                        },
                        error: function () {
                            alert('Erro de comunicação com o servidor.');
                        }
                    });
                }
            });
        });
    });
    </script>

    <!-- Script de interação -->
    <script src="src/javascript/script.js"></script>
</body>
</html>
