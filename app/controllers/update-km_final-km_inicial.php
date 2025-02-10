<?php
require_once 'db.php'; // Inclui o arquivo de conexão com o banco de dados
header('Content-Type: application/json'); // Define o cabeçalho para JSON

// Verifica se a requisição é POST e se os parâmetros necessários estão presentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['new_km_final'])) {
    $id = intval($_POST['id']); // Converte o ID para inteiro
    $newKmFinal = intval($_POST['new_km_final']); // Converte o novo KM Final para inteiro

    $database = new Database();
    $pdo = $database->connect();

    try {
        // Inicia uma transação para garantir atomicidade
        $pdo->beginTransaction();

        // Atualiza o KM Final da reserva atual
        $sql = "UPDATE reservas SET km_final = :new_km_final WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_km_final', $newKmFinal, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Busca os dados da reserva atual
        $sql = "SELECT carro, data_fim FROM reservas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $reservaAtual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservaAtual) {
            throw new Exception("Reserva atual não encontrada.");
        }

        // Busca a próxima reserva para o mesmo carro
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

        // Se houver uma próxima reserva, atualiza o KM Inicial dela
        if ($proximaReserva) {
            $sql = "UPDATE reservas SET km_inicial = :km_inicial WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':km_inicial', $newKmFinal, PDO::PARAM_INT);
            $stmt->bindParam(':id', $proximaReserva['id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        // Confirma a transação
        $pdo->commit();

        // Retorna uma resposta de sucesso
        echo json_encode([
            'status' => 'success',
            'message' => 'KM Final e próxima reserva atualizados com sucesso.'
        ]);
    } catch (Exception $e) {
        // Em caso de erro, reverte a transação
        $pdo->rollBack();

        // Retorna uma mensagem de erro
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    // Retorna um erro se os parâmetros estiverem ausentes
    echo json_encode([
        'status' => 'error',
        'message' => 'Parâmetros inválidos.'
    ]);
}