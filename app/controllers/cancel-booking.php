<?php
require_once 'db.php';
function cancelarReserva($id) {
    $database = new Database();
    $pdo = $database->connect();

    try {
        if (empty($id)) {
            throw new Exception("ID da reserva obrigatório.");
        }

        $sql = "UPDATE reservas SET status = 'cancelado' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['status' => 'success', 'message' => 'Reserva cancelada.'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Erro ao cancelar reserva: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id) {
    $result = cancelarReserva($id);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID da reserva não fornecido.']);
}