<?php

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Viagem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
            <div class="card-header text-center bg-primary text-white">
                <h3>Reservar Viagem</h3>
            </div>
            <div class="card-body">
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="nome_usuario" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" placeholder="Seu nome completo" required>
                    </div>

                    <div class="mb-3">
                        <label for="email_usuario" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email_usuario" name="email_usuario" placeholder="seuemail@exemplo.com" required>
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

                    <div id="campo_data_hora" class="mb-3">
                        <label for="data_inicio" class="form-label">Data e Hora de Início</label>
                        <input type="datetime-local" class="form-control" id="data_inicio" name="data_inicio">
                    </div>

                    <div id="campo_data_inicio" class="mb-3" style="display: none;">
                        <label for="data_inicio" class="form-label">Data de Inicio</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                    </div>

                    <div id="campo_data_fim" class="mb-3" style="display: none;">
                        <label for="data_fim" class="form-label">Data de Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim">
                    </div>

                    <div class="mb-3">
                        <label for="carro" class="form-label">Tipo de Aplicação</label>
                        <select class="form-select" id="carro" name="carro" required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="carga" data-bs-toggle="tooltip" title="Ex: Saveiro">Carga</option>
                            <option value="passeio" data-bs-toggle="tooltip" title="Ex: Polo">Passeio</option>
                            <option value="indiferente" data-bs-toggle="tooltip" title="Qualquer tipo de carro serve">Indiferente</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Reservar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const tipoReserva = document.getElementById('tipo_reserva').value;
            const avisoCurta = document.getElementById('aviso_curta');
            const campoDataHora = document.getElementById('campo_data_hora');
            const campoDataInicio = document.getElementById('campo_data_inicio');
            const campoDataFim = document.getElementById('campo_data_fim');

            if (tipoReserva == "curta") {
                avisoCurta.style.display = "block";
                campoDataHora.style.display = "block";
                campoDataInicio.style.display = "none";
                campoDataFim.style.display = "none";
            } else if (tipoReserva == "longa") {
                avisoCurta.style.display = "none";
                campoDataHora.style.display = "none";
                campoDataInicio.style.display = "block";
                campoDataFim.style.display = "block";
            } else {
                avisoCurta.style.display = "none";
                campoDataHora.style.display = "none";
                campoDataInicio.style.display = "none";
                campoDataFim.style.display = "none";
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>