<?php
/**
 * Sillage - Página de Resenhas (RF02 / Wireframe 3)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 * Refatorado com Layout em Grid Editorial Leve, Padronização e Filtros Rápidos
 */

require_once __DIR__ . '/config/db.php';
initializeDatabase();
$pdo = getDBConnection();

// Busca postagens da categoria 'resenhas'
$stmtResenhas = $pdo->query("SELECT * FROM posts WHERE categoria = 'resenhas' ORDER BY data_criacao DESC, id DESC");
$resenhas = $stmtResenhas->fetchAll();

/**
 * Extrai de forma inteligente a marca do perfume a partir dos metadados
 */
function extrairMarcaResenha(array $post): string {
    if (!empty($post['marca'])) {
        return (string)$post['marca'];
    }
    $marcasConhecidas = [
        'Natura', 'Paco Rabanne', 'Dior', 'Chanel', 'Yves Saint Laurent', 'YSL', 
        'Tom Ford', 'Valentino', 'Giorgio Armani', 'Armani', 'Carolina Herrera', 
        'Versace', 'Jean Paul Gaultier', 'Lancôme', 'Gucci', 'Prada', 'Hermès', 
        'Givenchy', 'Bvlgari', 'Dolce & Gabbana', 'Guerlain', 'Creed', 'Dove',
        'O Boticário', 'Boticário', 'Granado', 'Eudora', 'Jo Malone', 'Mugler',
        'Calvin Klein', 'Hugo Boss', 'Burberry', 'Kenzo', 'Chloé', 'Marc Jacobs'
    ];
    $texto = ($post['nome_perfume'] ?? '') . ' ' . ($post['titulo'] ?? '');
    foreach ($marcasConhecidas as $marca) {
        if (stripos($texto, $marca) !== false) {
            return $marca;
        }
    }
    $nome = !empty($post['nome_perfume']) ? $post['nome_perfume'] : $post['titulo'];
    $limpo = trim(preg_replace('/^Resenha:\s*/i', '', (string)$nome));
    $partes = explode(' ', $limpo);
    return !empty($partes[0]) ? $partes[0] : 'Sillage';
}

$customTitle = 'Sillage — Resenhas Críticas e Análises Sensoriais';
$headerBadge = 'Resenhas';
include __DIR__ . '/header.php';
?>

