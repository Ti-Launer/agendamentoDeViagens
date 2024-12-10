<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $modelo = htmlspecialchars($_POST['carModel']);
    $placa = htmlspecialchars($_POST['carPlate']);
    $detalhe = htmlspecialchars($_POST['carDetail']);

    try {
        $sql = 'INSERT INTO carros (modelo, placa, detalhe) VALUES (:modelo, :placa, :detalhe)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':detalhe', $detalhe);

        $stmt->execute();
        header('Content-Type: text/plain');
        echo "Novo Carro adicionado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao adicionar Novo Carro". $e->getMessage();
    }
}