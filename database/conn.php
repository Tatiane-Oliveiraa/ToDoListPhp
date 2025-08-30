<?php

$hostname = 'localhost';
$database = 'to_do_list';  // Nome do banco de dados no MySQL
$username = 'root';         // Usuário padrão do MySQL no Laragon é 'root'
$password = '';             // Senha do usuário 'root' no MySQL, geralmente em branco no Laragon

try {
    // Conectando ao banco MySQL
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida ao MySQL!";
} catch (PDOException $erro) {
    echo "Erro: " . $erro->getMessage();
}
?>
