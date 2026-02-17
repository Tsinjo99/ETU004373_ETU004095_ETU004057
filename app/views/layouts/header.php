<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Gestion des Dons</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar + Navbar -->
    <nav class="navbar navbar-expand-lg navbar-main fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <div class="brand-icon me-2">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div>
                    <span class="brand-name">BNGRC</span>
                    <small class="brand-sub d-none d-sm-block">Gestion des Dons</small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-1">
                    <?php
                    $current_uri = $_SERVER['REQUEST_URI'] ?? '/';
                    $nav_items = [
                        ['/', 'bi-speedometer2', 'Tableau de Bord'],
                        ['/villes', 'bi-geo-alt-fill', 'Villes'],
                        ['/besoins', 'bi-clipboard2-pulse-fill', 'Besoins'],
                        ['/dons', 'bi-gift-fill', 'Dons'],
                        ['/achats', 'bi-cart-fill', 'Achats'],
                        ['/dispatch', 'bi-arrow-left-right', 'Dispatch'],
                        ['/recap', 'bi-graph-up-arrow', 'Récap'],
                    ];
                    foreach ($nav_items as $item):
                        $is_active = ($current_uri === $item[0]) || ($item[0] !== '/' && str_starts_with($current_uri, $item[0]));
                    ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= $is_active ? 'active' : '' ?>" href="<?= $item[0] ?>">
                            <i class="bi <?= $item[1] ?> me-1"></i>
                            <span><?= $item[2] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        <i class="bi bi-clock me-1"></i>
                        <span id="live-clock"><?= date('H:i') ?></span>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid px-4">
