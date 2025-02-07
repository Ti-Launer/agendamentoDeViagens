<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Coleta e sanitiza os valores enviados via POST
    $km_final = htmlspecialchars($_POST['km_final']);
    $id = htmlspecialchars($_POST['id']);
    error_log("Recebido id da reserva: $id");

    $database = new Database();
    $conn = $database->connect();

    // Buscar a reserva atual
    $sql = "SELECT id, km_final, status, carro, data_fim FROM reservas WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$atual) {
        error_log("Reserva não encontrada para id: $id");
        exit("Reserva não encontrada.");
    }
    error_log("Dados da reserva: " . print_r($atual, true));

    // Atualizar a reserva atual: definir km_final e mudar status para 'fechada'
    $sql = "UPDATE reservas SET km_final = :km_final, status = 'fechada' WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':km_final', $km_final, PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        error_log("Falha ao atualizar km_final da reserva id: " . $id);
    } else {
        error_log("Reserva id " . $id . " atualizada com km_final: " . $km_final);
        
        // Só atualiza as reservas confirmadas se a atualização da reserva atual foi bem-sucedida
        $sql = "UPDATE reservas SET km_inicial = :km_final WHERE status = 'confirmado' AND carro = :carro";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':carro', $atual['carro'], PDO::PARAM_STR);
        $stmt->bindParam(':km_final', $km_final, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            error_log("Falha ao atualizar km_inicial das reservas confirmadas para carro: " . $atual['carro']);
        } else {
            error_log("Reservas confirmadas atualizadas com km_inicial: " . $km_final);
        }
    }

    // Atualizar o km_atual do carro com base no km_final informado
    $sql = "UPDATE carros SET km_atual = :km_final WHERE placa = :carro";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':carro', $atual['carro'], PDO::PARAM_STR);
    $stmt->bindParam(':km_final', $km_final, PDO::PARAM_INT);
    if (!$stmt->execute()) {
        error_log("Falha ao atualizar km_atual do carro para placa: " . $atual['carro']);
    } else {
        error_log("Carro atualizado com km_atual: " . $km_final);
    }
} else {
    error_log("Método inválido ou id não definido.");
}
?>
