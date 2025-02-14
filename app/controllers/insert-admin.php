<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['adminName']);
    $email = htmlspecialchars($_POST['adminEmail']);
    $senha = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);
    $forcaSenha = isset($_POST['forcePasswordChange']) ? 'yes' : 'no';

    $username = strtolower(str_replace(' ', '-', $nome));

    try {
        $sql = 'SELECT COUNT(*) FROM admins WHERE username = :username';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $usernameExists = $stmt->fetchColumn();

        if ($usernameExists) {
            $counter = 1;
            $newUsername = $username . '-' . $counter;

            while ($usernameExists) {
                $newUsername = $username . '-' . ++$counter;
                $stmt->bindParam(':username', $newUsername);
                $stmt->execute();
                $usernameExists = $stmt->fetchColumn();
            }
            $username = $newUsername;
        }

        $sql = 'INSERT INTO admins (nome, username, email, senha, forca_senha) VALUES (:nome, :username, :email, :senha, :forca_senha)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':forca_senha', $forcaSenha);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        header('Content-Type: text/plain');
        echo "Administrador adicionado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao adicionar administrador" . $e->getMessage();
    }
}
