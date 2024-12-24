<?php
require_once 'config/email-config.php';

$emailConfig = new EmailConfig();

$to = 'brendakunzler06@gmail.com';
$subject = 'Teste de Envio de E-mail';
$body = '<h1>Olá!</h1><p>Este é um teste de envio de e-mail usando PHPMailer.</p>';
$altBody = 'Este é um teste de envio de e-mail usando PHPMailer.';

if ($emailConfig->sendMail($to, $subject, $body, $altBody)) {
    echo "E-mail enviado com sucesso!";
} else {
    echo "Erro ao enviar o e-mail.";
}