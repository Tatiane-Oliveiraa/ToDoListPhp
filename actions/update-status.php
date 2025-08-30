<?php
require_once('../database/conn.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if ($id && $status) {
    $sql = $pdo->prepare("UPDATE task SET status = :status WHERE id = :id");
    $sql->bindValue(':status', $status);
    $sql->bindValue(':id', $id);
    $success = $sql->execute();

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}
?>
