<?php
require_once 'db.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors',1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['adminUser']);
    $password = htmlspecialchars($_POST['adminPassword']);

    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = 'SELECT id, nome, email, senha, ativo, forca_senha FROM admins WHERE username = :username';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['senha'])) {
            if ($admin['ativo'] === 'yes') {
                if ($admin['forca_senha'] === 'yes') {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['nome'];
                    $_SESSION['admin_email'] = $admin['email'];

                    header('Location: /agendamentoDeViagens/admin/change-passwd.php');
                    exit();
                } else {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['nome'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['is_master'] = $admin['master'];

                    header('Location: /agendamentoDeViagens/admin/dashboard.php');
                    exit();
                }
            } else {
                echo 'Seu usuário está desativado. Entre em contato com a TI.';
            }
        } else {
            echo 'Nome de usuário ou senha incorretos.';
        } 
    } catch (PDOException $e) {	
        echo 'Erro ao conectar com o banco de dados'. $e->getMessage();
    }
} else {
    echo 'Algo deu Errado, contate a TI.';
}