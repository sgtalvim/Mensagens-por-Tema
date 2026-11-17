<?php
require __DIR__ . '/config.php';

$pdo = db();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$edicao = $id > 0;
$nome = '';
$erros = [];
$totalMensagens = 0;

if ($edicao) {
    $stmt = $pdo->prepare('SELECT id, nome FROM temas WHERE id = ?');
    $stmt->execute([$id]);
    $tema = $stmt->fetch();

    if (!$tema) {
        flash('warning', 'Tema não encontrado.');
        redirect('temas.php');
    }

    $nome = $tema['nome'];

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM mensagens WHERE tema_id = ?');
    $stmt->execute([$id]);
    $totalMensagens = (int) $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $nome = trim((string) ($_POST['nome'] ?? ''));

    if ($nome === '') {
        $erros[] = 'Digite o nome do tema.';
    } elseif (texto_tamanho($nome) > 80) {
        $erros[] = 'O nome do tema deve ter no máximo 80 caracteres.';
    }

    if (!$erros) {
        $sql = 'SELECT COUNT(*) FROM temas WHERE nome = ? COLLATE NOCASE' . ($edicao ? ' AND id <> ?' : '');
        $stmt = $pdo->prepare($sql);
        $stmt->execute($edicao ? [$nome, $id] : [$nome]);

        if ((int) $stmt->fetchColumn() > 0) {
            $erros[] = 'Já existe um tema com esse nome.';
        }
    }

    if (!$erros) {
        if ($edicao) {
            $stmt = $pdo->prepare('UPDATE temas SET nome = ? WHERE id = ?');
            $stmt->execute([$nome, $id]);
            flash('success', 'Tema atualizado com sucesso.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO temas (nome) VALUES (?)');
            $stmt->execute([$nome]);
            flash('success', 'Tema cadastrado com sucesso.');
        }

        redirect('temas.php');
    }
}

$pageTitle = $edicao ? 'Editar tema' : 'Novo tema';
require __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="temas.php">Temas</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $edicao ? 'Editar' : 'Novo' ?></li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">
                    <i class="bi bi-tag"></i>
                    <?= $edicao ? 'Editar tema' : 'Cadastrar tema' ?>
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

                <form method="post" action="tema-form.php<?= $edicao ? '?id=' . $id : '' ?>">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="nome" class="form-label">Nome do tema <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control" maxlength="80"
                               value="<?= h($nome) ?>" placeholder="Ex.: Motivação, Amizade, Humor..."
                               required autofocus>
                        <div class="form-text">O nome deve ser único.</div>
                    </div>

                    <?php if ($edicao && $totalMensagens > 0): ?>
                        <div class="alert alert-info small">
                            Este tema possui <strong><?= $totalMensagens ?></strong> mensagem(ns). Renomeá-lo mantém as mensagens vinculadas.
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> <?= $edicao ? 'Salvar alterações' : 'Cadastrar tema' ?>
                        </button>
                        <a href="temas.php" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
