<?php
session_start(); // Certifique-se de iniciar a sessão
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['new_km_final'])) {
    $id = intval($_POST['id']); // Converte o ID para inteiro
    $newKmFinal = intval($_POST['new_km_final']); // Converte o novo KM Final para inteiro

    $database = new Database();
    $pdo = $database->connect();

    try {
        // Inicia uma transação para garantir atomicidade
        $pdo->beginTransaction();

        // Coleta o antigo km_final
        $sql = "SELECT km_final FROM reservas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $oldKmFinalData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$oldKmFinalData) {
            throw new Exception("Reserva não encontrada para id: $id");
        }
        $oldKmFinal = $oldKmFinalData['km_final'];

        // Atualiza o KM Final da reserva atual
        $sql = "UPDATE reservas SET km_final = :new_km_final WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_km_final', $newKmFinal, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Busca os dados da reserva atual (para obter o carro e a data_fim)
        $sql = "SELECT carro, data_fim FROM reservas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reservaAtual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservaAtual) {
            throw new Exception("Reserva atual não encontrada.");
        }

        // Busca a próxima reserva para o mesmo carro, com data_inicio > data_fim da reserva atual
        $sql = "SELECT id, km_inicial FROM reservas 
                WHERE carro = :carro 
                  AND data_inicio > :data_fim 
                ORDER BY data_inicio ASC 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':carro', $reservaAtual['carro'], PDO::PARAM_STR);
        $stmt->bindParam(':data_fim', $reservaAtual['data_fim']);
        $stmt->execute();
        $proximaReserva = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cria a mensagem de log
        $type = "Editou Km Final da Reserva #$id de '$oldKmFinal' para '$newKmFinal'";
        if ($proximaReserva) {
            // Atualiza o KM Inicial da próxima reserva para o mesmo carro
            $sql = "UPDATE reservas SET km_inicial = :km_inicial WHERE id = :next_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':km_inicial', $newKmFinal, PDO::PARAM_INT);
            $stmt->bindParam(':next_id', $proximaReserva['id'], PDO::PARAM_INT);
            $stmt->execute();
            $proximaReservaId = $proximaReserva['id'];
            $type .= ", atualizando o Km Inicial da Reserva #$proximaReservaId";
        }

        $pdo->commit();

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

        echo json_encode([
            'status' => 'success',
            'message' => 'KM Final e próxima reserva atualizados com sucesso.'
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Parâmetros inválidos.'
    ]);
}
?>
