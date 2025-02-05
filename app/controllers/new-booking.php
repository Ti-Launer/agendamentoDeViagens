<?php
require_once 'db.php';
require_once '../../config/email-config.php';
require_once 'get-active-admins.php';

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
              
            $emailConfig = new EmailConfig();
            // ENVIO DE EMAIL CLIENTE

            $subject = "Reserva pendente de aprovação";
            $body = "<h1>Olá, $nome!</h1><p>Sua reserva está esperando para ser aprovada por alguém responsável.</p>
            <p>Você receberá um e-mail como este quando ela for atualizada, portanto, fique atento!.</p>";
            $altBody = "Olá, $nome! Sua reserva está esperando para ser aprovada por alguém responsável.
            Você receberá um e-mail quando ela for atualizada, portanto, fique atento!.";

            if (!($emailConfig->sendMail($email, $subject, $body, $altBody))) {
                echo "Erro ao enviar e-mail.";
            }

            // ENVIO DE EMAIL ADMIN
            fetchActiveAdmins();
            foreach ($adminEmails as $adminEmail) {
                $subjectAdmin = "Nova reserva pendente de aprovação";
                $bodyAdmin = "<h1>Reserva de $nome!</h1><p>Há uma reserva esperando para ser aprovada.</p>
                    <p>Verifique no link <a href='localhost/agendamentoDeViagens/admin/dashboard.php'>AQUI</a>.</p>";
                $altBodyAdmin = "Reserva de $nome! Há uma reserva esperando para ser aprovada.
                    Verifique no link: 192.168.0.201:50/agendamentoDeViagens/admin/dashboard.php).";

                if (!($emailConfig->sendMail($adminEmail, $subjectAdmin, $bodyAdmin, $altBodyAdmin))) {
                    echo "Erro ao enviar e-mail para administradores.";
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao criar a reserva.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
}