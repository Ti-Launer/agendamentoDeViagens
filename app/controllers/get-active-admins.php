<?php
require_once "db.php";
try {
    $database = new Database();
    $pdo = $database->connect();
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

function fetchActiveAdmins()
{
    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = 'SELECT email FROM admins_ativos';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Erro ao buscar carros: " . $e->getMessage();
        return [];
    }
}
