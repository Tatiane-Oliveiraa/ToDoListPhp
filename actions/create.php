<?php
session_start();
require_once('../database/conn.php');

$description = filter_input(INPUT_POST, 'description');
$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);

if (!empty($description) && !empty($date)) {
    $sql = $pdo->prepare("INSERT INTO task (description, date) VALUES (:description, :date)");
    $sql->bindValue(':description', $description);
    $sql->bindValue(':date', $date);
    $sql->execute();

    $_SESSION['message'] = 'Tarefa criada com sucesso!';
} else {
    $_SESSION['message'] = 'Preencha todos os campos!';
}

header('Location: ../index.php');
exit;
?>
