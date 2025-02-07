<?php
require_once 'db.php';
header('Content-Type: application/json');

require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['new_km_final'])) {
    $id = intval($_POST['id']);
    $newKmFinal = intval($_POST['new_km_final']);

    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = "UPDATE reservas SET km_final = :new_km_final WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_km_final', $newKmFinal, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'KM Final atualizado.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar KM Final.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Atualiza o km_inicial da reserva próxima desta
    try {
        $sql = "SELECT carro, data_fim FROM reservas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reservaAtual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservaAtual) {
            echo json_encode(['status' => 'error', 'message' => 'Reserva atual não encontrada.']);
            exit;
        }

        // Agora, encontre a próxima reserva para o mesmo carro
        // Aqui, vamos supor que 'data_inicio' ordena as reservas e a próxima reserva é aquela cujo 'data_inicio' é maior que a 'data_fim' da reserva atual
        $sql = "SELECT id FROM reservas 
                WHERE carro = :carro 
                  AND data_inicio > :data_fim 
                ORDER BY data_inicio ASC 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':carro', $reservaAtual['carro'], PDO::PARAM_STR);
        $stmt->bindParam(':data_fim', $reservaAtual['data_fim']);
        $stmt->execute();
        $proximaReserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proximaReserva) {
            // Atualiza o km_inicial da próxima reserva com o novo km_final da reserva atual
            $sql = "UPDATE reservas SET km_inicial = :km_inicial WHERE id = :id";
            $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':km_inicial', $newKmFinal, PDO::PARAM_INT);
            $stmt->bindParam(':id', $proximaReserva['id'], PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Próxima reserva atualizada.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar próxima reserva.']);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Nenhuma próxima reserva encontrada.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
}