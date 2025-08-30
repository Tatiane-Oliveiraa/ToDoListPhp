$(document).ready(function () {

    // Evento de clique no botão de edição
    $('.edit-button').on('click', function () {
        const $task = $(this).closest('.task');

        // Esconde os elementos da tarefa
        $task.find('.progress, .task-description, .task-actions').addClass('hidden');

        // Exibe o formulário de edição
        $task.find('.edit-task').removeClass('hidden');
    });

    // Evento de clique no checkbox de progresso
    $('.progress').on('change', function () {
        const $checkbox = $(this);
        const id = $checkbox.data('task-id');
        const completed = $checkbox.is(':checked');

        // Atualiza visualmente a tarefa
        $checkbox.toggleClass('done', completed);

        // Envia atualização via AJAX
        $.ajax({
            url: 'actions/update-progress.php', // Corrigido o caminho relativo
            method: 'POST',
            data: {
                id: id,
                completed: completed ? 1 : 0
            },
            dataType: 'json',
            success: function (response) {
                if (!response.success) {
                    alert('Erro ao atualizar o status da tarefa.');
                }
            },
            error: function () {
                alert('Ocorreu um erro na comunicação com o servidor.');
            }
        });
    });

});



document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: 'actions/calendar-feed.php', // Certifique-se de que esse arquivo existe e retorna JSON
        eventClick: function(info) {
            alert('📝 Tarefa: ' + info.event.title + '\n📅 Data: ' + info.event.start.toLocaleDateString());
        }
    });

    calendar.render();
});

