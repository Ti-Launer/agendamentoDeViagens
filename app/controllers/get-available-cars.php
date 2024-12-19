<?php
require_once "db.php";

function getAvailableCars($tipoCarro, $dataInicio, $dataFim) {

    $database = new Database();
    $pdo = $database->connect();

    try {
        $sql = "
            SELECT c.*
            FROM carros c
            WHERE c.tipo_carro = :tipo_carro
                AND c.ativo = 1                    -- Verifica se o carro está ativo
                AND c.condicao = 'boa'      -- Verifica a condição do carro
                AND c.placa NOT IN (               -- Verifica se a placa não está agendada no período
                    SELECT a.carro
                    FROM agenda_carros a
                    WHERE (a.data_inicio < :data_fim AND a.data_fim > :data_inicio)
                );

        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tipo_carro', $tipoCarro, PDO::PARAM_STR);
        $stmt->bindValue(':data_inicio', $dataInicio, PDO::PARAM_STR);
        $stmt->bindValue(':data_fim', $dataFim, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar carros disponíveis: " . $e->getMessage();
        return [];
    }
}

