<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['carId'], $_POST['usage'])) {
    $carId= $_POST['carId'];
    $usage= intval($_POST['usage']);

    try {
        $sql = 'UPDATE carros SET ativo = :usage WHERE placa = :carId';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':carId', $carId);
        $stmt->bindParam(':usage', $usage);
        error_log("carId: $carId");
        error_log("usage: $usage");

        $stmt->execute();
        header('Content-Type: application/json');
        echo json_encode(['message'=> 'Status atualizado com sucesso!']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error'=> 'Erro ao atualizar status: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error'=> 'Método não permitido']);
}