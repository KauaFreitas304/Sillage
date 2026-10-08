<?php
/**
 * Sillage - Página Inicial (RF01 / Wireframe 2)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/config/db.php';
initializeDatabase();
$pdo = getDBConnection();

// Busca postagens da categoria 'noticias' (Lançamentos para a Área Central)
$stmtNoticias = $pdo->query("SELECT * FROM posts WHERE categoria = 'noticias' ORDER BY data_criacao DESC, id DESC");
$lancamentos = $stmtNoticias->fetchAll();

// Busca postagens da categoria 'curiosidades' (Tópicos para a Sidebar / Sumário)
$stmtCuriosidades = $pdo->query("SELECT id, titulo FROM posts WHERE categoria = 'curiosidades' ORDER BY id ASC");
$topicosSidebar = $stmtCuriosidades->fetchAll();

$customTitle = 'Sillage — Inicial / Notícias & Lançamentos';
$headerBadge = 'Inicial-noticias';
include __DIR__ . '/header.php';
?>

<main class="sillage-main-wrapper" id="main-content">
  
  <!-- Barra Lateral (Sidebar / Sumário Dinâmico) Conforme Mockup 2 -->
  <aside class="sillage-sidebar" aria-label="Sumário de Tópicos e Curiosidades">
    <div class="sillage-sidebar-title">
      <i class="bi bi-book-half me-2"></i> Sumário / Tópicos
    </div>
    
    <nav>
      <ul class="sillage-sidebar-list">
        <?php if (!empty($topicosSidebar)): ?>
          <?php foreach ($topicosSidebar as $index => $topico): ?>
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
  </aside>

  <!-- Área Central (Carrossel de Lançamentos) -->
  <section class="sillage-content-area" aria-label="Lançamentos do Mundo da Perfumaria">
    
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
      <div class="sillage-section-header mb-0 text-start">
        <h1 class="h2 mb-1">Carrossel de lançamentos</h1>
        <p class="text-muted small mb-0">Descubra as criações mais recentes e as pirâmides olfativas que estão definindo tendências</p>
      </div>

      <!-- Controles de Navegação do Carrossel -->
      <div class="d-flex gap-2">
        <button type="button" id="carousel-prev" class="btn btn-sm btn-outline-dark rounded-circle" style="width: 36px; height: 36px;" title="Lançamento anterior">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" id="carousel-next" class="btn btn-sm btn-outline-dark rounded-circle" style="width: 36px; height: 36px;" title="Próximo lançamento">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Grade / Carrossel de Cards Estruturados (Conforme Mockup 2) -->
    <div class="sillage-cards-grid" id="launches-container">
      <?php if (!empty($lancamentos)): ?>
        <?php foreach ($lancamentos as $post): ?>
          <article class="sillage-launch-card" id="post-card-<?= (int)$post['id'] ?>">
            
            <!-- Imagem do Perfume -->
            <div class="sillage-card-img-wrapper">
              <?php 
                $imgSrc = !empty($post['imagem']) ? htmlspecialchars($post['imagem']) : 'assets/img/samples/una_somos.svg';
              ?>
              <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($post['nome_perfume'] ?? $post['titulo']) ?>" loading="lazy">
            </div>

            <!-- Informações Estruturadas: Nome, Concentração, Família, Pirâmide -->
            <div class="sillage-card-body">
              <h2 class="sillage-perfume-name">
                <?= htmlspecialchars($post['nome_perfume'] ?? $post['titulo']) ?>
              </h2>
              
              <p class="sillage-perfume-meta">
                <strong>Concentração:</strong> <?= htmlspecialchars($post['concentracao'] ?? 'Eau de Parfum') ?>
              </p>
              
              <p class="sillage-perfume-meta">
                <strong>Família:</strong> <?= htmlspecialchars($post['familia_olfativa'] ?? 'Floral Amadeirado') ?>
              </p>
              
              <div class="sillage-perfume-pyramid-preview">
                <div><strong>Pirâmide Olfativa:</strong></div>
                <?php if (!empty($post['notas_topo'])): ?>
                  <div class="text-truncate small">• <em>Topo:</em> <?= htmlspecialchars($post['notas_topo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($post['notas_corpo'])): ?>
                  <div class="text-truncate small">• <em>Corpo:</em> <?= htmlspecialchars($post['notas_corpo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($post['notas_fundo'])): ?>
                  <div class="text-truncate small">• <em>Fundo:</em> <?= htmlspecialchars($post['notas_fundo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($post['clima'])): ?>
                  <div class="mt-1 small text-muted"><strong>Clima Ideal:</strong> <?= htmlspecialchars($post['clima']) ?></div>
                <?php endif; ?>
              </div>

              <a href="post.php?id=<?= (int)$post['id'] ?>" class="sillage-btn-explore mt-3" id="btn-ler-<?= (int)$post['id'] ?>">
                Ler Matéria Completa <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>

          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 py-5 text-center text-muted">
          <p>Nenhum lançamento publicado no momento. Utilize o painel administrativo para cadastrar novos perfumes.</p>
        </div>
      <?php endif; ?>
    </div>

  </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>
