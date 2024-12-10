<?php
include "../app/models/header.php";

require_once "../app/controllers/get-admins.php";
require_once "../app/controllers/insert-admin.php";

$admins = fetchAdmins();
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
        <h2 class="text-center mb-4">Gerenciar Administradores</h2>

        <div class="text-center mt-4 mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                Adicionar Novo Administrador
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= $admin['id']; ?></td>
                            <td><?= htmlspecialchars($admin['nome']); ?></td>
                            <td><?= htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <span class="badge <?= $admin['ativo'] === 'yes' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $admin['ativo'] === 'yes' ? 'Ativo' : 'Inativo'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($admin['ativo'] === 'yes'): ?>
                                    <button class="btn btn-warning btn-sm toggle-status" data-id="<?= $admin['id']; ?>" data-status="no">
                                        Desativar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success btn-sm toggle-status" data-id="<?= $admin['id']; ?>" data-status="yes">
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

    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="addAdminModalLabel">Adicionar Novo Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm">
                        <div class="mb-3">
                            <label for="adminName" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="adminName" name="adminName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="adminEmail" name="adminEmail" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="forcePasswordChange" name="forcePasswordChange">
                            <label class="form-check-label" for="forcePasswordChange">
                                Exigir troca de senha no primeiro login
                            </label>
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

    <script>
        document.getElementById('addAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('../app/controllers/insert-admin.php', {
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
                    const modalElement = document.getElementById('addAdminModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    document.getElementById('addAdminForm').reset();
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro ao enviar dados: ', error);
                alert('Erro ao adicionar administrador. Verifique o console.');
            });
        });

        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.getAttribute('data-id');
                const newStatus = this.getAttribute('data-status');

                if (confirm(`Tem certeza que deseja ${newStatus === 'no' ? 'desativar' : 'ativar'} este administrador?`)) {
                    fetch('../app/controllers/toggle-admin-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            adminId: adminId,
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
                    
                }
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>