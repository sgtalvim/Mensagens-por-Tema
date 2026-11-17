<?php
require __DIR__ . '/config.php';

$pdo = db();

$temaId = isset($_GET['tema']) ? (int) $_GET['tema'] : 0;

$sql = 'SELECT m.id, m.texto, m.criado_em, m.atualizado_em, t.id AS tema_id, t.nome AS tema
        FROM mensagens m
        JOIN temas t ON t.id = m.tema_id';
$params = [];

if ($temaId > 0) {
    $sql .= ' WHERE m.tema_id = ?';
    $params[] = $temaId;
}

$sql .= ' ORDER BY m.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mensagens = $stmt->fetchAll();

$temas = $pdo->query(
    'SELECT t.id, t.nome, COUNT(m.id) AS total
     FROM temas t
     LEFT JOIN mensagens m ON m.tema_id = t.id
     GROUP BY t.id
     ORDER BY t.nome'
)->fetchAll();

$pageTitle = 'Mensagens';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Mensagens</h1>
        <p class="text-muted mb-0">Cadastre, edite e organize suas mensagens por tema.</p>
    </div>
    <a href="mensagem-form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nova mensagem
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="get" action="mensagens.php" class="row g-2 align-items-end">
            <div class="col-md-8 col-lg-6">
                <label for="tema" class="form-label">Filtrar por tema</label>
                <select name="tema" id="tema" class="form-select">
                    <option value="0">Todos os temas</option>
                    <?php foreach ($temas as $tema): ?>
                        <option value="<?= (int) $tema['id'] ?>" <?= $temaId === (int) $tema['id'] ? 'selected' : '' ?>>
                            <?= h($tema['nome']) ?> (<?= (int) $tema['total'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
            <?php if ($temaId > 0): ?>
                <div class="col-auto">
                    <a href="mensagens.php" class="btn btn-link">Limpar</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!$mensagens): ?>
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-chat-square display-5 d-block mb-3"></i>
            <?= $temaId > 0 ? 'Nenhuma mensagem cadastrada neste tema.' : 'Nenhuma mensagem cadastrada ainda.' ?>
            <div class="mt-3">
                <a href="mensagem-form.php" class="btn btn-primary btn-sm">Cadastrar mensagem</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($mensagens as $mensagem): ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 message-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <p class="mb-2 message-text"><?= nl2br(h($mensagem['texto'])) ?></p>
                                <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                                    <a href="mensagens.php?tema=<?= (int) $mensagem['tema_id'] ?>"
                                       class="badge text-bg-light border text-decoration-none">
                                        <i class="bi bi-tag"></i> <?= h($mensagem['tema']) ?>
                                    </a>
                                    <span><i class="bi bi-calendar3"></i> <?= h($mensagem['criado_em']) ?></span>
                                    <?php if ($mensagem['atualizado_em']): ?>
                                        <span><i class="bi bi-pencil"></i> editada em <?= h($mensagem['atualizado_em']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="mensagem-form.php?id=<?= (int) $mensagem['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form method="post" action="excluir.php" data-confirm="Excluir esta mensagem?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="tipo" value="mensagem">
                                    <input type="hidden" name="id" value="<?= (int) $mensagem['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
