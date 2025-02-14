<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $modelo = htmlspecialchars($_POST['carModel']);
    $placa = htmlspecialchars($_POST['carPlate']);
    $tipoCarro = htmlspecialchars($_POST['carType']);
    $detalhe = htmlspecialchars($_POST['carDetail']);
    $kmAtual = htmlspecialchars($_POST['carKM']);

    try {
        $sql = 'INSERT INTO carros (modelo, placa, tipo_carro, detalhe, km_atual) VALUES (:modelo, :placa, :tipo_carro, :detalhe, :km_atual)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':tipo_carro', $tipoCarro);
        $stmt->bindParam(':detalhe', $detalhe);
        $stmt->bindParam(':km_atual', $kmAtual);

        $stmt->execute();
        header('Content-Type: text/plain');
        echo "Novo Carro adicionado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao adicionar Novo Carro" . $e->getMessage();
    }
}
