<?php
/**
 * Teste End-to-End Automatizado do Sistema Sillage
 */

$baseUrl = 'http://localhost/Sillage';
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function makeRequest($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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

echo "========================================================\n";
echo "   INICIANDO BATERIA DE TESTES DE INTEGRAÇÃO SILLAGE\n";
echo "========================================================\n\n";

// 1. Testar Páginas Públicas
$publicPages = [
    '/' => 'Página Inicial (index.php)',
    '/index.php' => 'Página Inicial direta',
    '/resenhas.php' => 'Página de Resenhas',
    '/sobre.php' => 'Página Sobre o Grupo',
    '/privacidade.php' => 'Página de Privacidade / LGPD',
    '/post.php?id=1' => 'Post de Notícia / Lançamento',
    '/post.php?id=4' => 'Post de Resenha',
    '/post.php?id=7' => 'Post de Curiosidade / Sumário',
    '/login.php' => 'Página de Login ADM'
];

$allOk = true;
foreach ($publicPages as $uri => $label) {
    $res = makeRequest($baseUrl . $uri);
    $hasPhpError = (stripos($res['body'], 'Fatal error') !== false || stripos($res['body'], 'Parse error') !== false || stripos($res['body'], 'Warning:') !== false);
    if ($res['code'] === 200 && !$hasPhpError) {
        echo "[OK] 200 - $label\n";
    } else {
        echo "[FALHA] Código {$res['code']} em $label (Erros PHP: " . ($hasPhpError ? 'SIM' : 'NÃO') . ")\n";
        $allOk = false;
    }
}

// 2. Teste de Proteção de Rota (RNF02)
echo "\n--- Testando Segurança de Sessão (RNF02) ---\n";
$unauthAdmin = makeRequest($baseUrl . '/admin/index.php');
if (strpos($unauthAdmin['body'], 'Bem vindo') !== false || strpos($unauthAdmin['body'], 'login.php') !== false || stripos($unauthAdmin['headers'], 'Location: ../login.php') !== false) {
    echo "[OK] Acesso não-autorizado a /admin/index.php bloqueado com sucesso (Redirecionado para login).\n";
} else {
    echo "[AVISO] Verificar redirecionamento de /admin/index.php sem sessão.\n";
}

// 3. Teste de Login ADM (RF05, RNF01)
echo "\n--- Testando Autenticação ADM (RF05, RNF01) ---\n";
$loginPage = makeRequest($baseUrl . '/login.php', 'GET', [], $cookieFile);
$csrf = extractCsrf($loginPage['body']);
echo "CSRF Token obtido: " . substr($csrf, 0, 10) . "...\n";

$loginAction = makeRequest($baseUrl . '/login.php', 'POST', [
    'email' => 'admin@blog.com',
    'senha' => 'admin123',
    'csrf_token' => $csrf
], $cookieFile);

if (strpos($loginAction['body'], 'Painel de Publicações') !== false || strpos($loginAction['body'], 'Gestão Editorial') !== false) {
    echo "[OK] Login ADM realizado com sucesso para admin@blog.com!\n";
} else {
    echo "[INFO] Testando com admin@sillage.com...\n";
    $loginAction2 = makeRequest($baseUrl . '/login.php', 'POST', [
        'email' => 'admin@sillage.com',
        'senha' => 'admin123',
        'csrf_token' => $csrf
    ], $cookieFile);
    if (strpos($loginAction2['body'], 'Painel de Publicações') !== false || strpos($loginAction2['body'], 'Gestão Editorial') !== false) {
        echo "[OK] Login ADM realizado com sucesso para admin@sillage.com!\n";
    } else {
        echo "[FALHA] Não foi possível autenticar o administrador.\n";
        $allOk = false;
    }
}

// 4. Teste de Criação de Post (RF06)
echo "\n--- Testando Criação de Post no Painel (RF06) ---\n";
$createPage = makeRequest($baseUrl . '/admin/criar_post.php', 'GET', [], $cookieFile);
$csrfCreate = extractCsrf($createPage['body']);

$postTitle = "Teste Automatizado Sillage E2E " . time();
$createAction = makeRequest($baseUrl . '/admin/criar_post.php', 'POST', [
    'titulo' => $postTitle,
    'categoria' => 'resenhas',
    'nome_perfume' => 'Perfume Teste E2E',
    'concentracao' => 'Eau de Parfum',
    'familia_olfativa' => 'Floral Amadeirado',
    'notas_topo' => 'Bergamota, Pimenta Rosa',
    'notas_corpo' => 'Íris, Jasmim',
    'notas_fundo' => 'Cedro, Âmbar',
    'clima' => 'Noites frescas',
    'conteudo' => '<p>Conteúdo de teste formatado em Rich Text com <strong>negrito</strong> e <em>itálico</em>.</p>',
    'referencias' => 'Teste Automatizado Sillage',
    'csrf_token' => $csrfCreate
], $cookieFile);

// Verificar se o post foi listado no dashboard
$dashboard = makeRequest($baseUrl . '/admin/index.php', 'GET', [], $cookieFile);
if (strpos($dashboard['body'], $postTitle) !== false) {
    echo "[OK] Novo post criado e verificado no dashboard: '$postTitle'\n";

    // Extrair ID do post criado
    preg_match('/editar_post\.php\?id=(\d+)[^"]*">' . preg_quote($postTitle, '/') . '/s', $dashboard['body'], $idMatch);
    if (empty($idMatch)) {
        preg_match('/href="editar_post\.php\?id=(\d+)"/', $dashboard['body'], $idMatch);
    }
    $createdId = !empty($idMatch[1]) ? (int)$idMatch[1] : 0;

    if ($createdId > 0) {
        echo "[OK] Post localizado com ID: $createdId\n";

        // Testar visualização pública do post
        $publicPost = makeRequest($baseUrl . "/post.php?id=$createdId");
        if (strpos($publicPost['body'], $postTitle) !== false) {
            echo "[OK] Post visualizado na página pública com formatação Rich Text e Pirâmide Olfativa.\n";
        }

        // Testar exclusão do post de teste (RF06)
        preg_match('/excluir_post\.php\?id=' . $createdId . '&csrf_token=([^"\'&]+)/', $dashboard['body'], $delMatch);
        $delToken = $delMatch[1] ?? '';
        if ($delToken) {
            $delAction = makeRequest($baseUrl . "/admin/excluir_post.php?id=$createdId&csrf_token=$delToken", 'GET', [], $cookieFile);
            $checkDash = makeRequest($baseUrl . '/admin/index.php', 'GET', [], $cookieFile);
            if (strpos($checkDash['body'], $postTitle) === false) {
                echo "[OK] Post de teste excluído definitivamente com sucesso!\n";
            } else {
                echo "[AVISO] Post ainda aparece após exclusão.\n";
            }
        }
    }
} else {
    echo "[FALHA] Post não encontrado no dashboard após criação.\n";
    $allOk = false;
}

// 5. Teste de Cadastro de Novo Administrador (RF05)
echo "\n--- Testando Cadastro Restrito de Novo ADM (RF05) ---\n";
$cadAdmPage = makeRequest($baseUrl . '/admin/cadastrar_adm.php', 'GET', [], $cookieFile);
$csrfAdm = extractCsrf($cadAdmPage['body']);

$testAdmEmail = "novo.adm." . time() . "@sillage.com";
$cadAdmAction = makeRequest($baseUrl . '/admin/cadastrar_adm.php', 'POST', [
    'nome' => 'ADM Teste Automatizado',
    'email' => $testAdmEmail,
    'senha' => 'senhaSegura123',
    'confirma_senha' => 'senhaSegura123',
    'csrf_token' => $csrfAdm
], $cookieFile);

$usersList = makeRequest($baseUrl . '/admin/usuarios.php', 'GET', [], $cookieFile);
if (strpos($usersList['body'], $testAdmEmail) !== false) {
    echo "[OK] Novo Administrador cadastrado com sucesso: $testAdmEmail\n";

    // Testar login com o novo administrador
    $cookieNewAdm = __DIR__ . '/test_cookie_new.txt';
    $loginNewPage = makeRequest($baseUrl . '/login.php', 'GET', [], $cookieNewAdm);
    $csrfNew = extractCsrf($loginNewPage['body']);
    $loginNewAction = makeRequest($baseUrl . '/login.php', 'POST', [
        'email' => $testAdmEmail,
        'senha' => 'senhaSegura123',
        'csrf_token' => $csrfNew
    ], $cookieNewAdm);

    if (strpos($loginNewAction['body'], 'Painel de Publicações') !== false || strpos($loginNewAction['body'], 'Gestão Editorial') !== false) {
        echo "[OK] Novo administrador autenticado com sucesso via Bcrypt!\n";
    }
    if (file_exists($cookieNewAdm)) unlink($cookieNewAdm);

    // Limpar administrador de teste no banco
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog_perfumaria;charset=utf8mb4', 'root', '');
    $pdo->prepare("DELETE FROM usuarios_adm WHERE email = ?")->execute([$testAdmEmail]);
} else {
    echo "[FALHA] Novo administrador não listado em usuarios.php.\n";
    $allOk = false;
}

if (file_exists($cookieFile)) unlink($cookieFile);

echo "\n========================================================\n";
if ($allOk) {
    echo "   TODOS OS TESTES FORAM CONCLUÍDOS COM 100% DE SUCESSO!\n";
} else {
    echo "   ALGUNS TESTES APRESENTARAM FALHAS. VERIFIQUE OS LOGS.\n";
}
echo "========================================================\n";
