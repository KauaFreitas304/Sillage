<?php
/**
 * Sillage - Exclusão de Administrador
 * Restrito exclusivamente a Kauã Freitas & Yasmin Cristiny (admin@sillage.com)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

// Bloqueio rigoroso de Super Administrador (Apenas admin@sillage.com pode excluir ADMs)
require_super_admin('usuarios.php');

$currentUser = current_user();
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$csrfToken = $_GET['csrf_token'] ?? $_POST['csrf_token'] ?? '';

// Validação de CSRF Token
if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Token de segurança CSRF inválido ou expirado para exclusão.');
    header("Location: usuarios.php");
    exit;
}

// Validação básica de ID
if ($id <= 0) {
    set_flash('error', 'Identificador de administrador inválido.');
    header("Location: usuarios.php");
    exit;
}

// Impede que o Super ADM exclua a si mesmo
if ($id === (int)($currentUser['id'] ?? 0)) {
    set_flash('error', 'Por motivos de segurança, você não pode excluir a sua própria conta de administrador.');
    header("Location: usuarios.php");
    exit;
}

$pdo = getDBConnection();

// Busca o administrador no banco
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios_adm WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$targetUser = $stmt->fetch();

if (!$targetUser) {
    set_flash('error', 'Administrador não encontrado no sistema.');
    header("Location: usuarios.php");
    exit;
}

// Proteção permanente contra exclusão da conta Master do Sillage
if (strtolower(trim($targetUser['email'])) === strtolower(SUPER_ADMIN_EMAIL)) {
    set_flash('error', 'A conta de Super Administrador (' . SUPER_ADMIN_NAME . ' — ' . SUPER_ADMIN_EMAIL . ') é protegida e não pode ser excluída.');
    header("Location: usuarios.php");
    exit;
}

try {
    // Preserva integridade de publicações: reatribui posts existentes do autor excluído para o Super ADM
    $stmtReassign = $pdo->prepare("UPDATE posts SET autor_id = :super_id WHERE autor_id = :target_id");
    $stmtReassign->execute([
        ':super_id'  => $currentUser['id'],
        ':target_id' => $id
    ]);

    // Exclui o administrador
    $stmtDelete = $pdo->prepare("DELETE FROM usuarios_adm WHERE id = :id");
    $stmtDelete->execute([':id' => $id]);

    set_flash('success', "O administrador \"{$targetUser['nome']}\" ({$targetUser['email']}) foi excluído com sucesso.");
} catch (Exception $e) {
    set_flash('error', 'Erro ao excluir administrador: ' . $e->getMessage());
}

header("Location: usuarios.php");
exit;
