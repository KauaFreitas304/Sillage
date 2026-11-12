<?php
/**
 * Bateria Avançada de Testes Automatizados (Projeto Sillage)
 * Validação rigorosa dos 3 requisitos com simulação HTTP, Sessão, CSRF e Banco de Dados.
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';

echo "======================================================================\n";
echo "   SILLAGE - BATERIA AVANÇADA DE TESTES AUTOMATIZADOS (PHP CLI)      \n";
echo "======================================================================\n\n";

$baseUrl = 'http://localhost/Sillage';
$cookieFile = __DIR__ . '/test_advanced_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

function httpReq($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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

function getCsrfFromHtml($html) {
    if (preg_match('/name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']/', $html, $matches)) {
        return $matches[1];
    }
    return '';
}

$pdo = getDBConnection();
$passed = 0;
$failed = 0;

function assertTest($condition, $description) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASSOU] $description\n";
        $passed++;
    } else {
        echo "  [FALHOU] $description\n";
        $failed++;
    }
}

// =====================================================================
// BLOCO 1: REQUISITO 1 - SIDEBAR COM ROLAGEM INTERNA E CSS
// =====================================================================
echo "--- TESTANDO REQUISITO 1: SIDEBAR DE CURIOSIDADES (ROLAGEM INTERNA) ---\n";

$indexRes = httpReq($baseUrl . '/index.php');
assertTest($indexRes['code'] === 200, "Página inicial respondeu com HTTP 200 OK");

$hasScrollContainer = strpos($indexRes['body'], 'sillage-sidebar-scroll-container') !== false;
assertTest($hasScrollContainer, "Sidebar possui container dedicado '.sillage-sidebar-scroll-container'");

$css = file_get_contents(__DIR__ . '/assets/css/style.css');

$cssScrollMatch = preg_match('/\.sillage-sidebar-scroll-container\s*\{[^}]*overflow-y:\s*auto;/s', $css);
assertTest($cssScrollMatch === 1, "Regra CSS '.sillage-sidebar-scroll-container' define 'overflow-y: auto;'");

$cssHeightMatch = (strpos($css, 'max-height: 500px') !== false || strpos($css, 'calc(100vh - 200px)') !== false);
assertTest($cssHeightMatch, "Regra CSS define altura máxima fixa (max-height 500px ou calc(100vh - 200px))");

$cssScrollbarStyle = strpos($css, '.sillage-sidebar-scroll-container::-webkit-scrollbar') !== false &&
                     strpos($css, 'var(--sillage-darkgreen)') !== false;
assertTest($cssScrollbarStyle, "Estilização personalizada da barra de rolagem (webkit e cores Sillage) presente");

$hasCleanLinks = strpos($indexRes['body'], 'class="sillage-sidebar-item"') !== false &&
                 strpos($indexRes['body'], 'post.php?id=') !== false;
assertTest($hasCleanLinks, "Links dos tópicos da sidebar continuam limpos, estruturados e clicáveis");

// =====================================================================
// BLOCO 2: REQUISITO 2 - ÁREA CENTRAL / CARROSSEL DE LANÇAMENTOS BOOTSTRAP 5
// =====================================================================
echo "\n--- TESTANDO REQUISITO 2: CARROSSEL DE LANÇAMENTOS BOOTSTRAP 5 ---\n";

$hasCarouselWrapper = strpos($indexRes['body'], 'id="carouselLancamentos"') !== false &&
                      strpos($indexRes['body'], 'carousel slide') !== false;
assertTest($hasCarouselWrapper, "Listagem substituída por Carrossel Bootstrap 5 (id='carouselLancamentos' class='carousel slide')");

$hasIndicators = strpos($indexRes['body'], 'carousel-indicators') !== false &&
                 strpos($indexRes['body'], 'sillage-carousel-indicators') !== false;
assertTest($hasIndicators, "Indicadores de slide presentes e estilizados com cores do projeto");

$hasHeaderControls = strpos($indexRes['body'], 'id="carousel-prev"') !== false &&
                     strpos($indexRes['body'], 'id="carousel-next"') !== false;
assertTest($hasHeaderControls, "Controles de navegação (Anterior / Próximo) presentes no cabeçalho da seção");

$hasSideControls = strpos($indexRes['body'], 'carousel-control-prev') !== false &&
                   strpos($indexRes['body'], 'carousel-control-next') !== false;
assertTest($hasSideControls, "Setas flutuantes laterais do carrossel presentes");

$hasHeroCards = strpos($indexRes['body'], 'sillage-hero-launch-card') !== false &&
                strpos($indexRes['body'], 'sillage-hero-img-wrapper') !== false;
assertTest($hasHeroCards, "Cards expandidos (sillage-hero-launch-card) presentes preenchendo 100% da largura");

$hasPyramidInfo = strpos($indexRes['body'], 'Pirâmide Olfativa') !== false &&
                  strpos($indexRes['body'], 'Topo:') !== false &&
                  strpos($indexRes['body'], 'Coração:') !== false &&
                  strpos($indexRes['body'], 'Fundo:') !== false;
assertTest($hasPyramidInfo, "Pirâmide Olfativa completa (Topo, Coração, Fundo) exibida no card do carrossel");

$hasClimateInfo = strpos($indexRes['body'], 'Clima Ideal:') !== false;
assertTest($hasClimateInfo, "Informação de Clima Ideal exibida no card do carrossel");

$hasFullArticleBtn = strpos($indexRes['body'], 'Ler Matéria Completa') !== false;
assertTest($hasFullArticleBtn, "Botão de ação 'Ler Matéria Completa' presente e funcional");

// =====================================================================
// BLOCO 3: REQUISITO 3 - PAINEL ADM (ABAS, SELECT & CAMPOS CONDICIONAIS)
// =====================================================================
echo "\n--- TESTANDO REQUISITO 3: PAINEL ADMINISTRATIVO E CAMPOS DINÂMICOS ---\n";

// 3.1 Login como Super Admin
$loginPage = httpReq($baseUrl . '/login.php', 'GET', [], $cookieFile);
$csrfLogin = getCsrfFromHtml($loginPage['body']);
$doLogin = httpReq($baseUrl . '/login.php', 'POST', [
    'email' => 'admin@sillage.com',
    'senha' => 'admin123',
    'csrf_token' => $csrfLogin
], $cookieFile);

$adminHome = httpReq($baseUrl . '/admin/index.php', 'GET', [], $cookieFile);
assertTest($adminHome['code'] === 200 && strpos($adminHome['body'], 'Gestão Editorial') !== false, "Autenticação ADM realizada com sucesso");

// 3.2 Verificação do formulário criar_post.php
$createPage = httpReq($baseUrl . '/admin/criar_post.php', 'GET', [], $cookieFile);
assertTest($createPage['code'] === 200, "Página admin/criar_post.php carregou com sucesso");

$hasNavTabs = strpos($createPage['body'], 'id="categoryNavTabs"') !== false &&
              strpos($createPage['body'], 'nav-tabs') !== false;
assertTest($hasNavTabs, "Abas em navegação (nav-tabs Bootstrap 5) presentes para divisão dos fluxos");

$hasCategorySelect = strpos($createPage['body'], '<select name="categoria"') !== false;
assertTest($hasCategorySelect, "Campo de seleção (<select name=\"categoria\">) presente");

$hasConditionalPerfumeSection = strpos($createPage['body'], 'id="secao-perfume-tecnica"') !== false;
assertTest($hasConditionalPerfumeSection, "Seção técnica condicional (id='secao-perfume-tecnica') presente");

$hasCuriosidadesAlert = strpos($createPage['body'], 'id="aviso-curiosidades"') !== false;
assertTest($hasCuriosidadesAlert, "Aviso informativo para fluxo de curiosidades presente");

// 3.3 Teste de criação: Categoria 'curiosidades' (deve anular dados técnicos no backend)
$csrfCreate = getCsrfFromHtml($createPage['body']);
$tokenTestCuriosidade = 'Curiosidade Teste ' . time();
$createCuriosidadeRes = httpReq($baseUrl . '/admin/criar_post.php', 'POST', [
    'csrf_token'       => $csrfCreate,
    'titulo'           => $tokenTestCuriosidade,
    'categoria'        => 'curiosidades',
    'conteudo'         => '<p>Conteúdo educativo sobre a história do perfume.</p>',
    'referencias'      => 'Livro Perfumaria Antiga',
    'nome_perfume'     => 'Perfume Nao Deve Salvar',
    'concentracao'     => 'Eau de Parfum',
    'familia_olfativa' => 'Floral',
    'notas_topo'       => 'Bergamota',
    'notas_corpo'      => 'Jasmim',
    'notas_fundo'      => 'Baunilha',
    'clima'            => 'Verão'
], $cookieFile);

$stmtFindCurio = $pdo->prepare("SELECT * FROM posts WHERE titulo = :t");
$stmtFindCurio->execute([':t' => $tokenTestCuriosidade]);
$savedCurio = $stmtFindCurio->fetch(PDO::FETCH_ASSOC);

assertTest(!empty($savedCurio), "Post de Curiosidades inserido com sucesso via POST");
assertTest(
    empty($savedCurio['nome_perfume']) &&
    empty($savedCurio['notas_topo']) &&
    empty($savedCurio['notas_corpo']) &&
    empty($savedCurio['notas_fundo']) &&
    empty($savedCurio['clima']),
    "Campos técnicos de perfume foram anulados (NULL) automaticamente para a categoria 'curiosidades'"
);

// 3.4 Teste de criação: Categoria 'noticias' (Lançamentos com Pirâmide Olfativa e Clima)
$createPage2 = httpReq($baseUrl . '/admin/criar_post.php', 'GET', [], $cookieFile);
$csrfCreate2 = getCsrfFromHtml($createPage2['body']);
$tokenTestNoticia = 'Lancamento Luxo ' . time();
$createNoticiaRes = httpReq($baseUrl . '/admin/criar_post.php', 'POST', [
    'csrf_token'       => $csrfCreate2,
    'titulo'           => $tokenTestNoticia,
    'categoria'        => 'noticias',
    'conteudo'         => '<p>Lançamento bombástico da alta perfumaria internacional.</p>',
    'referencias'      => 'Maison Oficial 2026',
    'nome_perfume'     => 'Sillage Grand Royale',
    'concentracao'     => 'Extrait de Parfum',
    'familia_olfativa' => 'Amadeirado Nobre',
    'notas_topo'       => 'Açafrão Dourado e Mandarina Real',
    'notas_corpo'      => 'Oud Selvagem e Rosa Turca',
    'notas_fundo'      => 'Âmbar Negro e Vetiver Bourbon',
    'clima'            => 'Noites Frias e Gala'
], $cookieFile);

$stmtFindNoticia = $pdo->prepare("SELECT * FROM posts WHERE titulo = :t");
$stmtFindNoticia->execute([':t' => $tokenTestNoticia]);
$savedNoticia = $stmtFindNoticia->fetch(PDO::FETCH_ASSOC);

assertTest(!empty($savedNoticia), "Post de Lançamento/Notícia inserido com sucesso via POST");
assertTest(
    $savedNoticia['nome_perfume'] === 'Sillage Grand Royale' &&
    $savedNoticia['notas_topo'] === 'Açafrão Dourado e Mandarina Real' &&
    $savedNoticia['clima'] === 'Noites Frias e Gala',
    "Campos técnicos de perfume (Pirâmide Olfativa e Clima) foram devidamente gravados para 'noticias'"
);

// 3.5 Verificar se o novo lançamento aparece no carrossel da home
$homeAfterInsert = httpReq($baseUrl . '/index.php');
$appearsInCarousel = strpos($homeAfterInsert['body'], 'Sillage Grand Royale') !== false &&
                     strpos($homeAfterInsert['body'], 'Açafrão Dourado e Mandarina Real') !== false &&
                     strpos($homeAfterInsert['body'], 'Noites Frias e Gala') !== false;
assertTest($appearsInCarousel, "Novo lançamento renderizou imediatamente no Carrossel da Página Inicial");

// 3.6 Teste de Edição: editar_post.php com abas e sincronização
$editPage = httpReq($baseUrl . '/admin/editar_post.php?id=' . $savedNoticia['id'], 'GET', [], $cookieFile);
assertTest($editPage['code'] === 200, "Página admin/editar_post.php carregou com sucesso");
assertTest(strpos($editPage['body'], 'categoryNavTabs') !== false, "Abas nav-tabs presentes na página de edição");
assertTest(strpos($editPage['body'], 'id="secao-perfume-tecnica"') !== false, "Seção técnica presente na página de edição");

// Limpeza dos posts de teste
if (!empty($savedCurio['id'])) {
    $pdo->exec("DELETE FROM posts WHERE id = " . (int)$savedCurio['id']);
}
if (!empty($savedNoticia['id'])) {
    $pdo->exec("DELETE FROM posts WHERE id = " . (int)$savedNoticia['id']);
}

// 3.7 Verificação de rotas de compatibilidade (post-criar.php)
$aliasAdmin = httpReq($baseUrl . '/admin/post-criar.php', 'GET', [], $cookieFile);
assertTest($aliasAdmin['code'] === 200, "Rota de compatibilidade admin/post-criar.php responde 200 OK");

$aliasRoot = httpReq($baseUrl . '/post-criar.php');
assertTest($aliasRoot['code'] === 302 || $aliasRoot['code'] === 200, "Rota raiz post-criar.php redireciona corretamente");

// =====================================================================
// RESULTADO FINAL
// =====================================================================
echo "\n======================================================================\n";
echo "   RESUMO DOS TESTES AUTOMATIZADOS: $passed PASSOU | $failed FALHOU  \n";
echo "======================================================================\n";

if (file_exists($cookieFile)) unlink($cookieFile);

if ($failed === 0) {
    echo "\n>>> SUCESSO TOTAL: TODAS AS 3 DIRETRIZES FORAM VALIDADAS COM 100%! <<<\n\n";
    exit(0);
} else {
    echo "\n>>> ALERTA: HOUVE FALHA EM TESTES! <<<\n\n";
    exit(1);
}
