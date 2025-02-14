<?php
require_once "db.php";

function getAvailableCars($tipoCarro, $dataInicio, $dataFim)
{
    $database = new Database();
    $pdo = $database->connect();

    try {
        // Base da query, sem o filtro de tipo_carro
        $sql = "
            SELECT c.*
            FROM carros c
            WHERE c.ativo = 1                    -- Carro ativo
              AND c.condicao = 'boa'             -- Carro em boa condição
              AND c.placa NOT IN (               -- Exclui carros agendados no período
                    SELECT a.carro
                    FROM agenda_carros a
                    WHERE (a.data_inicio < :data_fim AND a.data_fim > :data_inicio)
              )
        ";

        // Se o tipo de carro não for 'indiferente', adiciona o filtro
        if ($tipoCarro !== 'indiferente') {
            $sql .= " AND c.tipo_carro = :tipo_carro";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio, PDO::PARAM_STR);
        $stmt->bindValue(':data_fim', $dataFim, PDO::PARAM_STR);

        // Só vincula o parâmetro se for necessário
        if ($tipoCarro !== 'indiferente') {
            $stmt->bindValue(':tipo_carro', $tipoCarro, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar carros disponíveis: " . $e->getMessage();
        return [];
    }
}
