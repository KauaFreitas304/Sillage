<?php
/**
 * Sillage - Página do Post Individual (RF04)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/config/db.php';
initializeDatabase();
$pdo = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT p.*, u.nome AS autor_nome 
    FROM posts p 
    LEFT JOIN usuarios_adm u ON p.autor_id = u.id 
    WHERE p.id = :id
");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    header("HTTP/1.0 404 Not Found");
    $customTitle = 'Post Não Encontrado — Sillage';
    $headerBadge = 'Erro 404';
    include __DIR__ . '/header.php';
    ?>
    <main class="container py-5 text-center my-5">
      <div class="alert alert-warning py-5 shadow-sm rounded-4">
        <i class="bi bi-exclamation-triangle display-3 text-warning"></i>
        <h1 class="h2 mt-3">Publicação Não Encontrada</h1>
        <p class="lead text-muted">A matéria solicitada não existe ou foi removida pelo administrador.</p>
        <a href="index.php" class="btn sillage-btn-primary mt-3">
          <i class="bi bi-arrow-left me-1"></i> Retornar à Página Inicial
        </a>
      </div>
    </main>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

$categoryLabels = [
    'noticias'    => 'Notícias & Lançamentos',
    'resenhas'    => 'Resenhas Críticas',
    'curiosidades'=> 'Informações & Curiosidades'
];

$categoriaFormatada = $categoryLabels[$post['categoria']] ?? ucfirst($post['categoria']);
$customTitle = htmlspecialchars($post['titulo']) . ' — Sillage';
$headerBadge = $categoriaFormatada;
include __DIR__ . '/header.php';
?>

<main class="container py-5">
  
  <div class="sillage-post-container">
    
    <article class="sillage-post-card" id="post-view-<?= (int)$post['id'] ?>">
      
      <!-- Categoria & Metadados -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <span class="sillage-category-tag">
          <?= htmlspecialchars($categoriaFormatada) ?>
        </span>
        <span class="text-muted small">
          <i class="bi bi-clock me-1"></i> Publicado em <?= date('d/m/Y \à\s H:i', strtotime($post['data_criacao'])) ?>
          <?php if (!empty($post['autor_nome'])): ?>
            • Por <strong><?= htmlspecialchars($post['autor_nome']) ?></strong>
          <?php endif; ?>
        </span>
      </div>

      <!-- Título Principal -->
      <h1 class="display-6 fw-bold mb-4" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
        <?= htmlspecialchars($post['titulo']) ?>
      </h1>

      <!-- Imagem de Destaque -->
      <?php if (!empty($post['imagem'])): ?>
        <div class="text-center mb-4">
          <img src="<?= htmlspecialchars($post['imagem']) ?>" alt="<?= htmlspecialchars($post['titulo']) ?>" class="sillage-post-hero-img">
        </div>
      <?php endif; ?>

      <!-- Bloco de Informações Olfativas & Clima (Quando Aplicável) -->
      <?php if (!empty($post['notas_topo']) || !empty($post['notas_corpo']) || !empty($post['notas_fundo']) || !empty($post['clima'])): ?>
        <section class="sillage-pyramid-box mb-4" aria-label="Ficha Técnica Olfativa">
          <h3 class="h5 fw-bold mb-3" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
            <i class="bi bi-flower1 me-2 text-primary"></i> Estrutura Olfativa & Ficha Técnica
          </h3>
          
          <div class="row g-2 mb-3">
            <?php if (!empty($post['nome_perfume'])): ?>
              <div class="col-sm-6">
                <strong>Fragrância:</strong> <?= htmlspecialchars($post['nome_perfume']) ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($post['concentracao'])): ?>
              <div class="col-sm-6">
                <strong>Concentração:</strong> <?= htmlspecialchars($post['concentracao']) ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($post['familia_olfativa'])): ?>
              <div class="col-sm-6">
                <strong>Família:</strong> <?= htmlspecialchars($post['familia_olfativa']) ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($post['clima'])): ?>
              <div class="col-sm-6">
                <strong>Clima Ideal:</strong> <?= htmlspecialchars($post['clima']) ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Níveis da Pirâmide Olfativa -->
          <?php if (!empty($post['notas_topo'])): ?>
            <div class="sillage-pyramid-level topo">
              <strong>Topo / Saída:</strong> <?= htmlspecialchars($post['notas_topo']) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($post['notas_corpo'])): ?>
            <div class="sillage-pyramid-level corpo">
              <strong>Coração / Corpo:</strong> <?= htmlspecialchars($post['notas_corpo']) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($post['notas_fundo'])): ?>
            <div class="sillage-pyramid-level fundo">
              <strong>Fundo / Base:</strong> <?= htmlspecialchars($post['notas_fundo']) ?>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>

      <!-- Conteúdo Formatado (Rich Text) -->
      <div class="sillage-post-content mb-4">
        <?= $post['conteudo'] ?>
      </div>

      <!-- Referências Bibliográficas -->
      <?php if (!empty($post['referencias'])): ?>
        <aside class="sillage-references-box">
          <div class="fw-bold mb-1" style="color: var(--sillage-darkgreen);">
            <i class="bi bi-journal-bookmark me-1"></i> Referências & Fontes:
          </div>
          <p class="mb-0 text-muted small">
            <?= nl2br(htmlspecialchars($post['referencias'])) ?>
          </p>
        </aside>
      <?php endif; ?>

      <!-- Navegação de Retorno -->
      <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="index.php" class="btn sillage-btn-secondary">
          <i class="bi bi-arrow-left me-1"></i> Voltar à Página Inicial
        </a>

        <?php if ($post['categoria'] === 'resenhas'): ?>
          <a href="resenhas.php" class="btn sillage-btn-primary">
            Ver Mais Resenhas <i class="bi bi-arrow-right ms-1"></i>
          </a>
        <?php endif; ?>
      </div>

    </article>

  </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>
