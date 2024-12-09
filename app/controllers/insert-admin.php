<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['adminName']);
    $email = htmlspecialchars($_POST['adminEmail']);
    $senha = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);
    $forcaSenha = isset($_POST['forcePasswordChange']) ? 'yes' : 'no';

    try {
        $sql = 'INSERT INTO admins (nome, email, senha, forca_senha) VALUES (:nome, :email, :senha, :forca_senha)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':forca_senha', $forcaSenha);

        $stmt->execute();

        echo "Administrador adicionado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao adicionar administrador". $e->getMessage();
    }
}