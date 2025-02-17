<?php
require_once "../app/controllers/get-cars.php";
require_once '../app/controllers/db.php';
require_once 'session_verify.php';
include "../app/models/header.php";

$cars = fetchCars();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento De Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .display-none {
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Carros</h2>

        <div class="text-center mt-4 mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                Adicionar Outro Carro
            </button>
        </div>
        <?php if (isset($_SESSION['is_master']) && $_SESSION['is_master'] === 'yes'): ?>
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="showInactiveCars" name="showInactiveCars" onchange="toggleInactiveCars()">
                <label class="form-check-label" for="showInactiveCars">
                    Visualizar Carros Inativos/Desativar Carros
                </label>
            </div>
        <?php endif; ?>
        <script>
            function toggleInactiveCars() {
                const isChecked = document.getElementById('showInactiveCars').checked;
                const header = document.getElementById('inactive-cars-header');
                const cells = document.querySelectorAll('.inactive-cars-cell');
                const inactiveCars = document.querySelectorAll('.inactive-car');

                header.style.display = isChecked ? 'table-cell' : 'none';
                inactiveCars.forEach(car => {
                    car.style.display = isChecked ? 'table-row' : 'none';
                });
                cells.forEach(cell => {
                    cell.style.display = isChecked ? 'table-cell' : 'none';
                });
            }
        </script>
        <div class="table-responsive">
            <table class="table table-light table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Modelo</th>
                        <th>Placa</th>
                        <th>Tipo do Carro</th>
                        <th>Particularidade</th>
                        <th>Condição</th>
                        <th>Quilometragem</th>
                        <th>Atualizar</th>
                        <th id="inactive-cars-header" style="display: none;">Ativar/Desativar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr class="<?= $car['ativo'] == 0 ? 'inactive-car display-none' : ''; ?>">
                            <td><?= htmlspecialchars($car['modelo']); ?></td>
                            <td><?= htmlspecialchars($car['placa']); ?></td>
                            <td>
                                <span class="badge <?= $car['tipo_carro'] === 'carga' ? 'bg-primary' : 'bg-info'; ?>">
                                    <?= $car['tipo_carro'] === 'carga' ? 'Carga' : 'Passeio'; ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($car['detalhe']); ?></td>
                            <td>
                                <span class="badge 
                                    <?= $car['condicao'] === 'boa' ? 'bg-success' : 'bg-warning text-black'; ?>">
                                    <?= $car['condicao'] === 'boa' ? 'Normal' : 'Manutenção'; ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($car['km_atual']); ?></td>
                            <td>
                                <?php if ($car['condicao'] === 'boa'): ?>
                                    <button class="btn btn-warning btn-sm toggle-status" data-id="<?= $car['placa']; ?>" data-status="manutencao" title="Marcar carro sob manutenção (impede pessoas de agendá-lo).">
                                        Manutenção
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-sm toggle-status" data-id="<?= $car['placa']; ?>" data-status="boa" title="Liberar o carro para uso.">
                                        Liberar
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td class="inactive-cars-cell display-none">
                                <?php if ($car['ativo'] == 1): ?>
                                    <button class="btn btn-danger btn-sm toggle-usage" data-id="<?= $car['placa']; ?>" data-usage="0">
                                        Desativar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-sm toggle-usage" data-id="<?= $car['placa']; ?>" data-usage="1">
                                        Ativar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="addCarModalForm">Adicionar Outro Carro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCarForm">
                        <div class="mb-3">
                            <label for="carModel" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="carModel" name="carModel" placeholder="Ex: Saveiro" required>
                        </div>

                        <div class="mb-3">
                            <label for="carPlate" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="carPlate" name="carPlate" placeholder="Ex: ABC1D23" required>
                        </div>

                        <div class="mb-3">
                            <label for="carType" class="form-label">Tipo de Aplicação</label>
                            <select class="form-select" id="carType" name="carType" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="carga" data-bs-toggle="tooltip">Carga</option>
                                <option value="passeio" data-bs-toggle="tooltip">Passeio</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="carDetail" class="form-label">Particularidade</label>
                            <input type="text" class="form-control" id="carDetail" name="carDetail" placeholder="Ex: Carro do João" required>
                        </div>

                        <div class="mb-3">
                            <label for="carKM" class="form-label">Quilometragem</label>
                            <input type="number" class="form-control" id="carKM" name="carKM" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="addCarForm" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addCarForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('../app/controllers/insert-car.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor')
                    }
                    return response.text();
                })
                .then(data => {
                    alert(data);
                    if (data.includes('sucesso')) {
                        const modalElement = document.getElementById('addCarModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                        document.getElementById('addCarForm').reset();
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erro ao enviar dados: ', error);
                    alert('Erro ao adicionar um Novo Carro. Verifique o console ou contate a TI.');
                });
        });

        // Toggle Status
        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function() {
                const carId = this.getAttribute('data-id');
                const newStatus = this.getAttribute('data-status');

                fetch('../app/controllers/toggle-car-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            carId: carId,
                            newStatus: newStatus
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar status: ', error);
                        alert('Erro ao atualizar status. Verifique o console.')
                    });

            });
        });

        // Toggle Usage
        document.querySelectorAll('.toggle-usage').forEach(button => {
            button.addEventListener('click', function() {
                const carId = this.getAttribute('data-id');
                const usage = this.getAttribute('data-usage');

                fetch('../app/controllers/toggle-car-usage.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            carId: carId,
                            usage: usage
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar status: ', error);
                        alert('Erro ao atualizar status. Verifique o console.')
                    });

            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>