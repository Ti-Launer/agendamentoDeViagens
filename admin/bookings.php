<?php
require_once '../app/controllers/db.php';
require_once 'session_verify.php';
include "../app/models/header.php";

$database = new Database();
$pdo = $database->connect();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento De Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section {
            display: none;
        }

        /* Oculta todas as seções inicialmente */
        .section.active {
            display: block;
        }

        /* Exibe a seção ativa */
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav nav-pills">
                    <li class="nav-item">
                        <a class="s nav-link bg-dark text-white shadow active" href="#" data-section="booking-history">Histórico de Reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="s nav-link" href="#" data-section="car-agenda">Agenda dos Carros</a>
                    </li>
                    <li class="nav-item" disabled>
                        <a class="s nav-link" href="#" data-section="report-generator">Gerador de Relatórios</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Seções -->
    <div class="container mt-4">
        <div id="booking-history" class="section active">
            <!-- Conteúdo da seção -->
        </div>
        <div id="car-agenda" class="section">

        </div>
        <div id="report-generator" class="section">
            <!-- Conteúdo da seção -->
        </div>
    </div>

    <!-- Modal para editar KM Final -->
    <div class="modal fade" id="editKmModal" tabindex="-1" aria-labelledby="editKmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="editKmModalLabel">Editar KM Final</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="editKmForm">
                        <div class="mb-3">
                            <label for="newKmFinal" class="form-label">Novo KM Final</label>
                            <input type="number" class="form-control" id="newKmFinal" name="newKmFinal" required>
                        </div>
                        <input type="hidden" id="reservaId" name="reservaId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="saveKmBtn">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navLinks = document.querySelectorAll('.s');
        const sections = document.querySelectorAll('.section');

        // Função para alternar entre seções
        function activateSection(sectionId) {
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === sectionId) {
                    section.classList.add('active');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('bg-dark', 'text-white', 'active', 'shadow');
                if (link.dataset.section === sectionId) {
                    link.classList.add('bg-dark', 'text-white', 'active', 'shadow');
                }
            });
        }

        // Função para carregar conteúdo dinamicamente
        function loadSection(sectionFile, targetSection, queryParams = '') {
            fetch(sectionFile + queryParams)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar a seção.');
                    }
                    return response.text();
                })
                .then(data => {
                    targetSection.innerHTML = data; // Atualiza o conteúdo da seção
                    initializeSectionScripts(); // Reativa os scripts necessários
                })
                .catch(error => {
                    targetSection.innerHTML = `<div class="alert alert-danger">Erro: ${error.message}</div>`;
                });
        }

        let currentFilters = {};
        // Função para inicializar scripts específicos das seções
        function initializeSectionScripts() {
            const currentSection = document.querySelector('.section.active');
            const currentSectionId = currentSection.id;
            let currentSectionFile;

            switch (currentSectionId) {
                case 'car-agenda':
                    currentSectionFile = '../app/views/car-agenda.php';
                    break;
                case 'booking-history':
                    currentSectionFile = '../app/views/booking-history.php';
                    break;
                case 'report-generator':
                    currentSectionFile = '../app/views/report-generator.php';
                    break;
                default:
                    console.error('Seção desconhecida:', currentSectionId);
                    return;
            }

            const filterForm = currentSection.querySelector('#filterForm');
            if (filterForm) {
                filterForm.removeEventListener('submit', handleFilterSubmit);

                filterForm.addEventListener('submit', handleFilterSubmit)

                // Reaplica os filtros armazenados
                for (const [key, value] of Object.entries(currentFilters)) {
                    const input = filterForm.querySelector(`[name="${key}"]`);
                    if (input) input.value = value;
                }

                function handleFilterSubmit(event) {
                    event.preventDefault();

                    const formData = new FormData(filterForm);
                    const queryParams = '?' + new URLSearchParams(formData).toString();

                    currentFilters = Object.fromEntries(formData.entries());

                    loadSection(currentSectionFile, currentSection, queryParams);
                }
            }

            // Configuração do Modal de Edição
            const editKmModalElement = document.getElementById("editKmModal");
            if (editKmModalElement) {
                const editKmModal = new bootstrap.Modal(editKmModalElement);
                const saveKmBtn = document.getElementById("saveKmBtn");

                // Configura o evento de clique nos botões "Editar KM"
                currentSection.addEventListener("click", function(event) {
                    const btn = event.target.closest(".edit-km");
                    if (btn) {
                        event.preventDefault();
                        const reservaId = btn.dataset.reservaId;
                        document.getElementById("reservaId").value = reservaId;
                        const kmFinalElem = document.getElementById(`kmFinal${reservaId}`);
                        document.getElementById("newKmFinal").value = kmFinalElem?.textContent.trim() || "";
                        editKmModal.show();
                    }
                });

                // Configura o evento de salvar
                saveKmBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const newKmFinal = document.getElementById("newKmFinal").value.trim();
                    const reservaId = document.getElementById("reservaId").value;

                    if (!newKmFinal || isNaN(newKmFinal)) {
                        alert("Insira um valor numérico válido.");
                        return;
                    }

                    fetch("../app/controllers/update-km_final-km_inicial.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: new URLSearchParams({
                                id: reservaId,
                                new_km_final: newKmFinal
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                const kmFinalElem = document.getElementById(`kmFinal${reservaId}`);
                                if (kmFinalElem) kmFinalElem.textContent = newKmFinal;
                                editKmModal.hide();
                            } else {
                                alert("Erro: " + data.message);
                            }
                        })
                        .catch(error => console.error("Erro:", error));
                });
            }
        }

        // Adiciona eventos de clique nos links
        navLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const sectionId = link.dataset.section; // Obtém o ID da seção
                activateSection(sectionId); // Ativa a seção correspondente

                // Carrega conteúdo dinamicamente para seções específicas
                const targetSection = document.getElementById(sectionId);
                let sectionFile;

                switch (sectionId) {
                    case 'car-agenda':
                        sectionFile = '../app/views/car-agenda.php';
                        break;
                    case 'booking-history':
                        sectionFile = '../app/views/booking-history.php';
                        break;
                    case 'report-generator':
                        sectionFile = '../app/views/report-generator.php';
                        break;
                    default:
                        console.error('Seção desconhecida:', sectionId);
                        return;
                }

                // Reaplica os filtros ao carregar a seção
                const queryParams = new URLSearchParams(currentFilters).toString();
                loadSection(sectionFile, targetSection, queryParams ? '?' + queryParams : '');


            });
        });

        // Carrega a seção inicial
        activateSection('booking-history'); // Ativa a seção inicial
        loadSection('../app/views/booking-history.php', document.getElementById('booking-history'));
    });
</script>


</html>