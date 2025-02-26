<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="bg-dark text-white">
    <div class="container d-flex align-items-center justify-content-between py-3">
        <div>
            <img src="/agendamentoDeViagens/app/models/logo-launer.png" height="40px" alt="Logo">
        </div>

        <nav>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo $currentPage === 'dashboard.php' ? 'active bg-dark' : ''; ?>" href="/agendamentoDeViagens/admin/dashboard.php">Painel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo $currentPage === 'admins-manager.php' ? 'active bg-dark' : ''; ?>" href="/agendamentoDeViagens/admin/admins-manager.php">Admins</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo $currentPage === 'bookings.php' ? 'active bg-dark' : ''; ?>" href="/agendamentoDeViagens/admin/bookings.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo $currentPage === 'cars-manager.php' ? 'active bg-dark' : ''; ?>" href="/agendamentoDeViagens/admin/cars-manager.php">Carros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo $currentPage === 'logout.php' ? 'active bg-dark' : ''; ?> danger" href="/agendamentoDeViagens/admin/logout.php">Sair</a>
                    <style>
                        .danger:hover {
                            color: red !important;
                            border-color: red !important;
                        }
                    </style>
                </li>
            </ul>
        </nav>
    </div>
</header>