<main class="container-fluid py-5" style="background-color: var(--sillage-offwhite); min-height: 85vh;">
  
  <div class="container" style="max-width: 1200px;">
    
    <!-- Cabeçalho da Página -->
    <div class="text-center mb-4">
      <h1 class="display-5 fw-bold" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
        Resenhas Aprofundadas
      </h1>
      <p class="text-muted lead" style="max-width: 720px; margin: 0 auto;">
        Análises sensoriais completas sobre fixação, projeção, silagem e evolução olfativa na pele real.
      </p>
    </div>

    <!-- Barra de Filtros e Busca Rápida (Requisito 5) -->
    <section class="sillage-filter-toolbar" aria-label="Filtros e Busca de Resenhas">
      <div class="row g-3 align-items-center">
        
        <!-- Barra de Pesquisa por Texto (Nome ou Marca) -->
        <div class="col-lg-5 col-md-6">
          <div class="sillage-search-input-wrapper">
            <i class="bi bi-search sillage-search-icon"></i>
            <input type="text" 
                   id="reviewSearchInput" 
                   class="form-control sillage-search-input" 
                   placeholder="Buscar por perfume, marca ou família..." 
                   aria-label="Buscar resenhas por perfume, marca ou notas">
            <button type="button" id="reviewSearchClear" class="btn btn-sm sillage-search-clear d-none" aria-label="Limpar termo de busca">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <!-- Botões de Filtro Rápido por Clima ou Categoria -->
        <div class="col-lg-7 col-md-6">
          <div class="sillage-quick-filters justify-content-md-end">
            <span class="text-muted small fw-semibold me-1 d-none d-sm-inline">Filtro Rápido:</span>
            <button type="button" class="btn sillage-filter-btn active" data-filter="todos">
              Todos <span class="badge rounded-pill count-badge ms-1"><?= count($resenhas) ?></span>
            </button>
            <button type="button" class="btn sillage-filter-btn" data-filter="frio">
              <i class="bi bi-snow me-1 text-primary"></i> Frio / Inverno
            </button>
            <button type="button" class="btn sillage-filter-btn" data-filter="calor">
              <i class="bi bi-sun me-1 text-warning"></i> Primavera / Calor
            </button>
            <button type="button" class="btn sillage-filter-btn" data-filter="amadeirado">
              <i class="bi bi-tree me-1 text-success"></i> Amadeirado
            </button>
            <button type="button" class="btn sillage-filter-btn" data-filter="floral">
              <i class="bi bi-flower1 me-1 text-danger"></i> Floral
            </button>
          </div>
        </div>

      </div>

      <!-- Barra de Status e Contagem Dinâmica -->
      <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-10 small text-muted">
        <div id="resultsCount">
          Exibindo <strong><?= count($resenhas) ?></strong> resenha(s)
        </div>
        <div id="activeFilterLabel" class="fw-semibold text-secondary" style="font-size: 0.8rem;">
          Todas as resenhas
        </div>
      </div>
    </section>

    <!-- Grid Responsivo de 3 Colunas com Cards Leves (Requisitos 1, 2, 3 e 4) -->
    <div class="sillage-reviews-grid" id="reviewsGrid">
      <?php if (!empty($resenhas)): ?>
        <?php foreach ($resenhas as $resenha): 
          $imgSrc = !empty($resenha['imagem']) ? htmlspecialchars($resenha['imagem']) : 'assets/img/samples/una_somos.svg';
          $perfumeNome = !empty($resenha['nome_perfume']) ? $resenha['nome_perfume'] : $resenha['titulo'];
          $marca = extrairMarcaResenha($resenha);
          $familia = $resenha['familia_olfativa'] ?? 'Floral Amadeirado';
          $concentracao = $resenha['concentracao'] ?? 'Eau de Parfum';
          $clima = $resenha['clima'] ?? '';
        ?>
          <article class="sillage-review-grid-card" 
                   id="resenha-<?= (int)$resenha['id'] ?>"
                   data-titulo="<?= htmlspecialchars(mb_strtolower($resenha['titulo'])) ?>"
                   data-nome="<?= htmlspecialchars(mb_strtolower($perfumeNome)) ?>"
                   data-marca="<?= htmlspecialchars(mb_strtolower($marca)) ?>"
                   data-familia="<?= htmlspecialchars(mb_strtolower($familia)) ?>"
                   data-clima="<?= htmlspecialchars(mb_strtolower($clima)) ?>"
                   data-concentracao="<?= htmlspecialchars(mb_strtolower($concentracao)) ?>">
            
            <!-- Padronização de Imagens: Altura Fixa 240px, Fundo Branco Puro e object-fit: contain (Requisito 3) -->
            <div class="sillage-review-img-box">
              <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($perfumeNome) ?>" loading="lazy">
            </div>

            <!-- Conteúdo Enxuto na Vitrine (Requisito 4) -->
            <div class="sillage-review-card-body">
              
              <!-- Tag/Badge com a Marca -->
              <div class="mb-2">
                <span class="sillage-brand-pill">
                  <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($marca) ?>
                </span>
              </div>

              <!-- Título do Perfume em Verde-Musgo -->
              <h2 class="sillage-review-card-title">
                <a href="post.php?id=<?= (int)$resenha['id'] ?>" title="Ler resenha completa de <?= htmlspecialchars($perfumeNome) ?>">
                  <?= htmlspecialchars($perfumeNome) ?>
                </a>
              </h2>

              <!-- Ficha Olfativa Enxuta: Família, Concentração e Clima Ideal -->
              <div class="sillage-review-meta-group">
                <div class="sillage-review-meta-row">
                  <span class="meta-label"><i class="bi bi-droplet-half me-1"></i> Família Olfativa</span>
                  <span class="meta-value"><?= htmlspecialchars($familia) ?></span>
                </div>
                
                <div class="sillage-review-meta-row">
                  <span class="meta-label"><i class="bi bi-award me-1"></i> Concentração</span>
                  <span class="meta-value"><?= htmlspecialchars($concentracao) ?></span>
                </div>

                <?php if (!empty($clima)): ?>
                  <div class="sillage-review-meta-row">
                    <span class="meta-label"><i class="bi bi-sun me-1"></i> Clima Ideal</span>
                    <span class="meta-value"><?= htmlspecialchars($clima) ?></span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Rodapé com Data e Botão 'Ler Resenha' em Verde-Musgo -->
              <div class="sillage-review-card-footer">
                <span class="sillage-review-card-date">
                  <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($resenha['data_criacao'])) ?>
                </span>
                <a href="post.php?id=<?= (int)$resenha['id'] ?>" class="sillage-review-btn" id="btn-resenha-<?= (int)$resenha['id'] ?>">
                  Ler Resenha <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>

            </div>

          </article>
        <?php endforeach; ?>

        <!-- Empty State para Busca / Filtros sem Resultados -->
        <div id="emptyReviewsSearch" class="d-none w-100 py-5 text-center" style="grid-column: 1 / -1;">
          <div class="p-5 bg-white rounded-4 border" style="border-color: #D5D8CE !important; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
            <h4 class="fw-bold" style="color: #4A5848;">Nenhuma resenha encontrada</h4>
            <p class="text-muted small mb-3">Tente pesquisar por outro termo, marca ou remover o filtro rápido selecionado.</p>
            <button type="button" class="btn sillage-filter-btn" id="resetFiltersBtn">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Limpar Filtros
            </button>
          </div>
        </div>

      <?php else: ?>
        <div class="alert alert-light text-center py-5 w-100" style="grid-column: 1 / -1; border: 1px solid #D5D8CE;">
          <i class="bi bi-journal-x fs-1 text-muted mb-2 d-block"></i>
          <h5>Nenhuma resenha cadastrada no momento</h5>
          <p class="small text-muted mb-0">Novas resenhas serão adicionadas pela equipe editorial em breve.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>

