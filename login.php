<?php

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
            <div class="card-header text-center bg-dark text-white">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                <form action="app/controllers/login-process.php" method="POST">
                    <div class="mb-3">
                        <label for="adminUser" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="adminUser" name="adminUser" required>
                    </div>

                    <div class="mb-3">
                        <label for="adminPassword" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>