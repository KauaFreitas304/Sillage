<?php
/**
 * Sillage - Página de Resenhas (RF02 / Wireframe 3)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/config/db.php';
initializeDatabase();
$pdo = getDBConnection();

// Busca postagens da categoria 'resenhas'
$stmtResenhas = $pdo->query("SELECT * FROM posts WHERE categoria = 'resenhas' ORDER BY data_criacao DESC, id DESC");
$resenhas = $stmtResenhas->fetchAll();

$customTitle = 'Sillage — Resenhas Críticas e Análises Sensoriais';
$headerBadge = 'Resenhas';
include __DIR__ . '/header.php';
?>

<main class="container-fluid py-5" style="background-color: var(--sillage-offwhite); min-height: 80vh;">
  
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
        Resenhas Aprofundadas
      </h1>
      <p class="text-muted lead" style="max-width: 700px; margin: 0 auto;">
        Análises sensoriais completas sobre fixação, projeção, silagem e evolução olfativa na pele real.
      </p>
    </div>

    <!-- Lista de Cards no Formato Largo do Wireframe 3 -->
    <div class="sillage-reviews-container">
      <?php if (!empty($resenhas)): ?>
        <?php foreach ($resenhas as $resenha): ?>
          <article class="sillage-review-wide-card" id="resenha-<?= (int)$resenha['id'] ?>">
            
            <!-- Imagem do Frasco / Caixa (Moldura Branca Conforme Mockup 3) -->
            <div class="sillage-review-thumb">
              <?php 
                $imgSrc = !empty($resenha['imagem']) ? htmlspecialchars($resenha['imagem']) : 'assets/img/samples/una_somos.svg';
              ?>
              <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($resenha['nome_perfume'] ?? $resenha['titulo']) ?>" loading="lazy">
            </div>

            <!-- Dados Estruturados Olfativos & Chamada -->
            <div class="sillage-review-details">
              <h2 class="sillage-review-title">
                <?= htmlspecialchars($resenha['nome_perfume'] ?? $resenha['titulo']) ?>
              </h2>
              
              <div class="sillage-review-info-line">
                <strong>Concentração:</strong> <?= htmlspecialchars($resenha['concentracao'] ?? 'Eau de Parfum') ?>
              </div>
              
              <div class="sillage-review-info-line">
                <strong>Família:</strong> <?= htmlspecialchars($resenha['familia_olfativa'] ?? 'Floral Oriental Amadeirado') ?>
              </div>

              <!-- Pirâmide Olfativa Estruturada -->
              <div class="sillage-review-pyramid-block">
                <div class="fw-bold mb-1" style="color: var(--sillage-champagne);">Pirâmide Olfativa:</div>
                <?php if (!empty($resenha['notas_topo'])): ?>
                  <div>• <em>Notas de Saída:</em> <?= htmlspecialchars($resenha['notas_topo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($resenha['notas_corpo'])): ?>
                  <div>• <em>Notas de Corpo:</em> <?= htmlspecialchars($resenha['notas_corpo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($resenha['notas_fundo'])): ?>
                  <div>• <em>Notas de Fundo:</em> <?= htmlspecialchars($resenha['notas_fundo']) ?></div>
                <?php endif; ?>
                <?php if (!empty($resenha['clima'])): ?>
                  <div class="mt-1 small" style="color: var(--sillage-champagne-light);">
                    <strong>Clima Recomendado:</strong> <?= htmlspecialchars($resenha['clima']) ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                <span class="small" style="color: var(--sillage-sage-light);">
                  <i class="bi bi-calendar3 me-1"></i> Publicado em: <?= date('d/m/Y', strtotime($resenha['data_criacao'])) ?>
                </span>
                <a href="post.php?id=<?= (int)$resenha['id'] ?>" class="sillage-btn-explore" id="btn-resenha-<?= (int)$resenha['id'] ?>">
                  Ler Resenha Completa <i class="bi bi-journal-text ms-1"></i>
                </a>
              </div>

            </div>

          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-light text-center py-5">
          <p class="mb-0">Nenhuma resenha cadastrada no momento.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>
