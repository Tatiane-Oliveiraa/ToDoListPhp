<?php

session_start();

require_once('../database/conn.php');

$description = filter_input(INPUT_POST, 'description');
$id = filter_input(INPUT_POST, 'id');

if (!empty($description) && !empty($id)) {
    $sql = $pdo->prepare("UPDATE task SET description = :description WHERE id = :id");
    $sql->bindValue(':description', $description);
    $sql->bindValue(':id', $id);
    $sql->execute();

     $_SESSION['message'] = 'Tarefa atualizada com sucesso!';
} else {
    $_SESSION['message'] = 'Erro ao atualizar tarefa!';
}

header('Location: ../index.php');
exit;
?>
