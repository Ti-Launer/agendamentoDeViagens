<?php
require_once 'db.php';
require_once __DIR__ . '/../../config/email-config.php';  // Ajuste o caminho conforme sua estrutura

function getCurrentCar($carro) {
    $database = new Database();
    $pdo = $database->connect();
    $sql = "SELECT km_atual FROM carros WHERE placa = :carro";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':carro', $carro, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}

function confirmarReserva($id, $carro) {
    $database = new Database();
    $pdo = $database->connect();

    try {
        if (empty($id) || empty($carro)) {
            throw new Exception("ID da reserva e carro são obrigatórios.");
        }

        $carData = getCurrentCar($carro);
        if (!$carData || !isset($carData['km_atual'])) {
            throw new Exception("Não foi possível obter a quilometragem atual do carro.");
        }
        $km_atual = $carData['km_atual'];

        $sql = "UPDATE reservas SET status = 'confirmado', carro = :carro, km_inicial = :km_atual WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':carro', $carro, PDO::PARAM_STR);
        $stmt->bindValue(':km_atual', $km_atual, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar a reserva.");
        }
        
        // Buscar reserva atualizada
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
            $carroInfo = $stmtGetCar->fetch(PDO::FETCH_ASSOC);

            if ($carroInfo) {
                $placaCarro = $carroInfo['placa'];
                $modeloCarro = $carroInfo['modelo'];

                $emailConfig = new EmailConfig();
                // ENVIO DE EMAIL CLIENTE
                $subjectConfirmation = "Reserva aprovada e confirmada!";
                $bodyConfirmation = "<h1>Olá de novo, $nome!</h1><p>Sua reserva para destino/motivo <strong>\"$destino\"</strong> está confirmada para os seguintes dias/horários:<br>
                <strong>Início:</strong> $dataInicio<br>
                <strong>Fim:</strong> $dataFim</p>
                <h3><strong>UTILZAR CARRO:</strong> $placaCarro - $modeloCarro.</h3>
                <p>Você poderá verificar sua reserva aqui <a href=''>aqui</a>, e lembre de conferir se a quilometragem está correta!</p>";
                $altBodyConfirmation = "Olá de novo, $nome! Sua reserva está confirmada.";

                if (!($emailConfig->sendMail($email, $subjectConfirmation, $bodyConfirmation, $altBodyConfirmation))) {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar e-mail.']);
                    exit();
                }
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Reserva confirmada. Agenda do carro atualizada.']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao confirmar reserva: ' . $e->getMessage()]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$carro = $data['carro'] ?? null;

if ($id && $carro) {
    confirmarReserva($id, $carro);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID da reserva ou carro não fornecido.']);
    exit();
}
