<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = htmlspecialchars($_POST['newPassword']);
    $confirmPassword = htmlspecialchars($_POST['confirmPassword']);
    $adminId = $_SESSION['admin_id'];

    if ($newPassword !== $confirmPassword) {
        echo "As senhas não coincidem. Tente novamente.";
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = 'UPDATE admins SET senha = :senha, forca_senha = "no" WHERE id = :adminId';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':senha', $hashedPassword);
        $stmt->bindParam(':adminId', $adminId);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Senha alterada com sucesso!";
            header('Location: /agendamentoDeViagens/admin/dashboard.php');
        } else {
            echo "Nenhuma alteração foi feita. Verifique seus dados.";
        }
        exit();

    } catch (PDOException $e) {
        echo "Erro ao atualizar senha: " . $e->getMessage();
    }
} else {
    echo "Houve algum erro, contate a TI.";
}
