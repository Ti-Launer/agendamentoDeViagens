<?php
require_once "../controllers/get-cars.php";
require_once '../controllers/db.php';

$database = new Database();
$pdo = $database->connect();

$whereClauses = [];
$params = [];

// Verifica se o filtro de carro foi aplicado
if (!empty($_GET['carro'])) {
    $whereClauses[] = 'carro LIKE :carro';
    $params[':carro'] = '%' . $_GET['carro'] . '%';
}

// Construção do SQL com base nos filtros
$whereSql = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

$sql = "SELECT * FROM agenda_carros $whereSql ORDER BY data_inicio";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao buscar agenda dos carros: {$e->getMessage()}</div>";
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<form id="filterForm" class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <label for="carSelect" class="form-label">Filtrar por Carro:</label>
            <select id="carSelect" name="carro" class="form-select">
                <option value="" selected>Todos</option>
                <?php
                $carros = fetchCars();
                foreach ($carros as $carro): ?>
                    <option value="<?= htmlspecialchars($carro['placa']) ?>" <?= isset($_GET['carro']) && $_GET['carro'] == $carro['placa'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($carro['modelo'] . ' - ' . $carro['placa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>Carro</th>
            <th>Nome</th>
            <th>Motivo</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Data Confirmação</th>
        </tr>
    </thead>
    <tbody id="agendaTableBody">
        <?php foreach ($agenda as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['carro']) ?></td>
                <td><?= htmlspecialchars($item['nome']) ?></td>
                <td><?= htmlspecialchars($item['destino_motivo']) ?></td>
                <td><?= htmlspecialchars($item['data_inicio']) ?></td>
                <td><?= htmlspecialchars($item['data_fim']) ?></td>
                <td><?= htmlspecialchars($item['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>