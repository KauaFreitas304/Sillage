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

    <?php if (is_super_admin()): ?>
      <a href="cadastrar_adm.php" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background-color: var(--sillage-darkgreen); border-color: var(--sillage-darkgreen);">
        <i class="bi bi-person-plus-fill me-1"></i> Cadastrar Novo ADM
      </a>
    <?php else: ?>
      <span class="badge rounded-pill bg-white border text-muted px-3 py-2 shadow-sm">
        <i class="bi bi-lock-fill text-warning me-1"></i> Cadastro restrito ao Super ADM
      </span>
    <?php endif; ?>
  </div>

  <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #FFFFFF;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 70px;">ID</th>
            <th>Nome do Administrador</th>
            <th>E-mail</th>
            <th>Nível de Acesso</th>
            <th>Data de Cadastro</th>
            <th class="text-center">Segurança</th>
            <?php if (is_super_admin()): ?>
              <th class="text-center" style="width: 150px;">Ações</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
            <?php 
              $isThisSuperAdmin = (strtolower(trim($u['email'])) === strtolower(SUPER_ADMIN_EMAIL));
              $isSelf = ((int)$u['id'] === (int)($currentUser['id'] ?? 0));
            ?>
            <tr>
              <td class="fw-bold text-muted">#<?= (int)$u['id'] ?></td>
              <td>
                <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-1">
                  <i class="bi bi-person-badge me-1 <?= $isThisSuperAdmin ? 'text-warning' : 'text-primary' ?>"></i>
                  <?= htmlspecialchars($u['nome']) ?>
                  <?php if ($isSelf): ?>
                    <span class="badge bg-success small">Você</span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <span class="text-muted"><?= htmlspecialchars($u['email']) ?></span>
              </td>
              <td>
                <?php if ($isThisSuperAdmin): ?>
                  <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1">
                    <i class="bi bi-shield-fill-check me-1"></i> Super Administrador
                  </span>
                <?php else: ?>
                  <span class="badge rounded-pill bg-light text-secondary border px-3 py-1">
                    <i class="bi bi-person-gear me-1"></i> Administrador Editorial
                  </span>
                <?php endif; ?>
              </td>
              <td class="small text-muted">
                <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y \à\s H:i', strtotime($u['data_cadastro'])) ?>
              </td>
              <td class="text-center">
                <span class="badge rounded-pill bg-light text-success border border-success-subtle">
                  <i class="bi bi-shield-check me-1"></i> Bcrypt Criptografado
                </span>
              </td>
              <?php if (is_super_admin()): ?>
                <td class="text-center">
                  <?php if ($isThisSuperAdmin): ?>
                    <span class="badge bg-light text-muted border px-3 py-2" title="Conta principal protegida contra exclusão">
                      <i class="bi bi-lock-fill text-warning me-1"></i> Protegido
                    </span>
                  <?php else: ?>
                    <a href="excluir_adm.php?id=<?= (int)$u['id'] ?>&csrf_token=<?= generate_csrf_token() ?>" 
                       class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-confirm-delete" 
                       data-title="o administrador <?= htmlspecialchars($u['nome']) ?> (<?= htmlspecialchars($u['email']) ?>)"
                       title="Excluir este Administrador">
                      <i class="bi bi-trash me-1"></i> Excluir
                    </a>
                  <?php endif; ?>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4 p-3 rounded-4 bg-light border text-muted small">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
      <div>
        <strong>Regra de Permissões e Segurança (RF05 & RNF01):</strong>
        Apenas o login principal <strong>Kauã Freitas & Yasmin Cristiny</strong> (<code>admin@sillage.com</code>) possui privilégios de Super Administrador para <strong>criar</strong> e <strong>excluir</strong> membros da equipe administrativa.
      </div>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