</main>

<!-- Script de Busca Interativa e Filtros Rápidos em Tempo Real (Requisito 5) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('reviewSearchInput');
  const clearBtn = document.getElementById('reviewSearchClear');
  const filterBtns = document.querySelectorAll('.sillage-filter-btn');
  const cards = document.querySelectorAll('.sillage-review-grid-card');
  const countEl = document.getElementById('resultsCount');
  const activeLabel = document.getElementById('activeFilterLabel');
  const emptySearch = document.getElementById('emptyReviewsSearch');
  const resetBtn = document.getElementById('resetFiltersBtn');

  let currentFilter = 'todos';
  let searchQuery = '';

  function applyFilters() {
    let visibleCount = 0;
    const query = searchQuery.toLowerCase().trim();

    cards.forEach(card => {
      const title = card.getAttribute('data-titulo') || '';
      const name = card.getAttribute('data-nome') || '';
      const brand = card.getAttribute('data-marca') || '';
      const family = card.getAttribute('data-familia') || '';
      const climate = card.getAttribute('data-clima') || '';
      const conc = card.getAttribute('data-concentracao') || '';

      const searchableText = `${title} ${name} ${brand} ${family} ${climate} ${conc}`.toLowerCase();
      const matchesSearch = !query || searchableText.includes(query);

      let matchesFilter = true;
      if (currentFilter === 'frio') {
        matchesFilter = climate.includes('frio') || climate.includes('inverno') || climate.includes('noite') || climate.includes('amen');
      } else if (currentFilter === 'calor') {
        matchesFilter = climate.includes('verão') || climate.includes('verao') || climate.includes('primavera') || climate.includes('quente') || climate.includes('livre') || climate.includes('tarde');
      } else if (currentFilter === 'amadeirado') {
        matchesFilter = family.includes('amadeirad') || searchableText.includes('amadeirad');
      } else if (currentFilter === 'floral') {
        matchesFilter = family.includes('floral') || searchableText.includes('floral');
      }

      if (matchesSearch && matchesFilter) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (countEl) {
      countEl.innerHTML = `Exibindo <strong>${visibleCount}</strong> de <strong>${cards.length}</strong> resenha(s)`;
    }

    if (emptySearch) {
      if (visibleCount === 0 && cards.length > 0) {
        emptySearch.classList.remove('d-none');
      } else {
        emptySearch.classList.add('d-none');
      }
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      searchQuery = this.value;
      if (clearBtn) {
        clearBtn.classList.toggle('d-none', !this.value);
      }
      applyFilters();
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      if (searchInput) {
        searchInput.value = '';
        searchQuery = '';
        this.classList.add('d-none');
        searchInput.focus();
        applyFilters();
      }
    });
  }

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.getAttribute('data-filter') || 'todos';
      if (activeLabel) {
        activeLabel.textContent = this.textContent.trim().replace(/\s*\d+$/, '');
      }
      applyFilters();
    });
  });

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (searchInput) {
        searchInput.value = '';
        searchQuery = '';
      }
      if (clearBtn) {
        clearBtn.classList.add('d-none');
      }
      currentFilter = 'todos';
      filterBtns.forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-filter') === 'todos');
      });
      if (activeLabel) {
        activeLabel.textContent = 'Todas as resenhas';
      }
      applyFilters();
    });
  }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
