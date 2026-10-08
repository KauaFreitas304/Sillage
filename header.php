<?php
/**
 * Sillage - Cabeçalho Padrão (Header / Navbar Fixa)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */
require_once __DIR__ . '/config/auth.php';

// Detecta página atual para estilização dos botões
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = $customTitle ?? 'Sillage — O Rastro da Perfumaria';
$headerBadge = $headerBadge ?? 'Inicial-noticias';
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
    <div class="container-fluid px-lg-5 px-3 d-flex align-items-center justify-content-between flex-wrap">
      
      <!-- Lado Esquerdo: Logotipo Sillage e Título da Seção -->
      <div class="d-flex align-items-center">
        <a href="index.php" class="sillage-brand-container text-decoration-none" title="Página Inicial Sillage">
          <img src="assets/img/logo.svg" alt="Sillage Perfumaria" class="sillage-logo-img">
        </a>
        <div class="sillage-page-title-badge d-none d-md-block">
          <?= htmlspecialchars($headerBadge) ?>
        </div>
      </div>

      <!-- Lado Direito: Menu de Navegação em Pílulas e Acesso ADM -->
      <nav class="d-flex align-items-center gap-3 my-2 my-md-0" aria-label="Navegação Principal">
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
