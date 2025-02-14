<?php
require_once "db.php";
function fetchPendingReservations()
{

    $database = new Database();
    $pdo = $database->connect();

    try {
        // Buscando reservas pendentes
        $sql = "SELECT * FROM reservas_pendentes";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar reservas pendentes: " . $e->getMessage();
        return [];
    }
}

$reservasPendentes = fetchPendingReservations();
