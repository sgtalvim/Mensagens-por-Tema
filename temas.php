<?php
require __DIR__ . '/config.php';

$pdo = db();

$temas = $pdo->query(
    'SELECT t.id, t.nome, t.criado_em, COUNT(m.id) AS total
     FROM temas t
     LEFT JOIN mensagens m ON m.tema_id = t.id
     GROUP BY t.id
     ORDER BY t.nome COLLATE NOCASE'
)->fetchAll();

$pageTitle = 'Temas';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Temas</h1>
        <p class="text-muted mb-0">Crie e gerencie os temas usados para classificar as mensagens.</p>
    </div>
    <a href="tema-form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Novo tema
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (!$temas): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-tags display-5 d-block mb-3"></i>
                Nenhum tema cadastrado ainda.
                <div class="mt-3">
                    <a href="tema-form.php" class="btn btn-primary btn-sm">Cadastrar tema</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tema</th>
                            <th scope="col" class="text-center">Mensagens</th>
                            <th scope="col">Criado em</th>
                            <th scope="col" class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($temas as $tema): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <i class="bi bi-tag text-primary"></i> <?= h($tema['nome']) ?>
                                </td>
                                <td class="text-center">
                                    <a href="mensagens.php?tema=<?= (int) $tema['id'] ?>"
                                       class="badge text-bg-light border text-decoration-none">
                                        <?= (int) $tema['total'] ?>
                                    </a>
                                </td>
                                <td class="text-muted small"><?= h($tema['criado_em']) ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="tema-form.php?id=<?= (int) $tema['id'] ?>"
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <form method="post" action="excluir.php"
                                              data-confirm="Excluir o tema &quot;<?= h($tema['nome']) ?>&quot; e todas as suas mensagens?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="tipo" value="tema">
                                            <input type="hidden" name="id" value="<?= (int) $tema['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
