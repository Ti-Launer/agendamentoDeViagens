<?php
session_start();
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

        if ($stmt->execute()){
            $sql = 'SELECT modelo, placa FROM carros WHERE placa = :carId';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':carId', $carId);
            $stmt->execute();
            $carro = $stmt->fetch(PDO::FETCH_ASSOC);
            $modelo = $carro['modelo'];
            $placa = $carro['placa'];

            $type = "Colocou carro $modelo - $placa em manutenção";
            // Inserção do log
            if (isset($_SESSION['admin_name'])) {
                $admin_name = $_SESSION['admin_name'];
                $sql = "INSERT INTO logs_table (admin, type, datetime) VALUES (:admin, :type, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':admin', $admin_name, PDO::PARAM_STR);
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                throw new Exception("Sessão do administrador não encontrada.");
            } 
        }
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
