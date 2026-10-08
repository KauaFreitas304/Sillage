<?php
/**
 * Sillage - Editar Postagem Existente (RF06)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$adminPageTitle = 'Editar Publicação — Sillage ADM';
require_once __DIR__ . '/admin_header.php';

$pdo = getDBConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'Publicação não encontrada.');
    header("Location: index.php");
    exit;
}

$error = '';

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
            // Processamento de substituição de imagem (se enviada nova)
            $imagemPath = $post['imagem'];
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
                        $error = 'Não foi possível salvar a nova imagem no servidor.';
                    }
                } else {
                    $error = 'Formato de imagem inválido. Formatos permitidos: JPG, PNG, WEBP ou SVG.';
                }
            }

            if (empty($error)) {
                $stmtUpdate = $pdo->prepare("UPDATE posts SET 
                    titulo = :titulo,
                    categoria = :categoria,
                    conteudo = :conteudo,
                    imagem = :imagem,
                    nome_perfume = :nome_perfume,
                    concentracao = :concentracao,
                    familia_olfativa = :familia_olfativa,
                    notas_topo = :notas_topo,
                    notas_corpo = :notas_corpo,
                    notas_fundo = :notas_fundo,
                    clima = :clima,
                    referencias = :referencias
                    WHERE id = :id
                ");

                $stmtUpdate->execute([
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
                    ':id'               => $id
                ]);

                set_flash('success', 'Publicação atualizada com sucesso!');
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
            Editar Publicação #<?= (int)$post['id'] ?>
          </h1>
          <p class="text-muted small mb-0">Atualize os campos ou imagem de capa associada.</p>
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

      <form action="editar_post.php?id=<?= (int)$post['id'] ?>" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4 p-4 p-md-5" style="background: #FFFFFF;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <!-- Bloco 1: Informações Gerais -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-file-earmark-text me-2"></i> 1. Dados Principais
        </h5>

        <div class="mb-3">
          <label for="titulo" class="form-label fw-semibold">Título da Publicação *</label>
          <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= htmlspecialchars($post['titulo']) ?>">
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label for="categoria" class="form-label fw-semibold">Categoria Editorial *</label>
            <select name="categoria" id="categoria" class="form-select" required>
              <option value="noticias" <?= ($post['categoria'] === 'noticias') ? 'selected' : '' ?>>Notícias & Lançamentos</option>
              <option value="resenhas" <?= ($post['categoria'] === 'resenhas') ? 'selected' : '' ?>>Resenhas Críticas</option>
              <option value="curiosidades" <?= ($post['categoria'] === 'curiosidades') ? 'selected' : '' ?>>Informações & Curiosidades (Sidebar)</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="imagem" class="form-label fw-semibold">Substituir Imagem de Capa</label>
            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
            <?php if (!empty($post['imagem'])): ?>
              <div class="mt-2 d-flex align-items-center gap-2 small text-muted">
                <span>Imagem atual:</span>
                <img src="../<?= htmlspecialchars($post['imagem']) ?>" alt="Atual" height="36" class="border rounded">
              </div>
            <?php endif; ?>
          </div>
        </div>

        <hr class="my-4">

        <!-- Bloco 2: Ficha Técnica Olfativa & Pirâmide (RF06) -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-flower1 me-2"></i> 2. Pirâmide Olfativa & Estrutura Sensorial
        </h5>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="nome_perfume" class="form-label fw-semibold">Nome do Perfume</label>
            <input type="text" name="nome_perfume" id="nome_perfume" class="form-control" value="<?= htmlspecialchars($post['nome_perfume'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="concentracao" class="form-label fw-semibold">Concentração</label>
            <input type="text" name="concentracao" id="concentracao" class="form-control" value="<?= htmlspecialchars($post['concentracao'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="familia_olfativa" class="form-label fw-semibold">Família Olfativa</label>
            <input type="text" name="familia_olfativa" id="familia_olfativa" class="form-control" value="<?= htmlspecialchars($post['familia_olfativa'] ?? '') ?>">
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="notas_topo" class="form-label fw-semibold">Notas de Topo (Saída)</label>
            <input type="text" name="notas_topo" id="notas_topo" class="form-control" value="<?= htmlspecialchars($post['notas_topo'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="notas_corpo" class="form-label fw-semibold">Notas de Coração (Corpo)</label>
            <input type="text" name="notas_corpo" id="notas_corpo" class="form-control" value="<?= htmlspecialchars($post['notas_corpo'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label for="notas_fundo" class="form-label fw-semibold">Notas de Base (Fundo)</label>
            <input type="text" name="notas_fundo" id="notas_fundo" class="form-control" value="<?= htmlspecialchars($post['notas_fundo'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label for="clima" class="form-label fw-semibold">Clima / Ocasião Ideal</label>
          <input type="text" name="clima" id="clima" class="form-control" value="<?= htmlspecialchars($post['clima'] ?? '') ?>">
        </div>

        <hr class="my-4">

        <!-- Bloco 3: Conteúdo Formatado (Rich Text) -->
        <h5 class="fw-bold mb-3" style="color: var(--sillage-darkgreen);">
          <i class="bi bi-pencil-square me-2"></i> 3. Conteúdo Formatado (Rich Text) *
        </h5>

        <div class="btn-toolbar mb-2 gap-1" role="toolbar" aria-label="Editor Toolbar">
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('bold')" title="Negrito"><strong>B</strong></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('italic')" title="Itálico"><em>I</em></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('insertUnorderedList')" title="Lista"><i class="bi bi-list-ul"></i></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('insertOrderedList')" title="Lista Numerada"><i class="bi bi-list-ol"></i></button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertHeading()" title="Subtítulo">H3</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertLink()" title="Inserir Link"><i class="bi bi-link-45deg"></i></button>
        </div>

        <div id="editor" contenteditable="true" class="form-control p-3 mb-2" style="min-height: 250px; max-height: 500px; overflow-y: auto; background-color: #FAFBF9; border-color: #CED4DA;">
          <?= $post['conteudo'] ?>
        </div>
        <textarea name="conteudo" id="conteudo-hidden" style="display:none;"></textarea>

        <hr class="my-4">

        <!-- Bloco 4: Referências -->
        <div class="mb-4">
          <label for="referencias" class="form-label fw-semibold">
            <i class="bi bi-journal-bookmark me-1"></i> Referências & Fontes Bibliográficas
          </label>
          <textarea name="referencias" id="referencias" rows="3" class="form-control"><?= htmlspecialchars($post['referencias'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-3">
          <a href="index.php" class="btn btn-light rounded-pill px-4">Cancelar</a>
          <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
            <i class="bi bi-check-lg me-1"></i> Salvar Alterações
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

document.querySelector('form').addEventListener('submit', function() {
  const editorContent = document.getElementById('editor').innerHTML;
  document.getElementById('conteudo-hidden').value = editorContent;
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
