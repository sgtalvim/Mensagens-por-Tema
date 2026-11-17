<?php
require __DIR__ . '/config.php';

$pdo = db();

$temas = $pdo->query(
    'SELECT t.id, t.nome, COUNT(m.id) AS total
     FROM temas t
     LEFT JOIN mensagens m ON m.tema_id = t.id
     GROUP BY t.id
     ORDER BY t.nome COLLATE NOCASE'
)->fetchAll();

$temaId = isset($_GET['tema']) ? (int) $_GET['tema'] : 0;
$modo = ($_GET['modo'] ?? 'todas') === 'aleatoria' ? 'aleatoria' : 'todas';

$tema = null;
$resultados = [];
$pesquisou = false;

if ($temaId > 0) {
    $stmt = $pdo->prepare('SELECT id, nome FROM temas WHERE id = ?');
    $stmt->execute([$temaId]);
    $tema = $stmt->fetch();

    if ($tema) {
        $pesquisou = true;

        $sql = 'SELECT m.id, m.texto, m.criado_em, m.atualizado_em, t.nome AS tema
                FROM mensagens m
                JOIN temas t ON t.id = m.tema_id
                WHERE m.tema_id = ?';

        if ($modo === 'aleatoria') {
            $sql .= ' ORDER BY RANDOM() LIMIT 1';
        } else {
            $sql .= ' ORDER BY m.id DESC';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$temaId]);
        $resultados = $stmt->fetchAll();
    }
}

$pageTitle = 'Pesquisar mensagens';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Pesquisar mensagens</h1>
        <p class="text-muted mb-0">Escolha um tema e veja todas as mensagens ou sorteie apenas uma.</p>
    </div>
    <a href="mensagem-form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nova mensagem
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <?php if (!$temas): ?>
            <div class="alert alert-warning mb-0">
                Nenhum tema cadastrado. Cadastre um tema e algumas mensagens para pesquisar.
                <div class="mt-2">
                    <a href="tema-form.php" class="btn btn-sm btn-warning">
                        <i class="bi bi-tag"></i> Cadastrar tema
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form method="get" action="pesquisa.php">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <label for="tema" class="form-label">Tema da mensagem <span class="text-danger">*</span></label>
                        <select name="tema" id="tema" class="form-select" required>
                            <option value="">Selecione um tema...</option>
                            <?php foreach ($temas as $item): ?>
                                <option value="<?= (int) $item['id'] ?>" <?= $temaId === (int) $item['id'] ? 'selected' : '' ?>>
                                    <?= h($item['nome']) ?> (<?= (int) $item['total'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-7">
                        <span class="form-label d-block">Tipo de resultado</span>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <div class="form-check result-option flex-fill">
                                <input class="form-check-input" type="radio" name="modo" id="modo-todas"
                                       value="todas" <?= $modo === 'todas' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="modo-todas">
                                    <i class="bi bi-list-ul"></i> Todas as mensagens do tema
                                </label>
                            </div>
                            <div class="form-check result-option flex-fill">
                                <input class="form-check-input" type="radio" name="modo" id="modo-aleatoria"
                                       value="aleatoria" <?= $modo === 'aleatoria' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="modo-aleatoria">
                                    <i class="bi bi-shuffle"></i> Uma mensagem aleatória
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Pesquisar
                    </button>
                    <?php if ($pesquisou): ?>
                        <a href="pesquisa.php" class="btn btn-outline-secondary">Nova pesquisa</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if ($pesquisou): ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h5 mb-0">
            <i class="bi bi-tag text-primary"></i> <?= h($tema['nome']) ?>
            <span class="text-muted fw-normal small">
                · <?= $modo === 'aleatoria' ? 'mensagem aleatória' : count($resultados) . ' mensagem(ns) encontrada(s)' ?>
            </span>
        </h2>
        <a href="pesquisa.php?tema=<?= (int) $tema['id'] ?>&modo=<?= $modo === 'aleatoria' ? 'aleatoria' : 'todas' ?>"
           class="btn btn-sm btn-outline-primary" id="btn-sortear">
            <i class="bi bi-shuffle"></i> Sortear outra
        </a>
    </div>

    <?php if (!$resultados): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-emoji-neutral display-5 d-block mb-3"></i>
                Nenhuma mensagem cadastrada neste tema.
                <div class="mt-3">
                    <a href="mensagem-form.php" class="btn btn-sm btn-primary">Cadastrar mensagem</a>
                </div>
            </div>
        </div>
    <?php elseif ($modo === 'aleatoria'): ?>
        <?php $sorteada = $resultados[0]; ?>
        <div class="card border-0 shadow-sm destaque-card">
            <div class="card-body p-4 p-lg-5 text-center">
                <div class="display-6 text-primary mb-3"><i class="bi bi-shuffle"></i></div>
                <p class="fs-5 message-text mb-4"><?= nl2br(h($sorteada['texto'])) ?></p>
                <div class="text-muted small">
                    <span class="badge text-bg-light border"><i class="bi bi-tag"></i> <?= h($sorteada['tema']) ?></span>
                    <span class="ms-2"><i class="bi bi-calendar3"></i> <?= h($sorteada['criado_em']) ?></span>
                </div>
                <div class="mt-4 d-flex justify-content-center gap-2">
                    <a href="pesquisa.php?tema=<?= (int) $tema['id'] ?>&modo=aleatoria"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-shuffle"></i> Sortear novamente
                    </a>
                    <a href="mensagem-form.php?id=<?= (int) $sorteada['id'] ?>"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($resultados as $mensagem): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100 message-card">
                        <div class="card-body">
                            <p class="message-text mb-3"><?= nl2br(h($mensagem['texto'])) ?></p>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
                                <span><i class="bi bi-calendar3"></i> <?= h($mensagem['criado_em']) ?></span>
                                <a href="mensagem-form.php?id=<?= (int) $mensagem['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
