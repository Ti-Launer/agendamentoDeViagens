<?php
require_once '../../config/email-config.php';
require_once '../controllers/get-active-admins.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['new_km_inicial'])) {
    // Captura e sanitiza os parâmetros
    $id = htmlspecialchars($_POST['id']);
    $new_km_inicial = htmlspecialchars($_POST['new_km_inicial']);

    $emailConfig = new EmailConfig();

    // Envia email para admins
    fetchActiveAdmins();
    foreach ($adminEmails as $adminEmail) {
        $subjectAdmin = "Alerta: Discrepância em KM Inicial na Reserva #$id";
        $bodyAdmin = "
            <p>Foi relatada uma discrepância no KM Inicial para a reserva #$id.</p>
            <p>Novo KM Inicial informado: <strong>$new_km_inicial</strong></p>
            <p>Favor verificar a situação e proceder com as devidas correções.</p>
        ";
        $altBodyAdmin = "Reserva de $nome! Há uma reserva esperando para ser aprovada.
            Verifique no link: 192.168.0.201:50/agendamentoDeViagens/admin/dashboard.php).";

        if (!($emailConfig->sendMail($adminEmail, $subjectAdmin, $bodyAdmin, $altBodyAdmin))) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar e-mail para o cliente.']);
            exit();
        }
    }
}
