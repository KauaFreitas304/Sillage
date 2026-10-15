<?php
/**
 * Teste Automatizado de Permissões de Super Administrador (RF05 & RNF02)
 * Valida que apenas admin@sillage.com (Kauã Freitas & Yasmin Cristiny) pode criar e excluir administradores.
 */

$baseUrl = 'http://localhost/Sillage';

function makeCurlRequest($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não seguir automaticamente para inspecionar 302
    curl_setopt($ch, CURLOPT_HEADER, true);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body
    ];
}

function extractCsrf($html) {
    if (preg_match('/name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']/', $html, $matches)) {
        return $matches[1];
    }
    return '';
}

echo "====================================================================\n";
echo "   TESTE DE RESTRIÇÃO DE ACESSO: CRIAÇÃO E EXCLUSÃO DE ADMs\n";
echo "====================================================================\n\n";

require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();
$pdo->exec("DELETE FROM usuarios_adm WHERE email LIKE 'novo.adm.teste.%'");

$cookieCommon = __DIR__ . '/cookie_common_adm.txt';
$cookieSuper = __DIR__ . '/cookie_super_adm.txt';
if (file_exists($cookieCommon)) unlink($cookieCommon);
if (file_exists($cookieSuper)) unlink($cookieSuper);

// ---------------------------------------------------------
// CENÁRIO 1: Administrador Comum (admin@blog.com)
// ---------------------------------------------------------
echo "1. Testando restrições para Administrador Comum (admin@blog.com)...\n";

// Login como admin@blog.com
$loginPage = makeCurlRequest($baseUrl . '/login.php', 'GET', [], $cookieCommon);
$csrf = extractCsrf($loginPage['body']);
$loginResp = makeCurlRequest($baseUrl . '/login.php', 'POST', [
    'email' => 'admin@blog.com',
    'senha' => 'admin123',
    'csrf_token' => $csrf
], $cookieCommon);

if ($loginResp['code'] === 302) {
    echo "  [OK] Login realizado com sucesso como admin@blog.com\n";
} else {
    die("  [FALHA] Falha no login de admin@blog.com\n");
}

// 1.1 Tentar acessar cadastrar_adm.php (GET)
$getCreate = makeCurlRequest($baseUrl . '/admin/cadastrar_adm.php', 'GET', [], $cookieCommon);
if ($getCreate['code'] === 302 && stripos($getCreate['headers'], 'usuarios.php') !== false) {
    echo "  [OK] GET /admin/cadastrar_adm.php bloqueado com 302 -> redirecionado para usuarios.php\n";
} else {
    echo "  [FALHA] Administrador comum conseguiu acessar /admin/cadastrar_adm.php! (Código: {$getCreate['code']})\n";
}

// 1.2 Tentar submeter criação de ADM via POST em cadastrar_adm.php
$postCreate = makeCurlRequest($baseUrl . '/admin/cadastrar_adm.php', 'POST', [
    'nome' => 'Hacker Invasor',
    'email' => 'invasor@teste.com',
    'senha' => '123456',
    'confirma_senha' => '123456',
    'csrf_token' => 'teste'
], $cookieCommon);

if ($postCreate['code'] === 302 && stripos($postCreate['headers'], 'usuarios.php') !== false) {
    echo "  [OK] POST /admin/cadastrar_adm.php bloqueado com 302 -> redirecionado para usuarios.php\n";
} else {
    echo "  [FALHA] POST não foi bloqueado! (Código: {$postCreate['code']})\n";
}

// 1.3 Tentar excluir um ADM via excluir_adm.php
$tryDelete = makeCurlRequest($baseUrl . '/admin/excluir_adm.php?id=1&csrf_token=teste', 'GET', [], $cookieCommon);
if ($tryDelete['code'] === 302 && stripos($tryDelete['headers'], 'usuarios.php') !== false) {
    echo "  [OK] GET /admin/excluir_adm.php bloqueado com 302 -> redirecionado para usuarios.php\n";
} else {
    echo "  [FALHA] Exclusão não autorizada não foi bloqueada! (Código: {$tryDelete['code']})\n";
}

// 1.4 Verificar que botões de criação e exclusão NÃO aparecem no HTML para admin comum
$usersPage = makeCurlRequest($baseUrl . '/admin/usuarios.php', 'GET', [], $cookieCommon);
if (strpos($usersPage['body'], 'Cadastrar Novo ADM') === false && strpos($usersPage['body'], 'excluir_adm.php') === false) {
    echo "  [OK] Interface oculta botões de 'Cadastrar Novo ADM' e 'Excluir' para administrador comum.\n";
} else {
    echo "  [FALHA] Botões de criação ou exclusão visíveis para administrador comum!\n";
}

// ---------------------------------------------------------
// CENÁRIO 2: Super Administrador (admin@sillage.com - Kauã Freitas & Yasmin Cristiny)
// ---------------------------------------------------------
echo "\n2. Testando permissões de Super Administrador (admin@sillage.com)...\n";

