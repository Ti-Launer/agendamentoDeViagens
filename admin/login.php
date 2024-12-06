<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
            <div class="card-header text-center bg-primary text-white">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="nome_usuario" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha_usuario" class="form-label">E-mail</label>
                        <input type="password" class="form-control" id="senha_usuario" name="senha_usuario" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>