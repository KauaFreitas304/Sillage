<?php
/**
 * Sillage - Login Administrativo (RF05 / Wireframe 5)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';

// Se já estiver logado, redireciona diretamente ao painel
if (is_logged_in()) {
    header("Location: admin/index.php");
    exit;
}

$error = get_flash('error');
$success = get_flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $csrf  = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = 'Requisição inválida ou token de segurança expirado. Tente novamente.';
    } elseif (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha o e-mail e a senha.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios_adm WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            // Autenticação bem-sucedida (RNF01)
            session_regenerate_id(true);
            $_SESSION['adm_id'] = $user['id'];
            $_SESSION['adm_nome'] = $user['nome'];
            $_SESSION['adm_email'] = $user['email'];

            header("Location: admin/index.php");
            exit;
        } else {
            $error = 'Credenciais incorretas. Verifique seu e-mail e senha.';
        }
    }
}

$csrfToken = generate_csrf_token();
$customTitle = 'Login de Cadastro (ADM) — Sillage';
$headerBadge = 'Área Restrita';
include __DIR__ . '/header.php';
?>

<main class="sillage-login-wrapper">
  
  <div class="container">
    <div class="row align-items-center justify-content-center g-5">
      
      <!-- Lado Esquerdo: Título da Página Conforme Wireframe 5 -->
      <div class="col-md-5 text-center text-md-start">
        <h1 class="display-5 fw-bold" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
          Login de cadastro<br>(ADM)
        </h1>
        <p class="text-muted mt-3">
          Área restrita aos administradores e curadores editoriais do portal Sillage.
        </p>
        <div class="alert alert-info py-2 px-3 small d-inline-block mt-2">
          <i class="bi bi-info-circle me-1"></i> <strong>Acesso Inicial:</strong> admin@blog.com (ou admin@sillage.com) | admin123
        </div>
      </div>

      <!-- Lado Direito: Card Lilás Conforme Wireframe 5 -->
      <div class="col-md-5 d-flex justify-content-center">
        
        <div class="sillage-login-card-lilac">
          <h2>Bem vindo<br>ADM!</h2>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 small text-start rounded-3 mb-3">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2 px-3 small text-start rounded-3 mb-3">
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>

          <form action="login.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="text-start mb-2">
              <input type="email" name="email" id="login-email" class="sillage-login-input" 
                     placeholder="Username / E-mail" required autofocus 
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="text-start mb-1">
              <input type="password" name="senha" id="login-password" class="sillage-login-input" 
                     placeholder="Password" required>
            </div>

            <a href="javascript:void(0)" onclick="alert('Para redefinição de credenciais de ADM, consulte Kauã Freitas ou Yasmin Cristiny.');" class="sillage-login-forgot">
              Forgot Password?
            </a>

            <div>
              <button type="submit" class="sillage-btn-login" id="btn-submit-login">
                Login
              </button>
            </div>
          </form>

        </div>

      </div>

    </div>
  </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>
