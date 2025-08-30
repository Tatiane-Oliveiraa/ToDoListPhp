<?php
require_once('../database/conn.php');

$sql = $pdo->query("SELECT id, description, date FROM task");

$events = [];

while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['description'],
        'start' => $row['date']
    ];
}

echo json_encode($events);
?>
