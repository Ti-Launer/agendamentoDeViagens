<?php
require_once "db.php";
try {
    $database = new Database();
    $pdo = $database->connect();
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
function fetchCars() {
    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = 'SELECT id, modelo, placa, detalhe, condicao, ativo FROM carros';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar carros: " . $e->getMessage();
        return [];
    }
}