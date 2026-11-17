<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

csrf_check();

$pdo = db();
$tipo = (string) ($_POST['tipo'] ?? '');
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    flash('warning', 'Registro inválido.');
    redirect($tipo === 'tema' ? 'temas.php' : 'mensagens.php');
}

if ($tipo === 'mensagem') {
    $stmt = $pdo->prepare('DELETE FROM mensagens WHERE id = ?');
    $stmt->execute([$id]);

    flash(
        $stmt->rowCount() > 0 ? 'success' : 'warning',
        $stmt->rowCount() > 0 ? 'Mensagem excluída com sucesso.' : 'Mensagem não encontrada.'
    );

    redirect('mensagens.php');
}

if ($tipo === 'tema') {
    $stmt = $pdo->prepare('DELETE FROM temas WHERE id = ?');
    $stmt->execute([$id]);

    flash(
        $stmt->rowCount() > 0 ? 'success' : 'warning',
        $stmt->rowCount() > 0 ? 'Tema excluído (com suas mensagens).' : 'Tema não encontrado.'
    );

    redirect('temas.php');
}

flash('warning', 'Ação desconhecida.');
redirect('index.php');
