<?php
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: /agendamentoDeViagens/login.php');
    exit();
}