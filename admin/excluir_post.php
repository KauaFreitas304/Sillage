<?php
/**
 * Sillage - Exclusão Definitiva de Postagem (RF06)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

// Bloqueio rigoroso de sessão (RNF02)
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Token de segurança CSRF inválido para exclusão.');
    header("Location: index.php");
    exit;
}

if ($id <= 0) {
    set_flash('error', 'Identificador de postagem inválido.');
    header("Location: index.php");
    exit;
}

$pdo = getDBConnection();

// Busca post para localizar arquivo de imagem
$stmt = $pdo->prepare("SELECT imagem, titulo FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if ($post) {
    // Se a imagem for um upload do usuário (não uma amostra pré-instalada), apaga o arquivo físico
    if (!empty($post['imagem']) && strpos($post['imagem'], 'assets/img/uploads/') !== false) {
        $filePath = __DIR__ . '/../' . $post['imagem'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // Remove do banco de dados
    $stmtDelete = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmtDelete->execute([':id' => $id]);

    set_flash('success', "A publicação \"{$post['titulo']}\" foi excluída com sucesso.");
} else {
    set_flash('error', 'Publicação não encontrada.');
}

header("Location: index.php");
exit;
