<?php
/**
 * Sillage - Criar Postagem (RF06)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$adminPageTitle = 'Nova Publicação — Sillage ADM';
require_once __DIR__ . '/admin_header.php';

$pdo = getDBConnection();
$error = '';
$currentUser = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        $error = 'Token de segurança CSRF inválido ou expirado. Tente novamente.';
    } else {
        $titulo           = trim($_POST['titulo'] ?? '');
        $categoria        = trim($_POST['categoria'] ?? 'noticias');
        $conteudo         = trim($_POST['conteudo'] ?? '');
        $nome_perfume     = trim($_POST['nome_perfume'] ?? '');
        $concentracao     = trim($_POST['concentracao'] ?? '');
        $familia_olfativa = trim($_POST['familia_olfativa'] ?? '');
        $notas_topo       = trim($_POST['notas_topo'] ?? '');
        $notas_corpo      = trim($_POST['notas_corpo'] ?? '');
        $notas_fundo      = trim($_POST['notas_fundo'] ?? '');
        $clima            = trim($_POST['clima'] ?? '');
        $referencias      = trim($_POST['referencias'] ?? '');

        if (empty($titulo) || empty($conteudo)) {
            $error = 'Por favor, preencha o Título e o Conteúdo da publicação.';
        } elseif (!in_array($categoria, ['noticias', 'resenhas', 'curiosidades'])) {
            $error = 'Categoria inválida selecionada.';
        } else {
            // Processamento do upload da imagem de capa (se enviada)
            $imagemPath = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['imagem']['tmp_name'];
                $fileName = $_FILES['imagem']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

                if (in_array($fileExt, $allowedExts)) {
                    $uploadDir = __DIR__ . '/../assets/img/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $newFileName = 'perfume_' . uniqid() . '.' . $fileExt;
                    $targetPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        $imagemPath = 'assets/img/uploads/' . $newFileName;
                    } else {
                        $error = 'Não foi possível salvar a imagem no servidor.';
                    }
                } else {
                    $error = 'Formato de imagem inválido. Formatos permitidos: JPG, PNG, WEBP ou SVG.';
                }
            } else {
                // Caso não tenha feito upload, atribui imagem padrão de amostra
                $imagemPath = 'assets/img/samples/una_somos.svg';
            }

            if (empty($error)) {
                $stmt = $pdo->prepare("INSERT INTO posts (
                    titulo, categoria, conteudo, imagem,
                    nome_perfume, concentracao, familia_olfativa,
                    notas_topo, notas_corpo, notas_fundo, clima, referencias,
                    autor_id, data_criacao
                ) VALUES (
                    :titulo, :categoria, :conteudo, :imagem,
                    :nome_perfume, :concentracao, :familia_olfativa,
                    :notas_topo, :notas_corpo, :notas_fundo, :clima, :referencias,
                    :autor_id, NOW()
                )");

                $stmt->execute([
                    ':titulo'           => $titulo,
                    ':categoria'        => $categoria,
                    ':conteudo'         => $conteudo,
                    ':imagem'           => $imagemPath,
                    ':nome_perfume'     => !empty($nome_perfume) ? $nome_perfume : null,
                    ':concentracao'     => !empty($concentracao) ? $concentracao : null,
                    ':familia_olfativa' => !empty($familia_olfativa) ? $familia_olfativa : null,
                    ':notas_topo'       => !empty($notas_topo) ? $notas_topo : null,
                    ':notas_corpo'      => !empty($notas_corpo) ? $notas_corpo : null,
                    ':notas_fundo'      => !empty($notas_fundo) ? $notas_fundo : null,
                    ':clima'            => !empty($clima) ? $clima : null,
                    ':referencias'      => !empty($referencias) ? $referencias : null,
                    ':autor_id'         => $currentUser['id']
                ]);

                set_flash('success', 'Publicação cadastrada com sucesso!');
                header("Location: index.php");
                exit;
            }
        }
    }
}

$csrfToken = generate_csrf_token();
?>

<main class="container py-4">
  
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color: var(--sillage-darkgreen);">
            Criar Nova Publicação
          </h1>
          <p class="text-muted small mb-0">Preencha os campos abaixo para disponibilizar um novo post no Sillage.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3">
          <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm mb-4">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="criar_post.php" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4 p-4 p-md-5" style="background: #FFFFFF;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <!-- Bloco 1: Informações Gerais -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-file-earmark-text me-2"></i> 1. Dados Principais
        </h5>

        <div class="mb-3">
          <label for="titulo" class="form-label fw-semibold">Título da Publicação *</label>
          <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ex: Natura Una Somos: A Potência do Floral Amadeirado Brasileiro" required value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label for="categoria" class="form-label fw-semibold">Categoria Editorial *</label>
            <select name="categoria" id="categoria" class="form-select" required>
              <option value="noticias" <?= (isset($_POST['categoria']) && $_POST['categoria'] === 'noticias') ? 'selected' : '' ?>>Notícias & Lançamentos</option>
              <option value="resenhas" <?= (isset($_POST['categoria']) && $_POST['categoria'] === 'resenhas') ? 'selected' : '' ?>>Resenhas Críticas</option>
              <option value="curiosidades" <?= (isset($_POST['categoria']) && $_POST['categoria'] === 'curiosidades') ? 'selected' : '' ?>>Informações & Curiosidades (Sidebar)</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="imagem" class="form-label fw-semibold">Imagem de Capa / Ilustrativa</label>
            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
            <div class="form-text small">Formatos: JPG, PNG, WEBP ou SVG. Se vazio, uma arte elegante será atribuída.</div>
          </div>
        </div>

        <hr class="my-4">

        <!-- Bloco 2: Ficha Técnica Olfativa & Pirâmide (RF06) -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-flower1 me-2"></i> 2. Pirâmide Olfativa & Estrutura Sensorial <span class="text-muted fw-normal small">(Opcional para curiosidades)</span>
        </h5>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="nome_perfume" class="form-label fw-semibold">Nome do Perfume</label>
            <input type="text" name="nome_perfume" id="nome_perfume" class="form-control" placeholder="Ex: Una Somos" value="<?= htmlspecialchars($_POST['nome_perfume'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="concentracao" class="form-label fw-semibold">Concentração</label>
            <input type="text" name="concentracao" id="concentracao" class="form-control" placeholder="Ex: Eau de Parfum, EDT, Parfum" value="<?= htmlspecialchars($_POST['concentracao'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="familia_olfativa" class="form-label fw-semibold">Família Olfativa</label>
            <input type="text" name="familia_olfativa" id="familia_olfativa" class="form-control" placeholder="Ex: Floral Amadeirado, Âmbar Gourmand" value="<?= htmlspecialchars($_POST['familia_olfativa'] ?? '') ?>">
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="notas_topo" class="form-label fw-semibold">Notas de Topo (Saída)</label>
            <input type="text" name="notas_topo" id="notas_topo" class="form-control" placeholder="Ex: Pimenta Rosa, Bergamota, Mandarina" value="<?= htmlspecialchars($_POST['notas_topo'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="notas_corpo" class="form-label fw-semibold">Notas de Coração (Corpo)</label>
            <input type="text" name="notas_corpo" id="notas_corpo" class="form-control" placeholder="Ex: Íris, Jasmim Sambac, Rosa Damascena" value="<?= htmlspecialchars($_POST['notas_corpo'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="notas_fundo" class="form-label fw-semibold">Notas de Base (Fundo)</label>
            <input type="text" name="notas_fundo" id="notas_fundo" class="form-control" placeholder="Ex: Breu Branco, Patchouli, Baunilha" value="<?= htmlspecialchars($_POST['notas_fundo'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label for="clima" class="form-label fw-semibold">Clima / Ocasião Ideal</label>
          <input type="text" name="clima" id="clima" class="form-control" placeholder="Ex: Outono/Inverno, noites elegantes e dias amenos" value="<?= htmlspecialchars($_POST['clima'] ?? '') ?>">
        </div>

        <hr class="my-4">

        <!-- Bloco 3: Conteúdo Formatado (Rich Text) -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-pencil-square me-2"></i> 3. Conteúdo Formatado (Rich Text) *
        </h5>

        <!-- Barra de ferramentas simples para formatação rápida -->
        <div class="btn-toolbar mb-2 gap-1" role="toolbar" aria-label="Editor Toolbar">
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('bold')" title="Negrito"><strong>B</strong></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('italic')" title="Itálico"><em>I</em></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('insertUnorderedList')" title="Lista"><i class="bi bi-list-ul"></i></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('insertOrderedList')" title="Lista Numerada"><i class="bi bi-list-ol"></i></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertHeading()" title="Subtítulo">H3</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertLink()" title="Inserir Link"><i class="bi bi-link-45deg"></i></button>
        </div>

        <!-- Área editável visualmente -->
        <div id="editor" contenteditable="true" class="form-control p-3 mb-2" style="min-height: 250px; max-height: 500px; overflow-y: auto; background-color: #FAFBF9; border-color: #CED4DA;">
          <?= $_POST['conteudo'] ?? '<p>Escreva o conteúdo educativo ou análise sensorial do perfume aqui...</p>' ?>
        </div>
        <!-- Textarea oculto que sincroniza com o formulário POST -->
        <textarea name="conteudo" id="conteudo-hidden" style="display:none;"></textarea>

        <hr class="my-4">

        <!-- Bloco 4: Referências Bibliográficas -->
        <div class="mb-4">
          <label for="referencias" class="form-label fw-semibold">
            <i class="bi bi-journal-bookmark me-1"></i> Referências & Fontes Bibliográficas
          </label>
          <textarea name="referencias" id="referencias" rows="3" class="form-control" placeholder="Ex: Jean-Claude Ellena, O Perfume; Fragrantica; Documentação oficial da Casa de Perfumaria."><?= htmlspecialchars($_POST['referencias'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-3">
          <a href="index.php" class="btn btn-light rounded-pill px-4">Cancelar</a>
          <button type="submit" id="btn-salvar-post" class="btn btn-primary rounded-pill px-5 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
            <i class="bi bi-check-lg me-1"></i> Publicar Post
          </button>
        </div>

      </form>

    </div>
  </div>

</main>

<script>
function formatDoc(cmd, val = null) {
  document.execCommand(cmd, false, val);
  document.getElementById('editor').focus();
}

function insertHeading() {
  document.execCommand('formatBlock', false, '<h3>');
  document.getElementById('editor').focus();
}

function insertLink() {
  const url = prompt('Digite o endereço do link (URL):', 'https://');
  if (url) {
    document.execCommand('createLink', false, url);
  }
}

// Sincroniza o editor visual com o textarea no submit
document.querySelector('form').addEventListener('submit', function() {
  const editorContent = document.getElementById('editor').innerHTML;
  document.getElementById('conteudo-hidden').value = editorContent;
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
