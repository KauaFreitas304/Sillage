<?php
/**
 * Sillage - Rodapé Padrão (Footer) & Conformidade LGPD
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */
?>

  <!-- Rodapé Institucional com Créditos Fixos -->
  <footer class="sillage-footer" id="footer-sillage">
    <div class="container text-center">
      <p class="mb-1">
        Sillage © — Desenvolvido por <strong>Kauã Freitas</strong> e <strong>Yasmin Cristiny</strong>
      </p>
      <p class="sillage-footer-subline mb-0">
        Blog educativo e estilizado sobre perfumaria • 
        <a href="privacidade.php" class="text-decoration-underline" id="link-footer-lgpd">Conformidade LGPD & Termos de Privacidade</a>
      </p>
    </div>
  </footer>

  <!-- Banner de Consentimento LGPD (Lei nº 13.709/2018) -->
  <div id="sillage-lgpd-banner" class="sillage-lgpd-banner" style="display: none;" role="region" aria-label="Aviso de Privacidade e Cookies">
    <div class="sillage-lgpd-text">
      <strong>Privacidade & Cookies:</strong> Este portal respeita rigorosamente a LGPD. Utilizamos cookies essenciais exclusivamente para autenticação de administradores e navegação segura. Nenhum dado pessoal é comercializado ou compartilhado com terceiros.
      <a href="privacidade.php" target="_blank" class="ms-1">Saiba mais sobre seus direitos.</a>
    </div>
    <div class="sillage-lgpd-buttons">
      <button type="button" id="lgpd-accept-all" class="sillage-btn-lgpd-accept">
        Aceitar Todos
      </button>
      <button type="button" id="lgpd-accept-necessary" class="sillage-btn-lgpd-settings">
        Apenas Essenciais
      </button>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
