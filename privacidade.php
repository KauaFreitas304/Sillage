<?php
/**
 * Sillage - Política de Privacidade e Conformidade LGPD (Lei 13.709/2018)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$customTitle = 'Conformidade LGPD & Política de Privacidade — Sillage';
$headerBadge = 'LGPD & Privacidade';
include __DIR__ . '/header.php';
?>

<main class="container py-5" style="background-color: var(--sillage-offwhite); min-height: 80vh;">
  
  <div class="row justify-content-center">
    <div class="col-lg-9">
      
      <div class="card p-4 p-md-5 border-0 shadow-sm rounded-4" style="background: #FFFFFF;">
        
        <div class="text-center mb-4">
          <span class="badge px-3 py-2 rounded-pill" style="background-color: var(--sillage-sage); color: #FFFFFF;">
            <i class="bi bi-shield-lock me-1"></i> Lei nº 13.709/2018
          </span>
          <h1 class="display-6 fw-bold mt-2" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
            Política de Privacidade & Conformidade LGPD
          </h1>
          <p class="text-muted small">Última atualização: Outubro de 2026 • Portal Sillage</p>
        </div>

        <div class="content lh-lg" style="color: #3B473C;">
          
          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">1. Apresentação e Compromisso</h2>
          <p>
            O portal <strong>Sillage</strong>, idealizado e mantido por <strong>Kauã Freitas e Yasmin Cristiny</strong>, tem como premissa ética e jurídica o respeito intransigente à privacidade e à autodeterminação informativa dos seus usuários, em total conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (LGPD - Lei nº 13.709/2018)</strong>.
          </p>

          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">2. Dados Pessoais Tratados e Finalidade</h2>
          <p>
            Nosso portal é majoritariamente aberto e informativo. Não exigimos cadastro prévio de leitores para consumo dos conteúdos:
          </p>
          <ul>
            <li><strong>Leitores e Visitantes:</strong> Não coletamos dados de identificação pessoal direta, CPF, telefone ou histórico comercial. Não comercializamos dados com redes de anúncios de terceiros.</li>
            <li><strong>Administradores do Sistema:</strong> Coletamos apenas Nome Completo e E-mail para gestão editorial e autenticação restrita. As senhas são <strong>irreversivelmente criptografadas usando o algoritmo Bcrypt (RNF01)</strong> através de <code>password_hash()</code> nativo do PHP.</li>
          </ul>

          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">3. Política de Cookies e Armazenamento Local</h2>
          <p>
            Utilizamos apenas cookies essenciais:
          </p>
          <ul>
            <li><strong>Cookies de Sessão (PHPSESSID):</strong> Necessários para manter o login seguro do administrador e proteger contra ataques de falsificação de solicitação entre sites (CSRF).</li>
            <li><strong>Armazenamento Local (localStorage):</strong> Guarda exclusivamente o registro da sua escolha referente ao banner de cookies da LGPD, prevenindo interrupções visuais constantes.</li>
          </ul>

          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">4. Direitos do Titular de Dados (Artigo 18 da LGPD)</h2>
          <p>
            Em consonância com a legislação brasileira, os titulares têm direito de obter do controlador, em relação aos dados tratados:
          </p>
          <ol>
            <li>Confirmação da existência de tratamento;</li>
            <li>Acesso aos dados coletados;</li>
            <li>Correção de dados incompletos, inexatos ou desatualizados;</li>
            <li>Anonimização, bloqueio ou eliminação de dados desnecessários;</li>
            <li>Revogação do consentimento concedido a qualquer momento.</li>
          </ol>

          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">5. Medidas de Segurança da Informação</h2>
          <p>
            Adotamos medidas técnicas adequadas para proteger os dados pessoais contra acessos não autorizados e situações acidentais ou ilícitas de destruição, perda ou alteração (RNF01 e RNF02):
          </p>
          <ul>
            <li>Criptografia forte de credenciais (Bcrypt);</li>
            <li>Proteção rigorosa de rotas administrativas com bloqueio de acesso direto via URL;</li>
            <li>Tratamento de entradas e parâmetros SQL através de Prepared Statements (PDO), mitigando riscos de SQL Injection.</li>
          </ul>

          <h2 class="h5 fw-bold mt-4" style="color: var(--sillage-darkgreen);">6. Contato com os Controladores e DPO</h2>
          <p>
            Para exercer quaisquer dos seus direitos previstos na LGPD ou esclarecer dúvidas sobre esta política, entre em contato com a equipe de desenvolvimento:
          </p>
          <div class="p-3 rounded-3" style="background: var(--sillage-offwhite); border: 1px solid var(--sillage-sage);">
            <p class="mb-1"><strong>Controladores de Dados:</strong> Kauã Freitas e Yasmin Cristiny</p>
            <p class="mb-0"><strong>Portal:</strong> Sillage — Universo da Perfumaria</p>
          </div>

          <div class="mt-4 text-center">
            <a href="index.php" class="btn sillage-btn-primary">
              <i class="bi bi-arrow-left me-1"></i> Retornar ao Portal Sillage
            </a>
          </div>

        </div>

      </div>

    </div>
  </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>
