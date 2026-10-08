<?php
/**
 * Sillage - Listagem de Administradores Cadastrados
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$adminPageTitle = 'Gestão de Administradores — Sillage ADM';
require_once __DIR__ . '/admin_header.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, nome, email, data_cadastro FROM usuarios_adm ORDER BY id ASC");
$usuarios = $stmt->fetchAll();
?>

<main class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color: var(--sillage-darkgreen);">
        Equipe de Administradores
      </h1>
      <p class="text-muted small mb-0">Controle de acessos e credenciais editoriais do portal Sillage (RF05).</p>
    </div>

    <a href="cadastrar_adm.php" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
      <i class="bi bi-person-plus-fill me-1"></i> Cadastrar Novo ADM
    </a>
  </div>

  <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #FFFFFF;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 70px;">ID</th>
            <th>Nome do Administrador</th>
            <th>E-mail</th>
            <th>Data de Cadastro</th>
            <th class="text-center">Segurança</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td class="fw-bold text-muted">#<?= (int)$u['id'] ?></td>
              <td>
                <div class="fw-bold text-dark">
                  <i class="bi bi-person-badge me-1 text-primary"></i> <?= htmlspecialchars($u['nome']) ?>
                  <?php if ($u['id'] == $currentUser['id']): ?>
                    <span class="badge bg-success ms-2 small">Você</span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <span class="text-muted"><?= htmlspecialchars($u['email']) ?></span>
              </td>
              <td class="small text-muted">
                <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y \à\s H:i', strtotime($u['data_cadastro'])) ?>
              </td>
              <td class="text-center">
                <span class="badge rounded-pill bg-light text-success border border-success-subtle">
                  <i class="bi bi-shield-check me-1"></i> Bcrypt Criptografado
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4 p-3 rounded-4 bg-light border text-muted small">
    <i class="bi bi-info-circle me-1 text-primary"></i>
    <strong>Aviso de Conformidade (RNF01 & RF05):</strong> As senhas são criptografadas com salt dinâmico e hash irreversível. Apenas administradores autenticados podem autorizar a inclusão de novos membros na equipe.
  </div>

</main>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
