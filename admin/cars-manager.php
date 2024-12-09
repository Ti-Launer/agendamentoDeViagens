<?php
include "../app/models/header.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Carros</h2>

        <div class="text-center mt-4 mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                Adicionar Outro Carro
            </button>
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="showInactiveCars" name="showInactiveCars" onchange="toggleInactiveCars()">
            <label class="form-check-label" for="showInactiveCars">
                Visualizar Carros Inativos
            </label>
        </div>
        <script>
            function toggleInactiveCars() {
                const isChecked = document.getElementById('showInactiveCars').checked;
                const header = document.getElementById('headerToggle');
                const cells = document.querySelectorAll('.cellToggle');
                const example = document.getElementById('example');

                example.style.display = isChecked ? 'table-row' : 'none';
                header.style.display = isChecked ? 'table-cell' : 'none';
                cells.forEach(cell => {
                    cell.style.display = isChecked ? 'table-cell' : 'none';
                });
            }
        </script>
        <div class="table-responsive">
            <table class="table table-dark table-hover table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Modelo</th>
                        <th>Placa</th>
                        <th>Particularidade</th>
                        <th>Condição</th>
                        <th>Atualizar</th>
                        <th id="headerToggle" style="display: none;">Ativar/Desativar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Saveiro</td>
                        <td>ABC1D23</td>
                        <td>Branco</td>
                        <td><span class="badge bg-success">Pode Viajar</span></td>
                        <td>
                            <button class="btn btn-danger btn-sm" title="Colocar carro em Manutenção (não pode usar para viagens).">Manutenção</button>
                        </td>
                        <td class="cellToggle" style="display: none;">
                            <button class="btn btn-danger btn-sm">Desativar</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Polo</td>
                        <td>EFG4H56</td>
                        <td>Preto</td>
                        <td><span class="badge bg-danger">Em Manutenção</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">Liberar</button>
                        </td>
                        <td class="cellToggle" style="display: none;">
                            <button class="btn btn-danger btn-sm" title="Desativa o carro para uso completamente.">Desativar</button>
                        </td>
                    </tr>
                    <tr id="example" style="display: none;">
                        <td>3</td>
                        <td>Saveiro</td>
                        <td>ABC1D23</td>
                        <td>Branco</td>
                        <td><span class="badge bg-success">Pode Viajar</span></td>
                        <td>
                            <button class="btn btn-danger btn-sm" title="Colocar carro em Manutenção (não pode usar para viagens).">Manutenção</button>
                        </td>
                        <td class="cellToggle" style="display: none;">
                            <button class="btn btn-danger btn-sm">Ativar</button>
                        </td>
                    </tr>
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
                            <label for="carPlate" class="form-label">E-mail</label>
                            <input type="text" class="form-control" id="carPlate" name="carPlate" placeholder="Ex: ABC1D23" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="carParticularity" class="form-label">Senha</label>
                            <input type="text" class="form-control" id="carParticularity" name="carParticularity" placeholder="Ex: Carro do João" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="addAdminForm" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>