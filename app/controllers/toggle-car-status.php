<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $carId = $_POST['carId'];
    $newStatus = $_POST['newStatus'];

    try {
        $sql = 'UPDATE carros SET condicao = :newStatus WHERE placa = :carId';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':carId', $carId);
        $stmt->bindParam(':newStatus', $newStatus);
        error_log("carId: $carId");
        error_log("newStatus: $newStatus");

        $stmt->execute();
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Status atualizado com sucesso!']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar status: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
