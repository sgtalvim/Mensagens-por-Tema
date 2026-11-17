<?php
require __DIR__ . '/config.php';

$pdo = db();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$edicao = $id > 0;

$mensagem = ['texto' => '', 'tema_id' => ''];
$erros = [];

$temas = $pdo->query('SELECT id, nome FROM temas ORDER BY nome')->fetchAll();

if ($edicao) {
    $stmt = $pdo->prepare('SELECT id, texto, tema_id FROM mensagens WHERE id = ?');
    $stmt->execute([$id]);
    $encontrada = $stmt->fetch();

    if (!$encontrada) {
        flash('warning', 'Mensagem não encontrada.');
        redirect('mensagens.php');
    }

    $mensagem = $encontrada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $mensagem['texto'] = trim((string) ($_POST['texto'] ?? ''));
    $mensagem['tema_id'] = (int) ($_POST['tema_id'] ?? 0);

    if ($mensagem['texto'] === '') {
        $erros[] = 'Digite o texto da mensagem.';
    }

    if (texto_tamanho($mensagem['texto']) > 5000) {
        $erros[] = 'A mensagem deve ter no máximo 5000 caracteres.';
    }

    if ($mensagem['tema_id'] <= 0) {
        $erros[] = 'Selecione o tema da mensagem.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM temas WHERE id = ?');
        $stmt->execute([$mensagem['tema_id']]);
        if ((int) $stmt->fetchColumn() === 0) {
            $erros[] = 'O tema selecionado não existe mais. Escolha outro.';
        }
    }

    if (!$erros) {
        if ($edicao) {
            $stmt = $pdo->prepare(
                "UPDATE mensagens
                 SET texto = ?, tema_id = ?, atualizado_em = datetime('now', 'localtime')
                 WHERE id = ?"
            );
            $stmt->execute([$mensagem['texto'], $mensagem['tema_id'], $id]);
            flash('success', 'Mensagem atualizada com sucesso.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO mensagens (texto, tema_id) VALUES (?, ?)');
            $stmt->execute([$mensagem['texto'], $mensagem['tema_id']]);
            flash('success', 'Mensagem cadastrada com sucesso.');
        }

        redirect('mensagens.php');
    }
}

$pageTitle = $edicao ? 'Editar mensagem' : 'Nova mensagem';
require __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="mensagens.php">Mensagens</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $edicao ? 'Editar' : 'Nova' ?></li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">
                    <i class="bi bi-chat-left-text"></i>
                    <?= $edicao ? 'Editar mensagem' : 'Cadastrar mensagem' ?>
                </h1>
            </div>
            <div class="card-body">
                <?php if ($erros): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($erros as $erro): ?>
                                <li><?= h($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!$temas): ?>
                    <div class="alert alert-warning mb-0">
                        Nenhum tema cadastrado. Cadastre um tema antes de criar mensagens.
                        <div class="mt-2">
                            <a href="tema-form.php" class="btn btn-sm btn-warning">
                                <i class="bi bi-tag"></i> Cadastrar tema
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="post" action="mensagem-form.php<?= $edicao ? '?id=' . $id : '' ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="texto" class="form-label">Mensagem <span class="text-danger">*</span></label>
                            <textarea name="texto" id="texto" rows="5" maxlength="5000"
                                      class="form-control" data-counter-target="contador-texto"
                                      placeholder="Escreva a mensagem..." required><?= h($mensagem['texto']) ?></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="form-text">Use quebras de linha para separar parágrafos.</div>
                                <div class="form-text"><span id="contador-texto">0</span>/5000</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="tema_id" class="form-label">Tema <span class="text-danger">*</span></label>
                            <select name="tema_id" id="tema_id" class="form-select" required>
                                <option value="">Selecione um tema...</option>
                                <?php foreach ($temas as $tema): ?>
                                    <option value="<?= (int) $tema['id'] ?>"
                                        <?= (int) $mensagem['tema_id'] === (int) $tema['id'] ? 'selected' : '' ?>>
                                        <?= h($tema['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Precisa de outro tema? <a href="tema-form.php">Cadastre um novo tema</a>.
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> <?= $edicao ? 'Salvar alterações' : 'Cadastrar mensagem' ?>
                            </button>
                            <a href="mensagens.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
