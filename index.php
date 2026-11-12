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

// Segundo Destaque (Apenas Desktop - Requisito): seleciona outro lançamento ou perfume em destaque
$segundoDestaque = null;
if (!empty($lancamentos) && count($lancamentos) > 1) {
    $segundoDestaque = $lancamentos[1];
} else {
    $excluirId = !empty($lancamentos) ? (int)$lancamentos[0]['id'] : 0;
    $stmtOutroDestaque = $pdo->prepare("SELECT * FROM posts WHERE id != ? AND (nome_perfume IS NOT NULL AND nome_perfume != '') ORDER BY data_criacao DESC, id DESC LIMIT 1");
    $stmtOutroDestaque->execute([$excluirId]);
    $segundoDestaque = $stmtOutroDestaque->fetch();
}

// Busca postagens da categoria 'curiosidades' (Tópicos para a Sidebar / Sumário)
$stmtCuriosidades = $pdo->query("SELECT id, titulo FROM posts WHERE categoria = 'curiosidades' ORDER BY id ASC");
$topicosSidebar = $stmtCuriosidades->fetchAll();

$customTitle = 'Sillage — Inicial / Notícias & Lançamentos';
$headerBadge = 'Inicial-noticias';
include __DIR__ . '/header.php';
?>

<main class="sillage-main-wrapper" id="main-content">
  
  <!-- Barra Lateral (Sidebar / Sumário Dinâmico com Rolagem Interna - Requisito 1) -->
  <aside class="sillage-sidebar" aria-label="Sumário de Tópicos e Curiosidades">
    <div class="sillage-sidebar-title d-flex justify-content-between align-items-center">
      <span class="d-flex align-items-center">
        <i class="bi bi-book-half me-2"></i> Sumário / Tópicos
        <?php if (!empty($topicosSidebar)): ?>
          <span class="badge rounded-pill bg-light text-dark ms-2" style="font-size: 0.72rem;"><?= count($topicosSidebar) ?></span>
        <?php endif; ?>
      </span>
      <button class="btn btn-sm sillage-sidebar-collapse-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sillageSidebarCollapse" aria-expanded="false" aria-controls="sillageSidebarCollapse" aria-label="Recolher ou Expandir Tópicos">
        <i class="bi bi-chevron-down"></i>
      </button>
    </div>
    
    <div class="collapse d-lg-block" id="sillageSidebarCollapse">
      <!-- Container com altura fixa máxima e overflow-y: auto para rolagem interna -->
      <div class="sillage-sidebar-scroll-container">
        <nav aria-label="Navegação de Tópicos Educativos">
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
              <li class="text-muted small p-2">Nenhum tópico cadastrado no momento.</li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Divisor Botânico Floral Exclusivo do Wireframe 2 -->
    <div class="sillage-botanical-divider" aria-hidden="true"></div>
  </aside>

  <!-- Área Central (Carrossel de Lançamentos - Requisito 2) -->
  <section class="sillage-content-area" aria-label="Lançamentos do Mundo da Perfumaria">
    
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div class="sillage-section-header mb-0 text-start">
        <h1 class="h2 mb-1">Carrossel de lançamentos</h1>
        <p class="text-muted small mb-0">Descubra as criações mais recentes e as pirâmides olfativas que estão definindo tendências</p>
      </div>

      <!-- Controles de Navegação do Carrossel (Setas Superior) -->
      <?php if (!empty($lancamentos) && count($lancamentos) > 1): ?>
        <div class="d-flex gap-2">
          <button type="button" id="carousel-prev" class="sillage-carousel-arrow" data-bs-target="#carouselLancamentos" data-bs-slide="prev" title="Lançamento anterior" aria-label="Slide Anterior">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button type="button" id="carousel-next" class="sillage-carousel-arrow" data-bs-target="#carouselLancamentos" data-bs-slide="next" title="Próximo lançamento" aria-label="Próximo Slide">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      <?php endif; ?>
    </div>

    <!-- Carrossel Bootstrap 5 com Cards Expandidos 100% da Largura Útil -->
    <div class="sillage-carousel-wrapper">
      <?php if (!empty($lancamentos)): ?>
        <div id="carouselLancamentos" class="carousel slide sillage-launch-carousel" data-bs-ride="carousel" data-bs-interval="6500">
          
          <!-- Indicadores de Slide Estilizados com as Cores do Projeto -->
          <?php if (count($lancamentos) > 1): ?>
            <div class="carousel-indicators sillage-carousel-indicators">
              <?php foreach ($lancamentos as $idx => $post): ?>
                <button type="button" 
                        data-bs-target="#carouselLancamentos" 
                        data-bs-slide-to="<?= $idx ?>" 
                        class="<?= $idx === 0 ? 'active' : '' ?>" 
                        aria-current="<?= $idx === 0 ? 'true' : 'false' ?>" 
                        aria-label="Slide <?= $idx + 1 ?>: <?= htmlspecialchars($post['nome_perfume'] ?? $post['titulo']) ?>">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Slides / Cards Expandidos -->
          <div class="carousel-inner">
            <?php foreach ($lancamentos as $idx => $post): 
              $imgSrc = !empty($post['imagem']) ? htmlspecialchars($post['imagem']) : 'assets/img/samples/una_somos.svg';
              $perfumeNome = !empty($post['nome_perfume']) ? $post['nome_perfume'] : $post['titulo'];
              $plainText = strip_tags($post['conteudo']);
              $resumo = mb_strlen($plainText) > 200 ? mb_substr($plainText, 0, 200) . '...' : $plainText;
            ?>
              <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>" id="post-card-<?= (int)$post['id'] ?>">
                <article class="sillage-hero-launch-card">
                  <div class="row g-0 align-items-stretch">
                    
                    <!-- Imagem do Perfume em Alta Resolução -->
                    <div class="col-lg-5 col-md-5">
                      <div class="sillage-hero-img-wrapper">
                        <span class="sillage-hero-badge">
                          <i class="bi bi-stars me-1"></i> Lançamento
                        </span>
                        <img src="<?= $imgSrc ?>" 
                             alt="<?= htmlspecialchars($perfumeNome) ?>" 
                             class="sillage-hero-img"
                             loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>">
                      </div>
                    </div>

                    <!-- Dados Estruturados, Resumo e Pirâmide Olfativa -->
                    <div class="col-lg-7 col-md-7">
                      <div class="sillage-hero-card-body">
                        
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                          <span class="badge rounded-pill sillage-badge-family">
                            <i class="bi bi-droplet-half me-1"></i><?= htmlspecialchars($post['familia_olfativa'] ?? 'Floral Amadeirado') ?>
                          </span>
                          <span class="badge rounded-pill sillage-badge-conc">
                            <?= htmlspecialchars($post['concentracao'] ?? 'Eau de Parfum') ?>
                          </span>
                        </div>

                        <h2 class="sillage-hero-title">
                          <a href="post.php?id=<?= (int)$post['id'] ?>">
                            <?= htmlspecialchars($perfumeNome) ?>
                          </a>
                        </h2>

                        <?php if (!empty($post['nome_perfume']) && $post['titulo'] !== $post['nome_perfume']): ?>
                          <p class="sillage-hero-subtitle">
                            <?= htmlspecialchars($post['titulo']) ?>
                          </p>
                        <?php endif; ?>

                        <!-- Resumo da Descrição -->
                        <p class="sillage-hero-description">
                          <?= htmlspecialchars($resumo) ?>
                        </p>

                        <!-- Box de Pirâmide Olfativa e Clima Ideal -->
                        <div class="sillage-hero-pyramid-box">
                          <div class="sillage-pyramid-header">
                            <i class="bi bi-diagram-3-fill me-2 text-success"></i> Pirâmide Olfativa
                          </div>
                          
                          <div class="sillage-pyramid-notes">
                            <?php if (!empty($post['notas_topo'])): ?>
                              <div class="sillage-note-item">
                                <span class="note-level">Topo:</span> <?= htmlspecialchars($post['notas_topo']) ?>
                              </div>
                            <?php endif; ?>
                            <?php if (!empty($post['notas_corpo'])): ?>
                              <div class="sillage-note-item">
                                <span class="note-level">Coração:</span> <?= htmlspecialchars($post['notas_corpo']) ?>
                              </div>
                            <?php endif; ?>
                            <?php if (!empty($post['notas_fundo'])): ?>
                              <div class="sillage-note-item">
                                <span class="note-level">Fundo:</span> <?= htmlspecialchars($post['notas_fundo']) ?>
                              </div>
                            <?php endif; ?>
                          </div>

                          <?php if (!empty($post['clima'])): ?>
                            <div class="sillage-hero-climate mt-2 pt-2 border-top border-secondary border-opacity-10">
                              <i class="bi bi-sun-fill text-warning me-1"></i> 
                              <strong>Clima Ideal:</strong> <?= htmlspecialchars($post['clima']) ?>
                            </div>
                          <?php endif; ?>
                        </div>

                        <!-- Botão Ler Matéria Completa -->
                        <div>
                          <a href="post.php?id=<?= (int)$post['id'] ?>" class="sillage-btn-explore" id="btn-ler-<?= (int)$post['id'] ?>">
                            Ler Matéria Completa <i class="bi bi-arrow-right ms-1"></i>
                          </a>
                        </div>

                      </div>
                    </div>

                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Setas Flutuantes Laterais do Carrossel (Anterior / Próximo) -->
          <?php if (count($lancamentos) > 1): ?>
            <button class="carousel-control-prev sillage-carousel-control" type="button" data-bs-target="#carouselLancamentos" data-bs-slide="prev" aria-label="Lançamento Anterior">
              <span class="sillage-carousel-control-icon"><i class="bi bi-chevron-left"></i></span>
            </button>
            <button class="carousel-control-next sillage-carousel-control" type="button" data-bs-target="#carouselLancamentos" data-bs-slide="next" aria-label="Próximo Lançamento">
              <span class="sillage-carousel-control-icon"><i class="bi bi-chevron-right"></i></span>
            </button>
          <?php endif; ?>

        </div>
      <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted" style="background: #FFFFFF;">
          <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
          <h5>Nenhum lançamento publicado no momento</h5>
          <p class="small mb-0">Utilize o painel administrativo para cadastrar novos perfumes.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Segundo Bloco de Destaque (Apenas Desktop - Requisito: Oculto no celular via CSS @media max-width: 768px) -->
    <?php if (!empty($segundoDestaque)): 
      $imgSrcSec = !empty($segundoDestaque['imagem']) ? htmlspecialchars($segundoDestaque['imagem']) : 'assets/img/samples/una_somos.svg';
      $perfumeNomeSec = !empty($segundoDestaque['nome_perfume']) ? $segundoDestaque['nome_perfume'] : $segundoDestaque['titulo'];
      $plainTextSec = strip_tags($segundoDestaque['conteudo']);
      $resumoSec = mb_strlen($plainTextSec) > 200 ? mb_substr($plainTextSec, 0, 200) . '...' : $plainTextSec;
      $badgeTextSec = ($segundoDestaque['categoria'] ?? '') === 'noticias' ? 'Lançamento' : 'Destaque';
      $badgeIconSec = ($segundoDestaque['categoria'] ?? '') === 'noticias' ? 'bi-stars' : 'bi-gem';
    ?>
      <div class="sillage-secondary-featured" id="segundo-destaque-desktop" aria-label="Segundo Bloco de Destaque">
        
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
          <div class="sillage-section-header mb-0 text-start">
            <h2 class="h3 mb-1">Destaque da Perfumaria</h2>
            <p class="text-muted small mb-0">Confira outra criação notável selecionada pela nossa curadoria olfativa</p>
          </div>
        </div>

        <article class="sillage-hero-launch-card" id="post-destaque-<?= (int)$segundoDestaque['id'] ?>">
          <div class="row g-0 align-items-stretch">
            
            <!-- Imagem do Frasco à Esquerda -->
            <div class="col-lg-5 col-md-5">
              <div class="sillage-hero-img-wrapper">
                <span class="sillage-hero-badge">
                  <i class="bi <?= $badgeIconSec ?> me-1"></i> <?= $badgeTextSec ?>
                </span>
                <img src="<?= $imgSrcSec ?>" 
                     alt="<?= htmlspecialchars($perfumeNomeSec) ?>" 
                     class="sillage-hero-img"
                     loading="lazy">
              </div>
            </div>

            <!-- Ficha Olfativa + Botão à Direita -->
            <div class="col-lg-7 col-md-7">
              <div class="sillage-hero-card-body">
                
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                  <span class="badge rounded-pill sillage-badge-family">
                    <i class="bi bi-droplet-half me-1"></i><?= htmlspecialchars($segundoDestaque['familia_olfativa'] ?? 'Floral Amadeirado') ?>
                  </span>
                  <span class="badge rounded-pill sillage-badge-conc">
                    <?= htmlspecialchars($segundoDestaque['concentracao'] ?? 'Eau de Parfum') ?>
                  </span>
                </div>

                <h2 class="sillage-hero-title">
                  <a href="post.php?id=<?= (int)$segundoDestaque['id'] ?>">
                    <?= htmlspecialchars($perfumeNomeSec) ?>
                  </a>
                </h2>

                <?php if (!empty($segundoDestaque['nome_perfume']) && $segundoDestaque['titulo'] !== $segundoDestaque['nome_perfume']): ?>
                  <p class="sillage-hero-subtitle">
                    <?= htmlspecialchars($segundoDestaque['titulo']) ?>
                  </p>
                <?php endif; ?>

                <!-- Resumo da Descrição -->
                <p class="sillage-hero-description">
                  <?= htmlspecialchars($resumoSec) ?>
                </p>

                <!-- Box de Pirâmide Olfativa e Clima Ideal -->
                <div class="sillage-hero-pyramid-box">
                  <div class="sillage-pyramid-header">
                    <i class="bi bi-diagram-3-fill me-2 text-success"></i> Pirâmide Olfativa
                  </div>
                  
                  <div class="sillage-pyramid-notes">
                    <?php if (!empty($segundoDestaque['notas_topo'])): ?>
                      <div class="sillage-note-item">
                        <span class="note-level">Topo:</span> <?= htmlspecialchars($segundoDestaque['notas_topo']) ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($segundoDestaque['notas_corpo'])): ?>
                      <div class="sillage-note-item">
                        <span class="note-level">Coração:</span> <?= htmlspecialchars($segundoDestaque['notas_corpo']) ?>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($segundoDestaque['notas_fundo'])): ?>
                      <div class="sillage-note-item">
                        <span class="note-level">Fundo:</span> <?= htmlspecialchars($segundoDestaque['notas_fundo']) ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($segundoDestaque['clima'])): ?>
                    <div class="sillage-hero-climate mt-2 pt-2 border-top border-secondary border-opacity-10">
                      <i class="bi bi-sun-fill text-warning me-1"></i> 
                      <strong>Clima Ideal:</strong> <?= htmlspecialchars($segundoDestaque['clima']) ?>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Botão Ler Matéria Completa -->
                <div>
                  <a href="post.php?id=<?= (int)$segundoDestaque['id'] ?>" class="sillage-btn-explore" id="btn-ler-destaque-<?= (int)$segundoDestaque['id'] ?>">
                    Ler Matéria Completa <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>

              </div>
            </div>

          </div>
        </article>
      </div>
    <?php endif; ?>

  </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>
