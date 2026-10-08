<?php
/**
 * Sillage - Painel de Controle Principal (Dashboard & Listagem CRUD)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$adminPageTitle = 'Painel de Publicações — Sillage ADM';
require_once __DIR__ . '/admin_header.php';

$pdo = getDBConnection();

// Filtro e busca
$filtroCategoria = trim($_GET['categoria'] ?? '');
$busca = trim($_GET['q'] ?? '');

$sql = "SELECT p.*, u.nome AS autor_nome 
        FROM posts p 
        LEFT JOIN usuarios_adm u ON p.autor_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($filtroCategoria) && in_array($filtroCategoria, ['noticias', 'resenhas', 'curiosidades'])) {
    $sql .= " AND p.categoria = :categoria";
    $params[':categoria'] = $filtroCategoria;
}

if (!empty($busca)) {
    $sql .= " AND (p.titulo LIKE :busca OR p.nome_perfume LIKE :busca OR p.conteudo LIKE :busca)";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY p.data_criacao DESC, p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Métricas para o Dashboard
$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalNoticias = $pdo->query("SELECT COUNT(*) FROM posts WHERE categoria = 'noticias'")->fetchColumn();
$totalResenhas = $pdo->query("SELECT COUNT(*) FROM posts WHERE categoria = 'resenhas'")->fetchColumn();
$totalCuriosidades = $pdo->query("SELECT COUNT(*) FROM posts WHERE categoria = 'curiosidades'")->fetchColumn();
$totalAdms = $pdo->query("SELECT COUNT(*) FROM usuarios_adm")->fetchColumn();
?>

<main class="container-fluid px-lg-5 py-4">

  <!-- Cabeçalho do Dashboard & Estatísticas -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color: var(--sillage-darkgreen);">
        Gestão Editorial de Conteúdo
      </h1>
      <p class="text-muted small mb-0">Controle completo de publicações, resenhas e pirâmides olfativas do Sillage.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="criar_post.php" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
        <i class="bi bi-plus-lg me-1"></i> Criar Nova Publicação
      </a>
      <a href="cadastrar_adm.php" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-person-plus-fill me-1"></i> Novo ADM
      </a>
    </div>
  </div>

  <!-- Cards de Métricas -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card border-0 shadow-sm p-3 rounded-4" style="background: #FFFFFF;">
        <span class="text-muted small fw-semibold">Total Geral</span>
        <h3 class="fw-bold mt-1 mb-0" style="color: var(--sillage-darkgreen);"><?= $totalPosts ?></h3>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card border-0 shadow-sm p-3 rounded-4" style="background: rgba(159, 175, 144, 0.2);">
        <span class="text-muted small fw-semibold">Lançamentos</span>
        <h3 class="fw-bold mt-1 mb-0" style="color: var(--sillage-darkgreen);"><?= $totalNoticias ?></h3>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card border-0 shadow-sm p-3 rounded-4" style="background: rgba(242, 220, 177, 0.35);">
        <span class="text-muted small fw-semibold">Resenhas</span>
        <h3 class="fw-bold mt-1 mb-0" style="color: #6C5528;"><?= $totalResenhas ?></h3>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card border-0 shadow-sm p-3 rounded-4" style="background: rgba(200, 162, 200, 0.3);">
        <span class="text-muted small fw-semibold">Curiosidades</span>
        <h3 class="fw-bold mt-1 mb-0" style="color: #633C63;"><?= $totalCuriosidades ?></h3>
      </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
      <div class="card border-0 shadow-sm p-3 rounded-4" style="background: #FFFFFF;">
        <span class="text-muted small fw-semibold">Administradores</span>
        <h3 class="fw-bold mt-1 mb-0" style="color: var(--sillage-darkgreen);"><?= $totalAdms ?></h3>
      </div>
    </div>
  </div>

  <!-- Barra de Filtros e Busca -->
  <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #FFFFFF;">
    <form method="GET" action="index.php" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control border-0 bg-light" placeholder="Buscar por título, perfume ou notas..." value="<?= htmlspecialchars($busca) ?>">
        </div>
      </div>

      <div class="col-md-4">
        <select name="categoria" class="form-select border-0 bg-light">
          <option value="">Todas as Categorias</option>
          <option value="noticias" <?= $filtroCategoria === 'noticias' ? 'selected' : '' ?>>Notícias & Lançamentos</option>
          <option value="resenhas" <?= $filtroCategoria === 'resenhas' ? 'selected' : '' ?>>Resenhas Críticas</option>
          <option value="curiosidades" <?= $filtroCategoria === 'curiosidades' ? 'selected' : '' ?>>Informações & Curiosidades</option>
        </select>
      </div>

      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-dark rounded-pill px-4 flex-fill">
          Filtrar
        </button>
        <?php if (!empty($busca) || !empty($filtroCategoria)): ?>
          <a href="index.php" class="btn btn-outline-secondary rounded-pill" title="Limpar Filtros">
            <i class="bi bi-x-lg"></i>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Tabela de Publicações (CRUD) -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #FFFFFF;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 70px;">ID</th>
            <th style="width: 80px;">Imagem</th>
            <th>Título da Publicação</th>
            <th style="width: 160px;">Categoria</th>
            <th style="width: 180px;">Autor</th>
            <th style="width: 130px;">Data</th>
            <th style="width: 160px;" class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
              <tr>
                <td class="fw-bold text-muted">#<?= (int)$post['id'] ?></td>
                <td>
                  <?php 
                    $thumbSrc = !empty($post['imagem']) ? '../' . htmlspecialchars($post['imagem']) : '../assets/img/samples/una_somos.svg';
                  ?>
                  <img src="<?= $thumbSrc ?>" alt="Capa" class="rounded border" style="width: 50px; height: 50px; object-fit: contain; background: #FFF;">
                </td>
                <td>
                  <div class="fw-bold text-dark mb-1">
                    <?= htmlspecialchars($post['titulo']) ?>
                  </div>
                  <?php if (!empty($post['nome_perfume'])): ?>
                    <span class="badge bg-light text-dark border">
                      <i class="bi bi-flower1 me-1 text-primary"></i> <?= htmlspecialchars($post['nome_perfume']) ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($post['categoria'] === 'noticias'): ?>
                    <span class="badge rounded-pill text-dark" style="background: var(--sillage-sage-light);">Notícias / Lançamento</span>
                  <?php elseif ($post['categoria'] === 'resenhas'): ?>
                    <span class="badge rounded-pill text-dark" style="background: var(--sillage-champagne);">Resenha</span>
                  <?php else: ?>
                    <span class="badge rounded-pill text-dark" style="background: var(--sillage-lilac);">Curiosidade</span>
                  <?php endif; ?>
                </td>
                <td class="small text-muted">
                  <i class="bi bi-person me-1"></i> <?= htmlspecialchars($post['autor_nome'] ?? 'Kauã & Yasmin') ?>
                </td>
                <td class="small text-muted">
                  <?= date('d/m/Y', strtotime($post['data_criacao'])) ?>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="../post.php?id=<?= (int)$post['id'] ?>" target="_blank" class="btn btn-outline-secondary" title="Visualizar Post Público">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="editar_post.php?id=<?= (int)$post['id'] ?>" class="btn btn-outline-primary" title="Editar Publicação">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="excluir_post.php?id=<?= (int)$post['id'] ?>&csrf_token=<?= generate_csrf_token() ?>" 
                       class="btn btn-outline-danger btn-confirm-delete" 
                       data-title="<?= htmlspecialchars($post['titulo']) ?>"
                       title="Excluir Definitivamente">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                Nenhuma publicação encontrada para os critérios selecionados.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
