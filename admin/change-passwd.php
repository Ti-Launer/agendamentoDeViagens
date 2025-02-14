<?php
require_once 'session_verify.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
            <div class="card-header text-center bg-dark text-white">
                <h3>Alterar Senha</h3>
            </div>
            <div class="card-body">
                <form id="changePasswordForm" action="../app/controllers/change-passwd-process.php" method="POST">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Nova Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword">
                                <span class="bi bi-eye" id="iconNewPassword"></span>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                <span class="bi bi-eye" id="iconConfirmPassword"></span>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }

        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            togglePassword('newPassword', 'iconNewPassword');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePassword('confirmPassword', 'iconConfirmPassword');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>