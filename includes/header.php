<?php
/** @var string|null $pageTitle */
$pageTitle = $pageTitle ?? 'Mensagens';
$paginaAtual = basename($_SERVER['PHP_SELF'] ?? '');
$flash = flash_render();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> · Mensagens por Tema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="index.php">
            <i class="bi bi-chat-quote-fill"></i> Mensagens por Tema
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($paginaAtual, ['mensagens.php', 'mensagem-form.php'], true) ? 'active' : '' ?>" href="mensagens.php">
                        <i class="bi bi-chat-left-text"></i> Mensagens
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($paginaAtual, ['temas.php', 'tema-form.php'], true) ? 'active' : '' ?>" href="temas.php">
                        <i class="bi bi-tags"></i> Temas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === 'pesquisa.php' ? 'active' : '' ?>" href="pesquisa.php">
                        <i class="bi bi-search"></i> Pesquisar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= h($flash['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