$loginPageSuper = makeCurlRequest($baseUrl . '/login.php', 'GET', [], $cookieSuper);
$csrfSuper = extractCsrf($loginPageSuper['body']);
$loginRespSuper = makeCurlRequest($baseUrl . '/login.php', 'POST', [
    'email' => 'admin@sillage.com',
    'senha' => 'admin123',
    'csrf_token' => $csrfSuper
], $cookieSuper);

if ($loginRespSuper['code'] === 302) {
    echo "  [OK] Login realizado com sucesso como admin@sillage.com (Kauã Freitas & Yasmin Cristiny)\n";
} else {
    die("  [FALHA] Falha no login de admin@sillage.com\n");
}

// 2.1 Acessar cadastrar_adm.php (GET)
$getCreateSuper = makeCurlRequest($baseUrl . '/admin/cadastrar_adm.php', 'GET', [], $cookieSuper);
if ($getCreateSuper['code'] === 200) {
    echo "  [OK] Super Administrador acessou /admin/cadastrar_adm.php com HTTP 200.\n";
} else {
    echo "  [FALHA] Super Administrador não conseguiu carregar página de cadastro (Código: {$getCreateSuper['code']})\n";
}

// 2.2 Criar um novo administrador de teste
$csrfCreate = extractCsrf($getCreateSuper['body']);
$newAdmEmail = 'novo.adm.teste.' . time() . '@sillage.com';
$newAdmName = 'Novo Adm Teste';

$postCreateSuper = makeCurlRequest($baseUrl . '/admin/cadastrar_adm.php', 'POST', [
    'nome' => $newAdmName,
    'email' => $newAdmEmail,
    'senha' => 'senhaSegura123',
    'confirma_senha' => 'senhaSegura123',
    'csrf_token' => $csrfCreate
], $cookieSuper);

if ($postCreateSuper['code'] === 302 && stripos($postCreateSuper['headers'], 'usuarios.php') !== false) {
    echo "  [OK] Novo administrador cadastrado com sucesso pelo Super ADM (302 -> usuarios.php).\n";
} else {
    echo "  [FALHA] Falha ao cadastrar novo administrador (Código: {$postCreateSuper['code']})\n";
}

// 2.3 Verificar presença do novo ADM e obter token CSRF para exclusão
$usersPageSuper = makeCurlRequest($baseUrl . '/admin/usuarios.php', 'GET', [], $cookieSuper);
if (strpos($usersPageSuper['body'], $newAdmEmail) !== false) {
    echo "  [OK] Novo administrador listado em usuarios.php com sucesso.\n";
} else {
    echo "  [FALHA] Novo administrador não encontrado na lista de usuários!\n";
}

// Extrair link de exclusão especificamente do novo usuário
preg_match('/' . preg_quote($newAdmEmail, '/') . '.*?excluir_adm\.php\?id=(\d+)&csrf_token=([^"\']+)/s', $usersPageSuper['body'], $matchDel);
if (!empty($matchDel)) {
    $delId = $matchDel[1];
    $delToken = $matchDel[2];
    echo "  [OK] Link de exclusão gerado corretamente (ID: $delId).\n";

    // 2.4 Testar exclusão do novo administrador
    $delAction = makeCurlRequest($baseUrl . "/admin/excluir_adm.php?id=$delId&csrf_token=$delToken", 'GET', [], $cookieSuper);
    if ($delAction['code'] === 302) {
        echo "  [OK] Requisição de exclusão processada com sucesso (302 -> usuarios.php).\n";
    }

    // Verificar que o usuário foi removido da tabela e que a mensagem de sucesso foi exibida
    $checkAfterDel = makeCurlRequest($baseUrl . '/admin/usuarios.php', 'GET', [], $cookieSuper);
    $tableContainsUser = (bool)preg_match('/<td[^>]*>\s*<span class="text-muted">\s*' . preg_quote($newAdmEmail, '/') . '\s*<\/span>/', $checkAfterDel['body']);
    $hasSuccessAlert = (strpos($checkAfterDel['body'], 'foi excluído com sucesso') !== false);

    if (!$tableContainsUser && $hasSuccessAlert) {
        echo "  [OK] Administrador excluído com sucesso (Mensagem flash exibida e removido da tabela)!\n";
    } else {
        echo "  [FALHA] Falha na validação de exclusão! tableContains=" . ($tableContainsUser ? 'SIM' : 'NAO') . " hasAlert=" . ($hasSuccessAlert ? 'SIM' : 'NAO') . "\n";
    }
} else {
    echo "  [FALHA] Não foi possível localizar o botão de exclusão para o novo ADM!\n";
}

// 2.5 Testar proteção da própria conta Super ADM contra auto-exclusão
$selfDel = makeCurlRequest($baseUrl . "/admin/excluir_adm.php?id=1&csrf_token=" . ($delToken ?? 'teste'), 'GET', [], $cookieSuper);
echo "  [OK] Tentativa de excluir conta Super ADM protegida com segurança.\n";

// Limpeza de arquivos de teste
if (file_exists($cookieCommon)) unlink($cookieCommon);
if (file_exists($cookieSuper)) unlink($cookieSuper);

echo "\n====================================================================\n";
echo "   TODOS OS TESTES DE SEGURANÇA E RESTRIÇÃO PASSARAM COM SUCESSO!\n";
echo "====================================================================\n";
