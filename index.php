<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Viagem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;" id="formElement">
            <div class="card-header text-center bg-dark text-white pt-3">
                <h3>Reservar Viagem</h3>
            </div>
            <div class="card-body">
                <form id="addBookingForm" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome completo" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seuemail@exemplo.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_reserva" class="form-label">Tipo de Viagem</label>
                        <select class="form-select" id="tipo_reserva" name="tipo_reserva" required onchange="toggleFields()">
                            <option value="">Selecione...</option>
                            <option value="curta">Viagem Curta</option>
                            <option value="longa">Viagem Longa</option>
                        </select>
                    </div>

                    <div id="aviso_curta" class="mb-3 text-danger" style="display: none;">
                        Você tem 2 horas até o início da viagem a partir do horário selecionado.
                    </div>

                    <div id="campo_data_inicio" class="mb-3" style="display: none;">
                        <label for="data_inicio" class="form-label">Data e Hora de Início</label>
                        <input type="datetime-local" class="form-control" id="data_inicio" name="data_inicio">
                    </div>

                    <div id="campo_data_fim" class="mb-3" style="display: none;">
                        <label for="data_fim" class="form-label">Data de Fim</label>
                        <input type="datetime-local" class="form-control" id="data_fim" name="data_fim">
                    </div>

                    <div class="mb-3">
                        <label for="campo_tipo_carro" class="form-label">Tipo de Aplicação (Carro)</label>
                        <select class="form-select" id="tipo_carro" name="tipo_carro" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="carga" data-bs-toggle="tooltip" title="Ex: Saveiro">Carga</option>
                            <option value="passeio" data-bs-toggle="tooltip" title="Ex: Polo">Passeio</option>
                            <option value="indiferente" data-bs-toggle="tooltip" title="Qualquer tipo de carro serve">Indiferente</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo" name="motivo" placeholder="Por que está agendando?" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" form="addBookingForm" class="btn btn-success" id="submitBtn">
                            Reservar
                            <span class="spinner-border spinner-border-sm text-light d-none" id="spinner" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" style="display: none;" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="addAdminModalLabel">Reserva ainda a ser confirmada!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- O conteúdo será preenchido aqui -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="ok-btn" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('ok-btn').addEventListener('click', function() {
            location.reload();
        })

        function toggleFields() {
            const tipoReserva = document.getElementById('tipo_reserva').value;
            const avisoCurta = document.getElementById('aviso_curta');
            const campoDataInicio = document.getElementById('campo_data_inicio');
            const campoDataFim = document.getElementById('campo_data_fim');

            if (tipoReserva === 'curta') {
                avisoCurta.style.display = "block";
                campoDataInicio.style.display = "block";
                campoDataFim.style.display = "none";
            } else if (tipoReserva === 'longa') {
                avisoCurta.style.display = "none";
                campoDataInicio.style.display = "block";
                campoDataFim.style.display = "block";
            } else {
                avisoCurta.style.display = "none";
                campoDataFim.style.display = "none";
            }
        }

        document.getElementById('addBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const tipoReserva = document.getElementById('tipo_reserva').value;
            const dataInicio = new Date(document.getElementById('data_inicio').value);
            const dataFim = tipoReserva === 'longa' ? new Date(document.getElementById('data_fim').value) : null;

            if (tipoReserva === 'longa' && (!dataFim || dataFim <= dataInicio)) {
                alert('Para viagens longas, é necessário que a data de fim seja maior que a data de início.');
                return;
            }

            const formData = new FormData(this);

            // Exibir spinner
            document.getElementById('spinner').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;

            fetch('app/controllers/new-booking.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na resposta do servidor');
                    }
                    return response.text();
                })
                .then(text => {
                    if (!text) {
                        throw new Error('Resposta vazia do servidor');
                    }
                    return JSON.parse(text); // Converte para JSON
                })
                .then(data => {
                    // Ocultar spinner e habilitar o botão novamente
                    document.getElementById('spinner').classList.add('d-none');
                    document.getElementById('submitBtn').disabled = false;
                    if (data.status === 'success') {
                        const capitalize = (text) => text.charAt(0).toUpperCase() + text.slice(1);
                        const modalBody = document.getElementById('modalBody');
                        modalBody.innerHTML = `
                        <p><strong>Nome:</strong> ${data.nome}</p>
                        <p><strong>E-mail:</strong> ${data.email}</p>
                        <p><strong>Tipo de Reserva:</strong> ${data.tipo_reserva === 'longa' ? 'Viagem Longa' : 'Viagem Curta'}</p>
                        <p><strong>Data de Início:</strong> ${data.data_inicio}</p>
                        ${data.tipo_reserva === 'longa' ? `<p><strong>Data de Fim:</strong> ${data.data_fim}</p>`: '' /*caso seja do tipo curta, não aparece*/}
                        <p><strong>Tipo de Carro:</strong> ${capitalize(data.tipo_carro)}</p>
                        <p><strong>Motivo:</strong> ${data.motivo}</p>
                        <p class="text-danger"><strong>Você receberá um email para poder acompanhar esta reserva</strong></p>
                    `;

                        const formElement = document.getElementById('formElement');
                        formElement.style.display = 'none';

                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();

                        document.getElementById('addBookingForm').reset();
                        toggleFields();
                    } else {
                        alert(data.message || 'Erro ao processar a reserva.');
                    }
                })
                .catch(error => { // Ocultar spinner e habilitar o botão novamente
                    document.getElementById('spinner').classList.add('d-none');
                    document.getElementById('submitBtn').disabled = false;

                    console.error('Erro ao enviar dados: ', error);
                    alert('Erro ao processar a reserva. Verifique o console.');
                });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>