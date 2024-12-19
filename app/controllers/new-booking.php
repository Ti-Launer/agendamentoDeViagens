<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $tipoReserva = htmlspecialchars($_POST['tipo_reserva']);
    $dataInicio = htmlspecialchars($_POST['data_inicio']);
    $dataFim = isset($_POST['data_fim']) ? $_POST['data_fim'] : null;
    $tipoCarro = htmlspecialchars($_POST['tipo_carro']);
    $motivo = htmlspecialchars($_POST['motivo']);

    try {
        $database = new Database();
        $pdo = $database->connect();

        if ($tipoReserva === 'curta') {
            $sql = "INSERT INTO reservas (nome, email, tipo_reserva, data_inicio, tipo_carro, destino_motivo) 
                    VALUES (:nome, :email, :tipoReserva, :dataInicio, :tipoCarro, :motivo)";
            $stmt = $pdo->prepare($sql);
        } else {
            $sql = "INSERT INTO reservas (nome, email, tipo_reserva, data_inicio, data_fim, tipo_carro, destino_motivo) 
                    VALUES (:nome, :email, :tipoReserva, :dataInicio, :dataFim, :tipoCarro, :motivo)";
            $stmt = $pdo->prepare($sql);
        }
        
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':tipoReserva', $tipoReserva);
        $stmt->bindParam(':dataInicio', $dataInicio);
        if ($tipoReserva === 'longa' ) { 
            $stmt->bindParam(':dataFim', $dataFim);
        }
        $stmt->bindParam(':tipoCarro', $tipoCarro);
        $stmt->bindParam(':motivo', $motivo);

        if ($stmt->execute()) {
            $id = $pdo->lastInsertId();
            echo json_encode([
                'status' => 'success',
                'message' => 'Reserva criada com sucesso.',
                'id' => $id,
                'nome' => $nome,
                'email' => $email,
                'tipo_reserva' => $tipoReserva,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'tipo_carro' => $tipoCarro,
                'motivo' => $motivo
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao criar a reserva.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
}