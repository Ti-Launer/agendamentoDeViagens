<?php
require_once 'db.php';
require_once '../../config/email-config.php';
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
        if ($stmt->execute()) {
            $sqlEmail = "SELECT * FROM reservas WHERE id = :id";
            $stmtEmail = $pdo->prepare($sqlEmail);
            $stmtEmail->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtEmail->execute();
            $reserva = $stmtEmail->fetch(PDO::FETCH_ASSOC);
            
            if ($reserva) {
                $nome = $reserva['nome'];
                $email = $reserva['email'];
                $placa = $reserva['carro'];
                $dataInicio = $reserva['data_inicio'];
                $dataFim = $reserva['data_fim'];
                $destino = $reserva['destino_motivo'];

                $sqlGetCar = 'SELECT modelo, placa FROM carros WHERE placa = :placa';
                $stmtGetCar = $pdo->prepare($sqlGetCar);
                $stmtGetCar->bindValue(':placa', $placa, PDO::PARAM_STR);
                $stmtGetCar->execute();
                $carro = $stmtGetCar->fetch(PDO::FETCH_ASSOC);

                if ($carro) {
                    $placaCarro = $carro['placa'];
                    $modeloCarro = $carro['modelo'];

                    $emailConfig = new EmailConfig();
                    // ENVIO DE EMAIL CLIENTE

                    $subjectConfirmation = "Reserva aprovada e confirmada!";
                    $bodyConfirmation = "<h1>Olá de novo, $nome!</h1><p>Sua reserva para destino/motivo <strong>\"$destino\"</strong> está confirmada para os seguintes dias/horários:<br>
                    <strong>Início:</strong> $dataInicio<br>
                    <strong>Fim:</strong> $dataFim</p>
                    <h3><strong>UTILZAR CARRO:</strong> $placaCarro - $modeloCarro.</h3>
                    <p>Você poderá verificar sua reserva aqui <a href=''>aqui</a>, e lembre de conferir se a quilometragem está correta!</p>";
                    $altBodyConfirmation = "Olá de novo, $nome! Sua reserva está esperando para ser aprovada por alguém responsável.
                    Você receberá um e-mail quando ela for atualizada.";

                    if (!($emailConfig->sendMail($email, $subjectConfirmation, $bodyConfirmation, $altBodyConfirmation))) {
                        echo "Erro ao enviar e-mail.";
                    }
                }
            }
        }
        

        return ['status' => 'success', 'message' => 'Reserva confirmada. Agenda do carro atualizada.'];
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
