<?php
require_once '../app/controllers/get-pending-bookings.php';
require_once '../app/controllers/get-available-cars.php';
require_once '../app/controllers/db.php';
require_once 'session_verify.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?= include "../app/models/header.php"; ?>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Gerenciamento De Reservas</h2>
        <div class="row justify-content-center">
            <?php if (empty($reservasPendentes)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Nenhuma reserva pendente no momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reservasPendentes as $reserva): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($reserva['nome']) ?></h5>
                                <p class="card-text">
                                    <strong style="font-size:20px;">Viagem <?= $reserva['tipo_reserva'] === 'curta' ? 'Curta' : 'Longa' ?></strong><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($reserva['email']) ?><br>
                                    <strong>Motivo:</strong> <?= htmlspecialchars($reserva['destino_motivo']) ?><br>
                                    <strong>Data Início:</strong> <?= htmlspecialchars($reserva['data_inicio']) ?><br>
                                    <strong>Data Fim:</strong> <?= htmlspecialchars($reserva['data_fim']) ?><br>
                                    <strong>Tipo de Carro:</strong> <?= htmlspecialchars($reserva['tipo_carro']) ?><br>
                                    <strong>Carro:</strong> <?= htmlspecialchars($reserva['carro'] ?? 'Não designado') ?>
                                </p>
                                <div class="mb-3">
                                    <label for="carSelect<?= $reserva['id'] ?>" class="form-label">Selecionar Carro:</label>
                                    <select id="carSelect<?= $reserva['id'] ?>" class="form-select">
                                        <option value="" disabled selected>Selecione um carro...</option>
                                        <?php
                                        $carrosDisponiveis = getAvailableCars($reserva['tipo_carro'], $reserva['data_inicio'], $reserva['data_fim']);
                                        foreach ($carrosDisponiveis as $carro): ?>
                                            <option value="<?= $carro['placa'] ?>">
                                                <?= htmlspecialchars($carro['modelo'] . ' - ' . $carro['placa']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-success btn-sm" onclick="confirmarReserva(<?= $reserva['id'] ?>)">Confirmar</button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelarReserva(<?= $reserva['id'] ?>)">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal fade" id="modalCancelamento" tabindex="-1" aria-labelledby="modalCancelamentoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCancelamentoLabel">Mensagem de Cancelamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="mensagemCancelamento" class="form-control" rows="3" placeholder="Digite a mensagem de cancelamento..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-danger" id="confirmarCancelamento">Cancelar Reserva</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleLoading(button, isLoading) {
            if (isLoading) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processando...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText;
            }
        }

        function confirmBooking(id) {
            const carSelect = document.getElementById(`carSelect${id}`);
            const selectedCarPlaca = carSelect.value;
            const button = event.target;

            if (!selectedCarPlaca) {
                alert("Por favor, selecione um carro antes de confirmar a reserva.");
                return;
            }

            button.dataset.originalText = button.innerHTML;
            toggleLoading(button, true);

            fetch('../app/controllers/confirm-booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        carro: selectedCarPlaca
                    })
                })
                .then(response => response.json())
                .then(data => {
                    toggleLoading(button, false);
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Erro: " + data.message);
                    }
                });
        }


        function cancelarReserva(id) {
            const confirmarBtn = document.getElementById('confirmarCancelamento');
            confirmarBtn.dataset.id = id;

            const modal = new bootstrap.Modal(document.getElementById('modalCancelamento'));
            modal.show();
        }

        document.getElementById('confirmarCancelamento').addEventListener('click', function() {
            const id = this.dataset.id;
            const mensagemCancelamento = document.getElementById('mensagemCancelamento').value;

            if (!mensagemCancelamento) {
                alert("Por favor, insira uma mensagem de cancelamento.");
                return;
            }

            const button = this;
            toggleLoading(button, true);

            fetch('../app/controllers/cancel-booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        mensagemCancelamento
                    })
                })
                .then(response => response.json())
                .then(data => {
                    toggleLoading(button, false);
                    if (data.status === 'success') {
                        alert("Reserva cancelada!");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCancelamento'));
                        modal.hide();
                        location.reload(); // Recarrega a página após fechar o modal
                    } else {
                        alert("Erro ao cancelar reserva: " + data.message);
                    }
                });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>