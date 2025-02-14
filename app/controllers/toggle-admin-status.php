<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminId = $_POST['adminId'];
    $newStatus = $_POST['newStatus'];

    // Validação Básica
    if (!is_numeric($adminId) || !in_array($newStatus, ['yes', 'no'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Parâmetros inválidos.']);
        exit;
    }

    try {
        $sql = 'UPDATE admins SET ativo = :newStatus WHERE id = :adminId';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':adminId', $adminId);
        $stmt->bindParam(':newStatus', $newStatus);
        error_log("adminId: $adminId");
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
