<?php
require_once "db.php";
try {
    $database = new Database();
    $pdo = $database->connect();
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
function fetchAdmins() {
    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = 'SELECT id, nome, email, ativo FROM admins';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar administradores: " . $e->getMessage();
        return [];
    }
}