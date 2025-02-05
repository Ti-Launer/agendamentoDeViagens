<?php
require_once 'app/controllers/booking-controller.php';

$reserva = getBooking();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva <?= $reserva['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Reserva #<?= $reserva['id'] ?></h3>
            </div>
            
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($reserva['nome']) ?></dd>

                    <dt class="col-sm-3">Carro:</dt>
                    <dd class="col-sm-9"><?= $reserva['carro'] ?></dd>

                    <dt class="col-sm-3">Período:</dt>
                    <dd class="col-sm-9">
                        <?= htmlspecialchars($reserva['data_inicio']) ?> 
                        a 
                        <?= htmlspecialchars($reserva['data_fim']) ?>
                    </dd>
                </dl>

                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">KM Inicial</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="km_inicial"
                                   value="<?= $reserva['km_inicial'] ?: $reserva['km_final'] ?>"
                                   <?= $reserva['km_inicial'] ? 'readonly' : '' ?>>
                        </div>
                        
                        <?php if (!$reserva['km_final']): ?>
                        <div class="col-md-6">
                            <label class="form-label">KM Final</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="km_final"
                                   min="<?= $reserva['km_inicial'] ?>"
                                   required>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$reserva['km_final']): ?>
                    <button type="submit" class="btn btn-danger">
                        <?= $reserva['km_final'] ? 'Finalizar Reserva' : 'KM Inicial Errado' ?>
                    </button>
                    <?php endif; ?>
                </form>

                <?php if ($reserva['status'] === 'fechado'): ?>
                <div class="alert alert-warning mt-3">
                    Reserva finalizada. Contate o administrador para ajustes.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>