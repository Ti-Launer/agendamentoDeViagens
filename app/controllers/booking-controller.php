<?php
require_once 'db.php';

function getBooking() {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    
        // Validar o 'id' para garantir que é um número inteiro
        $id = filter_var($id, FILTER_VALIDATE_INT);
    
        if ($id === false) {
            die("ID inválido.");
        }
    } else {
        die("ID não fornecido.");
    }

    $database = new Database();
    $conn = $database->connect();

    $sql = "SELECT id, nome, km_final, km_inicial, status, carro, data_inicio, data_fim FROM reservas WHERE id = :id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}