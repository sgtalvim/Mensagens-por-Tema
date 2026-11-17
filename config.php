<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__);
define('DB_PATH', BASE_PATH . '/database/app.sqlite');

/**
 * Retorna a conexão PDO (singleton) e garante que o banco exista.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');

        migrate($pdo);
    }

    return $pdo;
}

/**
 * Cria as tabelas na primeira execução.
 */
function migrate(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS temas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL UNIQUE COLLATE NOCASE,
            criado_em TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS mensagens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            texto TEXT NOT NULL,
            tema_id INTEGER NOT NULL,
            criado_em TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            atualizado_em TEXT,
            FOREIGN KEY (tema_id) REFERENCES temas (id) ON DELETE CASCADE
        )"
    );

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_mensagens_tema ON mensagens (tema_id)');
}

/**
 * Escapa texto para exibição em HTML.
 */
function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function flash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function flash_render(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . h(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['_csrf'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sessão expirada. Volte à página anterior e tente novamente.');
    }
}

/**
 * Conta caracteres (não bytes) sem depender da extensão mbstring.
 */
function texto_tamanho(string $texto): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($texto, 'UTF-8');
    }

    return (int) preg_match_all('/./us', $texto);
}

/**
 * Corta um texto respeitando caracteres UTF-8.
 */
function texto_cortar(string $texto, int $limite): string
{
    if (texto_tamanho($texto) <= $limite) {
        return $texto;
    }

    if (function_exists('mb_substr')) {
        return mb_substr($texto, 0, $limite, 'UTF-8');
    }

    preg_match('/^.{0,' . $limite . '}/us', $texto, $match);

    return $match[0] ?? '';
}

/**
 * Trecho de texto com reticências, para listagens.
 */
function resumo(string $texto, int $limite = 90): string
{
    $texto = trim(preg_replace('/\s+/u', ' ', $texto) ?? '');

    if (texto_tamanho($texto) <= $limite) {
        return $texto;
    }

    return texto_cortar($texto, $limite) . '…';
}
