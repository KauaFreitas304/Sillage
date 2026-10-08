/**
 * Sillage - Scripts Front-end & Interações
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

document.addEventListener('DOMContentLoaded', () => {
    // Inicialização do Carrossel de Lançamentos
    initLaunchCarousel();
    
    // Inicialização da Conformidade LGPD
    initLgpdConsent();
    
    // Tratamento de confirmações de exclusão segura
    initDeleteConfirmations();
});

/**
 * Carrossel de Lançamentos (Suporta rolagem suave ou setas de navegação)
 */
function initLaunchCarousel() {
    const grid = document.querySelector('.sillage-cards-grid');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');

    if (!grid || !prevBtn || !nextBtn) return;

    nextBtn.addEventListener('click', () => {
        grid.scrollBy({ left: 340, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
        grid.scrollBy({ left: -340, behavior: 'smooth' });
    });
}

/**
 * Gestão de Consentimento e Privacidade LGPD (Lei nº 13.709/2018)
 */
function initLgpdConsent() {
    const banner = document.getElementById('sillage-lgpd-banner');
    if (!banner) return;

    const consent = localStorage.getItem('sillage_lgpd_consent');
    if (!consent) {
        banner.style.display = 'flex';
    } else {
        banner.style.display = 'none';
    }

    const acceptBtn = document.getElementById('lgpd-accept-all');
    const rejectBtn = document.getElementById('lgpd-accept-necessary');

    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => {
            localStorage.setItem('sillage_lgpd_consent', JSON.stringify({
                status: 'accepted_all',
                timestamp: new Date().toISOString()
            }));
            banner.style.animation = 'slideDownLGPD 0.3s forwards ease-in';
            setTimeout(() => banner.style.display = 'none', 300);
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => {
            localStorage.setItem('sillage_lgpd_consent', JSON.stringify({
                status: 'only_essential',
                timestamp: new Date().toISOString()
            }));
            banner.style.animation = 'slideDownLGPD 0.3s forwards ease-in';
            setTimeout(() => banner.style.display = 'none', 300);
        });
    }
}

/**
 * Diálogo de Confirmação para Ações Sensíveis (Exclusão de posts/usuários)
 */
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemTitle = btn.getAttribute('data-title') || 'este registro';
            if (!confirm(`Atenção: Tem certeza de que deseja excluir permanentemente "${itemTitle}"? Esta ação não pode ser desfeita.`)) {
                e.preventDefault();
            }
        });
    });
}
