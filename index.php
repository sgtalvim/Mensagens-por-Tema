<?php
require __DIR__ . '/config.php';

$pdo = db();

$totalMensagens = (int) $pdo->query('SELECT COUNT(*) FROM mensagens')->fetchColumn();
$totalTemas = (int) $pdo->query('SELECT COUNT(*) FROM temas')->fetchColumn();

$ultimas = $pdo->query(
    'SELECT m.id, m.texto, m.criado_em, t.nome AS tema
     FROM mensagens m
     JOIN temas t ON t.id = m.tema_id
     ORDER BY m.id DESC
     LIMIT 5'
)->fetchAll();

$pageTitle = 'Início';
require __DIR__ . '/includes/header.php';
?>

<div class="hero p-4 p-lg-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h1 class="display-6 fw-bold mb-2">Cadastre, edite e encontre mensagens por tema</h1>
            <p class="lead mb-4">
                Organize mensagens em temas e pesquise por todas as mensagens de um tema
                ou sorteie uma única mensagem aleatória.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="mensagem-form.php" class="btn btn-light fw-semibold">
                    <i class="bi bi-plus-lg"></i> Nova mensagem
                </a>
                <a href="pesquisa.php" class="btn btn-outline-light">
                    <i class="bi bi-search"></i> Pesquisar mensagens
                </a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body">
                            <div class="display-6 fw-bold text-primary"><?= $totalMensagens ?></div>
                            <div class="text-muted small text-uppercase">Mensagens</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body">
                            <div class="display-6 fw-bold text-primary"><?= $totalTemas ?></div>
                            <div class="text-muted small text-uppercase">Temas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0"><i class="bi bi-clock-history"></i> Últimas mensagens</h2>
                <a href="mensagens.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body">
                <?php if (!$ultimas): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-square-dots display-5 d-block mb-2"></i>
                        Nenhuma mensagem cadastrada ainda.
                        <div class="mt-3">
                            <a href="mensagem-form.php" class="btn btn-primary btn-sm">Cadastrar a primeira</a>
                        </div>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ultimas as $mensagem): ?>
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="mb-1"><?= h(resumo($mensagem['texto'], 110)) ?></div>
                                        <span class="badge text-bg-light border">
                                            <i class="bi bi-tag"></i> <?= h($mensagem['tema']) ?>
                                        </span>
                                        <span class="text-muted small ms-1"><?= h($mensagem['criado_em']) ?></span>
                                    </div>
                                    <a href="mensagem-form.php?id=<?= (int) $mensagem['id'] ?>"
                                       class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0"><i class="bi bi-lightning-charge"></i> Atalhos</h2>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="mensagem-form.php" class="btn btn-outline-primary text-start">
                    <i class="bi bi-plus-circle"></i> Cadastrar mensagem
                </a>
                <a href="tema-form.php" class="btn btn-outline-primary text-start">
                    <i class="bi bi-tag"></i> Cadastrar tema
                </a>
                <a href="temas.php" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-tags"></i> Gerenciar temas
                </a>
                <a href="pesquisa.php" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-shuffle"></i> Sortear mensagem por tema
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
