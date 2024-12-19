<?php
require_once "db.php";

function confirmarReserva($id, $carro) {
    $database = new Database();
    $pdo = $database->connect();

    try {
        if (empty($id) || empty($carro)) {
            throw new Exception("ID da reserva e carro são obrigatórios.");
        }

        $sql = "UPDATE reservas SET status = 'confirmado', carro = :carro WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':carro', $carro, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['status' => 'success', 'message' => 'Reserva confirmada. Dados inseridos automaticamente na agenda de carros.'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Erro ao confirmar reserva: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$carro = $data['carro'] ?? null;

if ($id && $carro) {
    $result = confirmarReserva($id, $carro);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID da reserva ou carro não fornecido.']);
}
?>
