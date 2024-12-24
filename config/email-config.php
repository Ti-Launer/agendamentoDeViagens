<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ ."/../vendor/autoload.php";

class EmailConfig {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
            try {
                $this->mailer->isSMTP();
                $this->mailer->Host = 'mail-ssl.m9.network';
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = 'ti@launer.com.br';
                $this->mailer->Password = 'LAUR2019*sjh';
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->mailer->Port = 587;

                $this->mailer->setFrom('ti@launer.com.br', 'TI Launer');
                $this->mailer->isHTML(true);
                $this->mailer->CharSet = 'UTF-8';
            } catch (Exception $e) {
                error_log("Erro ao configurar Servidor de Email: " . $e->getMessage());
                throw new Exception("Não foi possível configurar o serviço de e-mail.");
            }
        }
    public function sendMail($to, $subject, $body, $altBody = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);

            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
}