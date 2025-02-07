<?php
require_once '../controllers/db.php';

$database = new Database();
$pdo = $database->connect();

$whereClauses = [];
$params = [];

// Verifica filtros passados via GET
if (!empty($_GET['filter_id'])) {
    $whereClauses[] = 'id = :id';
    $params[':id'] = $_GET['filter_id'];
}

if (!empty($_GET['filter_month'])) {
    $whereClauses[] = 'MONTH(data_inicio) = :month';
    $params[':month'] = $_GET['filter_month'];
}

if (!empty($_GET['name_email'])) {
    $whereClauses[] = '(nome LIKE :name_email OR email LIKE :name_email)';
    $params[':name_email'] = '%' . $_GET['name_email'] . '%';
}

if (!empty($_GET['filter_tipo_viagem'])) {
    $whereClauses[] = 'tipo_reserva = :tipo_viagem';
    $params[':tipo_viagem'] = $_GET['filter_tipo_viagem'];
}

if (!empty($_GET['filter_status'])) {
    $whereClauses[] = 'status = :status';
    $params[':status'] = $_GET['filter_status'];
}

if (!empty($_GET['filter_tipo_carro'])) {
    $whereClauses[] = 'tipo_carro = :tipo_carro';
    $params[':tipo_carro'] = $_GET['filter_tipo_carro'];
}

$whereSql = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$sql = "SELECT * FROM reservas $whereSql ORDER BY data_inicio DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao buscar histórico de reservas: {$e->getMessage()}</div>";
    exit;
}
?>

<form id="filterForm">
    <div class="row">
        <div class="col-md-3">
            <label for="filter_id">ID</label>
            <input type="number" name="filter_id" id="filter_id" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="filter_month">Mês</label>
            <select name="filter_month" id="filter_month" class="form-select">
                <option value="">Todos</option>
                <option value="1">Janeiro</option>
                <option value="2">Fevereiro</option>
                <option value="3">Março</option>
                <option value="4">Abril</option>
                <option value="5">Maio</option>
                <option value="6">Junho</option>
                <option value="7">Julho</option>
                <option value="8">Agosto</option>
                <option value="9">Setembro</option>
                <option value="10">Outubro</option>
                <option value="11">Novembro</option>
                <option value="12">Dezembro</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="name_email">Nome/Email</label>
            <input type="text" name="name_email" id="name_email" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="filter_tipo_viagem">Tipo de Viagem</label>
            <select name="filter_tipo_viagem" id="filter_tipo_viagem" class="form-select">
                <option value="">Todos</option>
                <option value="curta">Curta</option>
                <option value="longa">Longa</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter_status">Status</label>
            <select name="filter_status" id="filter_status" class="form-select">
                <option value="">Qualquer</option>
                <option value="confirmado">Confirmado</option>
                <option value="pendente">Pendente</option>
                <option value="fechada">Fechado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter_tipo_carro">Tipo de Carro</label>
            <select name="filter_tipo_carro" id="filter_tipo_carro" class="form-select">
                <option value="">Todos</option>
                <option value="carga">Carga</option>
                <option value="passeio">Passeio</option>
                <option value="indiferente">Indiferente</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Aplicar Filtros</button>
</form>

<table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo de Viagem</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Tipo de Carro</th>
            <th>Carro</th>
            <th>Motivo/Destino</th>
            <th>Status</th>
            <th>KM Inicial</th>
            <th>KM Final</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservas as $reserva): ?>
            <tr id="reservaRow<?= htmlspecialchars($reserva['id']) ?>">
                <td><?= htmlspecialchars($reserva['id']) ?></td>
                <td><?= htmlspecialchars($reserva['nome']) ?></td>
                <td><?= htmlspecialchars($reserva['email']) ?></td>
                <td><?= htmlspecialchars($reserva['tipo_reserva'] === 'curta' ? 'Curta' : 'Longa') ?></td>
                <td><?= htmlspecialchars($reserva['data_inicio']) ?></td>
                <td><?= htmlspecialchars($reserva['data_fim']) ?></td>
                <td><?= htmlspecialchars($reserva['tipo_carro']) ?></td>
                <td><?= htmlspecialchars($reserva['carro'] ?? 'Não designado') ?></td>
                <td><?= htmlspecialchars($reserva['destino_motivo']) ?></td>
                <td><?= htmlspecialchars($reserva['status']) ?></td>
                <td><?= htmlspecialchars($reserva['km_inicial']) ?></td>
                <td><?= htmlspecialchars($reserva['km_final']) ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary edit-km" data-reserva-id="<?= htmlspecialchars($reserva['id']) ?>" title="Editar KM Final">
                        Editar KM
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal para editar KM Final -->
<div class="modal fade" id="editKmModal" tabindex="-1" aria-labelledby="editKmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="editKmModalLabel">Editar KM Final</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="editKmForm">
          <div class="mb-3">
            <label for="newKmFinal" class="form-label">Novo KM Final</label>
            <input type="number" class="form-control" id="newKmFinal" name="newKmFinal" required>
          </div>
          <input type="hidden" id="reservaId" name="reservaId">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="saveKmBtn">Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inicializa o modal de edição
    const editKmModal = new bootstrap.Modal(document.getElementById("editKmModal"));
    const saveKmBtn = document.getElementById("saveKmBtn");

    // Adiciona listener a todos os botões de editar KM
    document.querySelectorAll(".edit-km").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const reservaId = this.getAttribute("data-reserva-id");
            // Preenche o campo hidden com o ID da reserva
            document.getElementById("reservaId").value = reservaId;
            // Opcional: pode também preencher o input com o valor atual (se quiser editar a partir do valor atual)
            const kmFinalElement = document.getElementById("kmFinal" + reservaId);
            document.getElementById("newKmFinal").value = kmFinalElement ? kmFinalElement.textContent.trim() : "";
            editKmModal.show();
        });
    });

    // Quando o botão "Salvar" do modal for clicado
    saveKmBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const newKmFinal = document.getElementById("newKmFinal").value.trim();
        const reservaId = document.getElementById("reservaId").value;

        if (!newKmFinal || isNaN(newKmFinal)) {
            alert("Por favor, insira um valor numérico válido.");
            return;
        }

        // Envia a atualização via AJAX para update-km.php
        fetch("app/controllers/update-km_final-km_inicial.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                id: reservaId,
                new_km_final: newKmFinal
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erro na resposta do servidor");
            }
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                alert("KM Final atualizado com sucesso!");
                // Atualiza o valor na tabela sem recarregar a página
                const kmFinalElement = document.getElementById("kmFinal" + reservaId);
                if (kmFinalElement) {
                    kmFinalElement.textContent = newKmFinal;
                }
                editKmModal.hide();
                // Se houver lógica para atualizar a próxima reserva, você pode chamar uma função aqui
                atualizarProximaReserva(reservaId, newKmFinal);
            } else {
                alert("Erro ao atualizar: " + data.message);
            }
        })
        .catch(error => {
            console.error("Erro:", error);
            alert("Erro ao atualizar o KM Final. Verifique o console.");
        });
    });
});
</script>
