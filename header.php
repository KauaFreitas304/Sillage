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
  <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
</head>
<body>

  <!-- Cabeçalho Fixo (Header / Navbar) Conforme Mockups -->
  <header class="sillage-header py-2" id="navbar-sillage">
    <div class="container-fluid px-3 px-lg-5 d-flex align-items-center justify-content-between">
      
      <!-- Lado Esquerdo: Logotipo Sillage e Título da Seção -->
      <div class="d-flex align-items-center flex-nowrap sillage-header-brand-wrap">
        <a href="index.php" class="sillage-brand-container text-decoration-none" title="Página Inicial Sillage">
          <img src="assets/img/logo.svg" alt="Sillage Perfumaria" class="sillage-logo-img">
        </a>
        <div class="sillage-page-title-badge text-truncate" title="Seção ativa: <?= htmlspecialchars($headerBadge) ?>">
          <?= htmlspecialchars($headerBadge) ?>
        </div>
      </div>

      <!-- Lado Direito Desktop: Menu de Navegação em Pílulas e Acesso ADM (>= 992px) -->
      <nav class="d-none d-lg-flex align-items-center gap-2 sillage-nav-container sillage-nav-desktop" aria-label="Navegação Principal Desktop">
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

        <!-- Acesso Administrativo Desktop -->
        <?php if (is_logged_in()): ?>
          <a href="admin/index.php" class="sillage-nav-pill bg-light" title="Painel de Controle do Administrador" id="nav-btn-admin-panel">
            <i class="bi bi-speedometer2 me-1"></i> Painel ADM
          </a>
          <a href="logout.php" class="sillage-adm-lock-btn text-danger" title="Encerrar Sessão" id="nav-btn-logout">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        <?php else: ?>
          <a href="login.php" class="sillage-adm-lock-btn" title="Acesso Administrativo Restrito" id="nav-btn-login-adm">
            <i class="bi bi-lock-fill"></i>
          </a>
        <?php endif; ?>
      </nav>

      <!-- Lado Direito Mobile: Botões de Ação Compactos e Acessíveis (< 992px) -->
      <div class="d-flex d-lg-none align-items-center gap-2 sillage-mobile-actions">
        <!-- Botão Acesso Rápido ao Sumário -->
        <button type="button" class="sillage-mobile-action-btn sillage-btn-mobile-sumario" data-bs-toggle="offcanvas" data-bs-target="#sillageSidebarOffcanvas" aria-controls="sillageSidebarOffcanvas" title="Abrir Sumário e Tópicos" aria-label="Abrir Sumário e Tópicos">
          <i class="bi bi-book-half"></i>
          <span class="d-none d-sm-inline ms-1">Sumário</span>
        </button>

        <!-- Botão Login/Painel ADM Mobile -->
        <?php if (is_logged_in()): ?>
          <a href="admin/index.php" class="sillage-mobile-action-btn sillage-btn-mobile-adm" title="Painel Administrativo" aria-label="Painel Administrativo">
            <i class="bi bi-speedometer2"></i>
          </a>
        <?php else: ?>
          <a href="login.php" class="sillage-mobile-action-btn sillage-btn-mobile-lock" title="Acesso ADM" aria-label="Acesso Administrativo">
            <i class="bi bi-lock-fill"></i>
          </a>
        <?php endif; ?>

        <!-- Botão Hambúrguer para Menu Completo -->
        <button type="button" class="sillage-mobile-action-btn sillage-btn-mobile-menu" data-bs-toggle="offcanvas" data-bs-target="#sillageNavOffcanvas" aria-controls="sillageNavOffcanvas" title="Abrir Menu Principal" aria-label="Abrir Menu de Navegação">
          <i class="bi bi-list"></i>
        </button>
      </div>

    </div>
  </header>

  <!-- Offcanvas Drawer de Navegação Geral no Mobile (< 992px) -->
  <div class="offcanvas offcanvas-end sillage-nav-offcanvas" tabindex="-1" id="sillageNavOffcanvas" aria-labelledby="sillageNavOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
      <div class="d-flex align-items-center gap-2" id="sillageNavOffcanvasLabel">
        <img src="assets/img/logo.svg" alt="Sillage Perfumaria" style="height: 34px; width: auto;">
      </div>
      <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Fechar Menu"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-3 p-sm-4">
      <nav class="sillage-mobile-nav-list" aria-label="Navegação Mobile">
        <div class="sillage-mobile-nav-label">Navegação Principal</div>
        
        <a href="index.php" class="sillage-mobile-nav-item <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
          <div class="d-flex align-items-center">
            <i class="bi bi-house-door-fill sillage-nav-icon"></i>
            <span>Inicial & Notícias</span>
          </div>
          <i class="bi bi-chevron-right sillage-nav-arrow"></i>
        </a>

        <a href="resenhas.php" class="sillage-mobile-nav-item <?= ($currentPage === 'resenhas.php') ? 'active' : '' ?>">
          <div class="d-flex align-items-center">
            <i class="bi bi-journal-richtext sillage-nav-icon"></i>
            <span>Resenhas Críticas</span>
          </div>
          <i class="bi bi-chevron-right sillage-nav-arrow"></i>
        </a>

        <a href="sobre.php" class="sillage-mobile-nav-item <?= ($currentPage === 'sobre.php') ? 'active' : '' ?>">
          <div class="d-flex align-items-center">
            <i class="bi bi-people-fill sillage-nav-icon"></i>
            <span>Sobre o Grupo</span>
          </div>
          <i class="bi bi-chevron-right sillage-nav-arrow"></i>
        </a>

        <button type="button" class="sillage-mobile-nav-item w-100 text-start border-0 bg-transparent" data-bs-toggle="offcanvas" data-bs-target="#sillageSidebarOffcanvas" aria-controls="sillageSidebarOffcanvas">
          <div class="d-flex align-items-center">
            <i class="bi bi-book-half sillage-nav-icon"></i>
            <span>Sumário / Tópicos</span>
            <?php if (!empty($topicosSidebar)): ?>
              <span class="badge rounded-pill bg-warning text-dark ms-2"><?= count($topicosSidebar) ?></span>
            <?php endif; ?>
          </div>
          <i class="bi bi-chevron-right sillage-nav-arrow"></i>
        </button>

        <hr class="sillage-mobile-nav-divider my-3">

        <div class="sillage-mobile-nav-label">Acesso Administrativo</div>
        <?php if (is_logged_in()): ?>
          <a href="admin/index.php" class="sillage-mobile-nav-item text-warning">
            <div class="d-flex align-items-center">
              <i class="bi bi-speedometer2 sillage-nav-icon"></i>
              <span>Painel Administrativo</span>
            </div>
            <i class="bi bi-chevron-right sillage-nav-arrow"></i>
          </a>
          <a href="logout.php" class="sillage-mobile-nav-item text-danger">
            <div class="d-flex align-items-center">
              <i class="bi bi-box-arrow-right sillage-nav-icon"></i>
              <span>Encerrar Sessão (Logout)</span>
            </div>
            <i class="bi bi-chevron-right sillage-nav-arrow"></i>
          </a>
        <?php else: ?>
          <a href="login.php" class="sillage-mobile-nav-item <?= ($currentPage === 'login.php') ? 'active' : '' ?>">
            <div class="d-flex align-items-center">
              <i class="bi bi-lock-fill sillage-nav-icon"></i>
              <span>Login ADM Restrito</span>
            </div>
            <i class="bi bi-chevron-right sillage-nav-arrow"></i>
          </a>
        <?php endif; ?>

        <hr class="sillage-mobile-nav-divider my-3">

        <a href="privacidade.php" class="sillage-mobile-nav-item <?= ($currentPage === 'privacidade.php') ? 'active' : '' ?>">
          <div class="d-flex align-items-center">
            <i class="bi bi-shield-lock-fill sillage-nav-icon"></i>
            <span>Privacidade & LGPD</span>
          </div>
          <i class="bi bi-chevron-right sillage-nav-arrow"></i>
        </a>
      </nav>

      <div class="pt-3 border-top border-secondary border-opacity-25 text-center text-muted small">
        <p class="mb-1 text-white-50"><strong>Sillage Perfumaria</strong></p>
        <p class="mb-0 text-white-50 opacity-75">Kauã Freitas &amp; Yasmin Cristiny</p>
      </div>
    </div>
  </div>

  <!-- Offcanvas Drawer da Sidebar no Mobile (Fiel ao Mockup com Ramo Botânico e Paleta Sillage) -->
  <div class="offcanvas offcanvas-start sillage-mobile-offcanvas-sidebar" tabindex="-1" id="sillageSidebarOffcanvas" aria-labelledby="sillageSidebarOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
      <div class="sillage-sidebar-title mb-0 border-0 p-0 text-white d-flex align-items-center" id="sillageSidebarOffcanvasLabel">
        <i class="bi bi-book-half me-2"></i> Sumário / Tópicos
      </div>
      <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Fechar Sumário"></button>
    </div>
    <div class="offcanvas-body position-relative">
      <p class="text-white-50 small mb-3">Selecione uma matéria informativa sobre a arte e ciência da perfumaria:</p>
      <nav aria-label="Tópicos Informativos">
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
            <li class="text-white-50 small">Nenhum tópico cadastrado no momento.</li>
          <?php endif; ?>
        </ul>
      </nav>
      <!-- Divisor Botânico Floral Exclusivo do Wireframe 2 -->
      <div class="sillage-botanical-divider" aria-hidden="true"></div>
    </div>
  </div>
