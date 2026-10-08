<?php
/**
 * Sillage - Cadastro de Novos Administradores (RF05)
 * Restrito exclusivamente a administradores já autenticados
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$adminPageTitle = 'Cadastrar Novo Administrador — Sillage ADM';
require_once __DIR__ . '/admin_header.php';

$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        $error = 'Token de segurança CSRF inválido ou expirado. Tente novamente.';
    } else {
        $nome       = trim($_POST['nome'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $senha      = $_POST['senha'] ?? '';
        $confSenha  = $_POST['confirma_senha'] ?? '';

        // Validação dos Campos Obrigatórios (RF05)
        if (empty($nome) || empty($email) || empty($senha) || empty($confSenha)) {
            $error = 'Todos os campos são obrigatórios: Nome Completo, E-mail, Senha e Confirmação de Senha.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor, insira um endereço de e-mail válido.';
        } elseif (strlen($senha) < 6) {
            $error = 'Por segurança, a senha deve conter pelo menos 6 caracteres.';
        } elseif ($senha !== $confSenha) {
            $error = 'A Senha e a Confirmação de Senha não coincidem.';
        } else {
            // Verifica se o e-mail já existe
            $stmtCheck = $pdo->prepare("SELECT id FROM usuarios_adm WHERE email = :email LIMIT 1");
            $stmtCheck->execute([':email' => $email]);
            if ($stmtCheck->fetch()) {
                $error = 'Já existe um administrador cadastrado com este e-mail.';
            } else {
                // Criptografia com Bcrypt (RNF01)
                $hashedPassword = password_hash($senha, PASSWORD_BCRYPT);

                $stmtInsert = $pdo->prepare("INSERT INTO usuarios_adm (nome, email, senha, data_cadastro) VALUES (:nome, :email, :senha, NOW())");
                $stmtInsert->execute([
                    ':nome'  => $nome,
                    ':email' => $email,
                    ':senha' => $hashedPassword
                ]);

                set_flash('success', "Novo administrador \"{$nome}\" cadastrado com sucesso!");
                header("Location: usuarios.php");
                exit;
            }
        }
    }
}

$csrfToken = generate_csrf_token();
?>

<main class="container py-4">
  
  <div class="row justify-content-center">
    <div class="col-lg-7">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color: var(--sillage-darkgreen);">
            Cadastrar Novo Administrador
          </h1>
          <p class="text-muted small mb-0">Disponível exclusivamente para administradores autenticados (RF05).</p>
        </div>
        <a href="usuarios.php" class="btn btn-outline-secondary rounded-pill px-3">
          <i class="bi bi-people me-1"></i> Lista de ADMs
        </a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger shadow-sm mb-4">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="cadastrar_adm.php" method="POST" autocomplete="off" class="card border-0 shadow-sm rounded-4 p-4 p-md-5" style="background: #FFFFFF;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="mb-3">
          <label for="nome" class="form-label fw-semibold">Nome Completo *</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
            <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Maria Alice Silveira" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">E-mail *</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control" placeholder="nome@sillage.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label for="senha" class="form-label fw-semibold">Senha *</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
              <input type="password" name="senha" id="senha" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="form-text small">Criptografada via Bcrypt (RNF01).</div>
          </div>

          <div class="col-md-6">
            <label for="confirma_senha" class="form-label fw-semibold">Confirmação de Senha *</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
              <input type="password" name="confirma_senha" id="confirma_senha" class="form-control" placeholder="Repita a senha" required>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
          <a href="index.php" class="btn btn-light rounded-pill px-4">Cancelar</a>
          <button type="submit" id="btn-cadastrar-adm" class="btn btn-primary rounded-pill px-5 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
            <i class="bi bi-person-check-fill me-1"></i> Cadastrar Administrador
          </button>
        </div>

      </form>

    </div>
  </div>

</main>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
