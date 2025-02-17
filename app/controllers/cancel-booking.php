<?php
session_start(); 
require_once 'db.php';
require_once '../../config/email-config.php';
function cancelarReserva($id, $mensagemCancelamento)
{
    $database = new Database();
    $pdo = $database->connect();

    try {
        if (empty($id)) {
            throw new Exception("ID da reserva obrigatório.");
        }

        $sql = "UPDATE reservas SET status = 'cancelado' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $sqlEmail = "SELECT nome, email FROM reservas WHERE id = :id";
            $stmtEmail = $pdo->prepare($sqlEmail);
            $stmtEmail->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtEmail->execute();
            $reserva = $stmtEmail->fetch(PDO::FETCH_ASSOC);

            if ($reserva) {
                $nome = $reserva['nome'];
                $email = $reserva['email'];

                $emailConfig = new EmailConfig();
                // ENVIO DE EMAIL CLIENTE
                $subject = "Reserva cancelada";
                $body = "<h1>Olá, $nome!</h1><p>$mensagemCancelamento</p>";
                $altBody = "Olá, $nome! $mensagemCancelamento";

                if (!($emailConfig->sendMail($email, $subject, $body, $altBody))) {
                    echo "Erro ao enviar e-mail.";
                }
            }

            $type = "Cancelou a reserva #$id";
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

        return ['status' => 'success', 'message' => 'Reserva cancelada.'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Erro ao cancelar reserva: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$mensagemCancelamento = $data['mensagemCancelamento'] ?? null;

if ($id && $mensagemCancelamento) {
    $result = cancelarReserva($id, $mensagemCancelamento);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID da reserva não fornecido.']);
}
