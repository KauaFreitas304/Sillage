<?php
/**
 * Sillage - Cabeçalho do Painel Administrativo Restrito
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

// Bloqueio rigoroso de sessão (RNF02)
require_admin();

$currentUser = current_user();
$adminPageTitle = $adminPageTitle ?? 'Painel Administrativo — Sillage';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($adminPageTitle) ?></title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Sillage Stylesheet -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background-color: #F4F6F4;">

  <!-- Barra Superior do Painel ADM -->
  <header class="navbar navbar-dark sticky-top p-3 shadow-sm" style="background-color: var(--sillage-darkgreen);">
    <div class="container-fluid">
      
      <div class="d-flex align-items-center gap-3">
        <a href="index.php" class="navbar-brand d-flex align-items-center gap-2 m-0">
          <img src="../assets/img/logo.svg" alt="Sillage" height="38" style="filter: brightness(0) invert(1);">
          <span class="badge rounded-pill bg-warning text-dark px-2 py-1 small">ADM</span>
        </a>
      </div>

      <!-- Navegação Interna do Painel -->
      <nav class="d-flex align-items-center gap-2 flex-wrap">
        <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
          <i class="bi bi-grid me-1"></i> Publicações
        </a>
        <a href="criar_post.php" class="btn btn-sm rounded-pill px-3" style="background: var(--sillage-champagne); color: var(--sillage-darkgreen); font-weight: 600;">
          <i class="bi bi-plus-circle me-1"></i> Novo Post
        </a>
        <a href="cadastrar_adm.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
          <i class="bi bi-person-plus me-1"></i> Cadastrar ADM
        </a>
        <a href="usuarios.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
          <i class="bi bi-people me-1"></i> Usuários ADM
        </a>
        <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3" title="Abrir portal público em nova aba">
          <i class="bi bi-box-arrow-up-right me-1"></i> Ver Blog
        </a>
        
        <div class="vr bg-light mx-2 d-none d-md-block" style="height: 24px;"></div>
        
        <span class="text-white-50 small d-none d-lg-inline me-2">
          Olá, <strong class="text-white"><?= htmlspecialchars($currentUser['nome']) ?></strong>
        </span>
        
        <a href="../logout.php" class="btn btn-sm btn-danger rounded-pill px-3" title="Encerrar Sessão Segura">
          <i class="bi bi-box-arrow-right me-1"></i> Sair
        </a>
      </nav>

    </div>
  </header>

  <!-- Container de Alertas e Mensagens Flash -->
  <div class="container-fluid px-lg-5 pt-3">
    <?php if ($flashSuccess = get_flash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flashSuccess) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php endif; ?>

    <?php if ($flashError = get_flash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($flashError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php endif; ?>
  </div>
