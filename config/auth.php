<?php
/**
 * Sillage - Gestão de Sessão, Autenticação e Segurança (RNF02)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se há um administrador logado
 */
function is_logged_in(): bool {
    return isset($_SESSION['adm_id']) && !empty($_SESSION['adm_id']);
}

/**
 * Retorna os dados do administrador atual da sessão
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'    => $_SESSION['adm_id'] ?? null,
        'nome'  => $_SESSION['adm_nome'] ?? 'Administrador',
        'email' => $_SESSION['adm_email'] ?? '',
    ];
}

/**
 * Proteção de Rota (RNF02):
 * Redireciona imediatamente para o login caso não esteja autenticado
 */
function require_admin(): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Acesso restrito. Por favor, autentique-se para acessar a área administrativa.';
        // Determina caminho relativo para o login
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
        $redirectUrl = (strpos($scriptPath, '/admin/') !== false) ? '../login.php' : 'login.php';
        header("Location: " . $redirectUrl);
        exit;
    }
}

/**
 * Geração de Token CSRF
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validação de Token CSRF
 */
function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Mensagens Flash para feedback do usuário
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash(string $type): ?string {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}
