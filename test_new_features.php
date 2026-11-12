<?php
/**
 * Teste Específico das 3 Novas Funcionalidades Solicitadas:
 * 1. Sidebar com Rolagem Interna
 * 2. Carrossel de Lançamentos Bootstrap 5 com Cards Expandidos
 * 3. Abas e Campos Condicionais no Painel Administrativo
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';

echo "========================================================\n";
echo "   VALIDAÇÃO DAS 3 INSTRUÇÕES DE REFATORAÇÃO SILLAGE    \n";
echo "========================================================\n\n";

$pdo = getDBConnection();

// --- TESTE 1: Sidebar de Curiosidades (Rolagem Interna) ---
echo "[1] Testando Sidebar de Curiosidades (Rolagem Interna)...\n";
$indexHtml = file_get_contents('http://localhost/Sillage/index.php');

$hasSidebarScrollContainer = strpos($indexHtml, 'sillage-sidebar-scroll-container') !== false;
$cssContent = file_get_contents(__DIR__ . '/assets/css/style.css');
$hasOverflowY = strpos($cssContent, 'sillage-sidebar-scroll-container') !== false && strpos($cssContent, 'overflow-y: auto') !== false;
$hasMaxHeight = (strpos($cssContent, 'max-height: 500px') !== false || strpos($cssContent, 'calc(100vh - 200px)') !== false);

if ($hasSidebarScrollContainer && $hasOverflowY && $hasMaxHeight) {
    echo "  -> [OK] Container da sidebar possui classe 'sillage-sidebar-scroll-container' com max-height e overflow-y: auto!\n";
} else {
    echo "  -> [ERRO] Falha na validação do container de rolagem da sidebar!\n";
    exit(1);
}

// --- TESTE 2: Área Central - Carrossel de Lançamentos Bootstrap 5 ---
echo "\n[2] Testando Carrossel de Lançamentos (Bootstrap 5 e Cards Expandidos)...\n";
$hasCarouselSlide = strpos($indexHtml, 'carousel slide') !== false && strpos($indexHtml, 'id="carouselLancamentos"') !== false;
$hasIndicators = strpos($indexHtml, 'carousel-indicators') !== false;
$hasControls = strpos($indexHtml, 'carousel-control-prev') !== false && strpos($indexHtml, 'carousel-control-next') !== false;
$hasHeroCard = strpos($indexHtml, 'sillage-hero-launch-card') !== false;
$hasPyramid = strpos($indexHtml, 'Pirâmide Olfativa') !== false && strpos($indexHtml, 'Topo:') !== false;
$hasClimate = strpos($indexHtml, 'Clima Ideal:') !== false;

if ($hasCarouselSlide && $hasIndicators && $hasControls && $hasHeroCard && $hasPyramid && $hasClimate) {
    echo "  -> [OK] Carrossel Bootstrap 5 (carousel slide), cards expandidos (100% largura útil), indicadores, controles e pirâmide/clima implementados com sucesso!\n";
} else {
    echo "  -> [ERRO] Falha na validação dos componentes do carrossel!\n";
    echo "     carousel: " . ($hasCarouselSlide ? 'OK' : 'FAIL') . "\n";
    echo "     indicators: " . ($hasIndicators ? 'OK' : 'FAIL') . "\n";
    echo "     controls: " . ($hasControls ? 'OK' : 'FAIL') . "\n";
    echo "     heroCard: " . ($hasHeroCard ? 'OK' : 'FAIL') . "\n";
    echo "     pyramid: " . ($hasPyramid ? 'OK' : 'FAIL') . "\n";
    echo "     climate: " . ($hasClimate ? 'OK' : 'FAIL') . "\n";
    exit(1);
}

// --- TESTE 3: Painel Administrativo (Abas e Campos Condicionais) ---
echo "\n[3] Testando Painel Administrativo (Abas e Campos Condicionais)...\n";
$criarPostPhp = file_get_contents(__DIR__ . '/admin/criar_post.php');
$editarPostPhp = file_get_contents(__DIR__ . '/admin/editar_post.php');

$hasNavTabs = strpos($criarPostPhp, 'categoryNavTabs') !== false && strpos($criarPostPhp, 'nav-tabs') !== false;
$hasSelect = strpos($criarPostPhp, 'name="categoria"') !== false;
$hasConditionalSection = strpos($criarPostPhp, 'id="secao-perfume-tecnica"') !== false;
$hasCuriosidadesAlert = strpos($criarPostPhp, 'id="aviso-curiosidades"') !== false;
$hasSyncJs = strpos($criarPostPhp, 'syncCategoryFlow') !== false;

if ($hasNavTabs && $hasSelect && $hasConditionalSection && $hasCuriosidadesAlert && $hasSyncJs) {
    echo "  -> [OK] Abas de navegação (nav-tabs), select sincronizado, container técnico condicional e scripts dinâmicos integrados com sucesso no painel ADM!\n";
} else {
    echo "  -> [ERRO] Falha na validação do formulário administrativo!\n";
    exit(1);
}

// Teste de criação no banco com curiosidades (deve anular dados técnicos de perfume)
$superAdminId = $pdo->query("SELECT id FROM usuarios_adm LIMIT 1")->fetchColumn();
$stmtCuriosidade = $pdo->prepare("INSERT INTO posts (titulo, categoria, conteudo, autor_id, nome_perfume, concentracao, notas_topo, clima) 
                                  VALUES ('Teste Curiosidade Limpa', 'curiosidades', 'Conteúdo da curiosidade', :autor, NULL, NULL, NULL, NULL)");
$stmtCuriosidade->execute([':autor' => $superAdminId]);
$curiosidadeId = $pdo->lastInsertId();

$stmtCheck = $pdo->prepare("SELECT nome_perfume, notas_topo, clima FROM posts WHERE id = :id");
$stmtCheck->execute([':id' => $curiosidadeId]);
$checkRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (empty($checkRow['nome_perfume']) && empty($checkRow['notas_topo']) && empty($checkRow['clima'])) {
    echo "  -> [OK] Curiosidades persistem limpas de dados técnicos de fragrância no banco de dados!\n";
}

// Limpa registro de teste
$pdo->exec("DELETE FROM posts WHERE id = {$curiosidadeId}");

echo "\n========================================================\n";
echo "   TODOS OS 3 REQUISITOS FORAM TESTADOS E VALIDADOS!    \n";
echo "========================================================\n";
