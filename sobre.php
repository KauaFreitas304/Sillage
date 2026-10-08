<?php
/**
 * Sillage - Página Sobre o Grupo (RF03 / Wireframe 4)
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

$customTitle = 'Sillage — Sobre o Grupo & Desenvolvedores';
$headerBadge = 'Sobre o Grupo';
include __DIR__ . '/header.php';
?>

<main class="container-fluid py-5" style="background-color: var(--sillage-offwhite); min-height: 80vh;">
  
  <div class="sillage-about-outer-frame">
    
    <!-- Moldura Verde Escura Externa do Wireframe 4 -->
    <div class="sillage-about-box">
      
      <!-- Caixa Interna Off-White (Área "TEXTO" do Mockup 4) -->
      <div class="sillage-about-inner-paper">
        
        <div class="text-center">
          <div class="sillage-creators-badge">
            <i class="bi bi-stars"></i>
            Desenvolvido por Kauã Freitas & Yasmin Cristiny
          </div>
          <h1 class="sillage-about-hero-title">Sobre o Projeto Sillage</h1>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-10">
            
            <p class="lead text-center mb-4" style="color: var(--sillage-darkgreen);">
              <em>"Sillage"</em> é uma palavra de origem francesa que traduz o rastro etéreo e invisível deixado no ar pela passagem de uma pessoa perfumada. Mais do que um cheiro, é uma presença que permanece e evoca memórias.
            </p>

            <hr style="border-color: var(--sillage-sage); margin: 30px 0;">

            <div class="row g-4 my-3">
              <div class="col-md-6">
                <div class="p-4 rounded-3 h-100" style="background: rgba(159, 175, 144, 0.15); border: 1px solid var(--sillage-sage);">
                  <h3 class="h4 fw-bold mb-3" style="color: var(--sillage-darkgreen);">
                    <i class="bi bi-compass me-2"></i> Nossa Proposta
                  </h3>
                  <p class="mb-0 text-muted">
                    O <strong>Sillage</strong> nasceu como um portal web educativo, analítico e estilizado para entusiastas, colecionadores e curiosos da arte da perfumaria. Nosso objetivo não é criar um catálogo estritamente técnico ou comercial de vendas, mas sim desmistificar acordes, notas, famílias olfativas e história, promovendo uma apreciação refinada do universo aromático.
                  </p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-4 rounded-3 h-100" style="background: rgba(200, 162, 200, 0.15); border: 1px solid var(--sillage-lilac);">
                  <h3 class="h4 fw-bold mb-3" style="color: var(--sillage-darkgreen);">
                    <i class="bi bi-shield-check me-2"></i> Compromisso & LGPD
                  </h3>
                  <p class="mb-0 text-muted">
                    Acreditamos em um ambiente digital seguro e transparente. O portal segue integralmente as diretrizes da <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong>, com sessões criptografadas (Bcrypt), proteção de rotas restritas e sem rastreamento invasivo de leitores.
                  </p>
                </div>
              </div>
            </div>

            <div class="mt-5 p-4 rounded-3" style="background: #FFFFFF; border: 1px solid rgba(92, 107, 94, 0.2);">
              <h3 class="h4 fw-bold mb-3 text-center" style="font-family: var(--sillage-font-serif); color: var(--sillage-darkgreen);">
                Equipe de Desenvolvimento
              </h3>
              <div class="row text-center mt-3 g-3">
                <div class="col-md-6">
                  <div class="p-3 border rounded-3 bg-light">
                    <h5 class="fw-bold mb-1">Kauã Freitas</h5>
                    <p class="text-muted small mb-0">Arquiteto de Software & Engenharia de Banco de Dados</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 border rounded-3 bg-light">
                    <h5 class="fw-bold mb-1">Yasmin Cristiny</h5>
                    <p class="text-muted small mb-0">Designer de Experiência (UX/UI) & Curadoria Editorial</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>

    </div>

  </div>

</main>

<?php include __DIR__ . '/footer.php'; ?>
