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
        <?php if($reserva['status'] === 'confirmado'): ?>
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
            <dt class="col-sm-3">Status:</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($reserva['status']) ?></dd>
          </dl>
  
          <form method="POST">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">KM Inicial</label>
                <input type="number" 
                       class="form-control" 
                       name="km_inicial"
                       value="<?= $reserva['km_inicial'] ?>"
                       <?= $reserva['km_inicial'] ? 'disabled' : '' ?>>
              </div>
              <div class="col-md-6">
                <label class="form-label">KM Final</label>
                <input type="number" 
                       class="form-control" 
                       name="km_final"
                       min="<?= $reserva['km_inicial'] + 5 ?>"
                       required>
              </div>
            </div>
  
            <div>
              <button style="float: left;" type="button" class="badge bg-danger" id="sendAlert" title="Relatar discrepância em KM Inicial">Relatar Discrepância Em KM Inicial</button>
              <button style="float: right;" type="button" class="btn btn-success" id="openModal">Finalizar Reserva</button>
            </div>
          </form>
  
        <?php else: ?>
          <div class="alert alert-warning mt-3 text-center">
            Reserva indisponível para visualização.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Modal para Finalizar Reserva (Confirm Modal) -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="confirmModalLabel">Confirmação</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Tem certeza de que deseja finalizar esta reserva?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="confirmSubmit">Confirmar</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal para Relatar Discrepância (Alert Modal) -->  
  <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="alertModalLabel">Relatar Discrepância de KM Inicial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="new_km_inicial" class="form-label">Novo KM Inicial:</label>
            <input type="number" class="form-control" id="new_km_inicial" name="new_km_inicial" required>
          </div>
          <p>Após confirmar, um e-mail será enviado para notificar a discrepância.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="alertSubmit">Confirmar</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector("form");
      
      // Botões do dashboard
      const finishButton = document.getElementById("openModal");
      const alertButton = document.getElementById("sendAlert");
      
      // Inicializa os modais
      const confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
      const alertModal = new bootstrap.Modal(document.getElementById("alertModal"));
      
      // Evento para Finalizar Reserva
      finishButton.addEventListener("click", function (event) {
          event.preventDefault();
          const kmFinalInput = document.querySelector("input[name='km_final']");
          const kmFinalValue = Number(kmFinalInput.value.trim());
          const kmMin = <?= $reserva['km_inicial'] ?> + 5;
  
          if (isNaN(kmFinalValue) || kmFinalValue < kmMin) {
              alert("O KM Final deve ser no mínimo " + kmMin + ".");
              return;
          }
          confirmModal.show();
      });
  
      // Evento para confirmar finalização da reserva
      document.getElementById("confirmSubmit").addEventListener("click", function (e) {
          e.preventDefault();
          const kmFinalValue = document.querySelector("input[name='km_final']").value.trim();
          // Envia atualização de km_final e finaliza a reserva via km-updater.php
          fetch("app/controllers/km-updater.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
              },
              body: new URLSearchParams({
                  id: "<?= $reserva['id'] ?>",
                  km_final: kmFinalValue
              })
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error("Erro na resposta do servidor");
              }
              return response.text();
          })
          .then(data => {
              console.log("Atualização concluída:", data);
          })
          .catch(error => {
              console.error("Erro:", error);
          });
          // Envia o formulário para finalizar a reserva
          form.submit();
          location.reload();
      });
  
      // Evento para Relatar Discrepância em KM Inicial
      alertButton.addEventListener("click", function (event) {
          event.preventDefault();
          alertModal.show();
      });
  
      // Evento para confirmar a discrepância e enviar e-mail via send-alert.php
      document.getElementById("alertSubmit").addEventListener("click", function (e) {
          e.preventDefault();
          const newKmInicial = document.getElementById("new_km_inicial").value.trim();
          if (!newKmInicial || isNaN(Number(newKmInicial))) {
              alert("Por favor, insira um valor válido para o novo KM Inicial.");
              return;
          }
  
          // Envia o novo KM Inicial para send-alert.php
          fetch("app/models/send-alert.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
              },
              body: new URLSearchParams({
                  id: "<?= $reserva['id'] ?>",
                  new_km_inicial: newKmInicial
              })
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error("Erro na resposta do servidor");
              }
              return response.text();
          })
          .then(data => {
              console.log("Alerta enviado:", data);
              alert("Discrepância relatada com sucesso! Um e-mail foi enviado.");
          })
          .catch(error => {
              console.error("Erro:", error);
          });
          alertModal.hide();
      });
    });
  </script>
</body>
</html>
