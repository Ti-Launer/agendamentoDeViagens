<!DOCTYPE html>
<html lang="en">
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
                    <tr>
                        <td>1</td>
                        <td>João Silva</td>
                        <td>joao.silva@exemplo.com</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm">Desativar</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Maria Souza</td>
                        <td>maria.souza@exemplo.com</td>
                        <td><span class="badge bg-danger">Inativo</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">Ativar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
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
                    <button type="submit" form="addAdminForm" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>