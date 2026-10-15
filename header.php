<?php
/**
 * Sillage - Cabeçalho Padrão (Header / Navbar Fixa & Sidebar Mobile)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';

// Detecta página atual para estilização dos botões
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = $customTitle ?? 'Sillage — O Rastro da Perfumaria';
$headerBadge = $headerBadge ?? 'Inicial-noticias';

// Carrega os tópicos da sidebar caso ainda não tenham sido injetados pela view
if (!isset($topicosSidebar)) {
    try {
        $pdoGlobal = getDBConnection();
        $stmtCurios = $pdoGlobal->query("SELECT id, titulo FROM posts WHERE categoria = 'curiosidades' ORDER BY id ASC");
        $topicosSidebar = $stmtCurios->fetchAll();
    } catch (Throwable $e) {
        $topicosSidebar = [];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="Sillage — Portal educativo, estilizado e informativo sobre o universo da perfumaria. Desenvolvido por Kauã Freitas e Yasmin Cristiny.">
  <meta name="author" content="Kauã Freitas e Yasmin Cristiny">
  <meta name="robots" content="index, follow">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Custom Sillage Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Cabeçalho Fixo (Header / Navbar) Conforme Mockups -->
  <header class="sillage-header py-2" id="navbar-sillage">
    <div class="container-fluid px-lg-5 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
      
      <!-- Lado Esquerdo: Logotipo Sillage e Título da Seção -->
      <div class="d-flex align-items-center flex-nowrap">
        <a href="index.php" class="sillage-brand-container text-decoration-none" title="Página Inicial Sillage">
          <img src="assets/img/logo.svg" alt="Sillage Perfumaria" class="sillage-logo-img">
        </a>
        <div class="sillage-page-title-badge" title="Seção ativa">
          <?= htmlspecialchars($headerBadge) ?>
        </div>
      </div>

      <!-- Lado Direito: Menu de Navegação em Pílulas, Acesso ADM e Botão da Sidebar no Mobile -->
      <nav class="d-flex align-items-center gap-2 sillage-nav-container my-1 my-md-0" aria-label="Navegação Principal">
        
        <!-- Botão Discreto de Acesso ao Sumário / Sidebar no Mobile -->
        <button type="button" class="sillage-nav-pill sillage-nav-sidebar-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sillageSidebarOffcanvas" aria-controls="sillageSidebarOffcanvas" title="Abrir Sumário e Tópicos">
          <i class="bi bi-book-half me-1"></i> Sumário
        </button>

        <?php if ($currentPage !== 'index.php'): ?>
          <a href="index.php" class="sillage-nav-pill" id="nav-btn-home">
            Inicial-noticias
          </a>
        <?php endif; ?>

        <?php if ($currentPage !== 'resenhas.php'): ?>
          <a href="resenhas.php" class="sillage-nav-pill" id="nav-btn-resenhas">
            Resenhas
          </a>
        <?php endif; ?>

        <?php if ($currentPage !== 'sobre.php'): ?>
          <a href="sobre.php" class="sillage-nav-pill" id="nav-btn-sobre">
            Sobre o Grupo
          </a>
        <?php endif; ?>

        <!-- Acesso Administrativo -->
        <?php if (is_logged_in()): ?>
          <a href="admin/index.php" class="sillage-nav-pill bg-light" title="Painel de Controle do Administrador" id="nav-btn-admin-panel">
            <i class="bi bi-speedometer2 me-1"></i> Painel ADM
          </a>
          <a href="logout.php" class="sillage-adm-lock-btn text-danger" title="Encerrar Sessão" id="nav-btn-logout">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        <?php else: ?>
          <!-- Acesso Discreto ao Login ADM com Ícone de Cadeado -->
          <a href="login.php" class="sillage-adm-lock-btn" title="Acesso Administrativo Restrito" id="nav-btn-login-adm">
            <i class="bi bi-lock-fill"></i>
          </a>
        <?php endif; ?>
      </nav>

    </div>
  </header>

  <!-- Offcanvas Drawer da Sidebar no Mobile (Fiel ao Mockup 2 com Ramo Botânico e Paleta Sillage) -->
  <div class="offcanvas offcanvas-start sillage-mobile-offcanvas-sidebar" tabindex="-1" id="sillageSidebarOffcanvas" aria-labelledby="sillageSidebarOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
      <div class="sillage-sidebar-title mb-0 border-0 p-0" id="sillageSidebarOffcanvasLabel">
        <i class="bi bi-book-half me-2"></i> Sumário / Tópicos
      </div>
      <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Fechar Sumário"></button>
    </div>
    <div class="offcanvas-body position-relative">
      <p class="text-muted small mb-3">Selecione uma matéria informativa sobre a arte e ciência da perfumaria:</p>
      <nav>
        <ul class="sillage-sidebar-list">
          <?php if (!empty($topicosSidebar)): ?>
            <?php foreach ($topicosSidebar as $topico): ?>
              <li class="sillage-sidebar-item">
                <a href="post.php?id=<?= (int)$topico['id'] ?>" title="Ler: <?= htmlspecialchars($topico['titulo']) ?>">
                  <?= htmlspecialchars($topico['titulo']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="text-muted small">Nenhum tópico cadastrado no momento.</li>
          <?php endif; ?>
        </ul>
      </nav>
      <!-- Divisor Botânico Floral Exclusivo do Wireframe 2 -->
      <div class="sillage-botanical-divider" aria-hidden="true"></div>
    </div>
  </div>
