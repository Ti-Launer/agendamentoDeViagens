<?php
try {
    $dsn = 'mysql:host=localhost;dbname=agendamentoDeViagens;charset=utf8mb4';
    $username = 'root'; // substitua se necessário
    $password = '';     // substitua se necessário
    $pdo = new PDO($dsn, $username, $password);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}