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

    // Inicialização da Sidebar e Accordion no Mobile
    initMobileSidebar();
});

/**
 * Controle de expansão/recolhimento da Sidebar no Mobile
 */
function initMobileSidebar() {
    const collapseElem = document.getElementById('sillageSidebarCollapse');
    const chevron = document.querySelector('.sillage-sidebar-collapse-btn i');
    if (collapseElem && chevron) {
        collapseElem.addEventListener('show.bs.collapse', () => {
            chevron.className = 'bi bi-chevron-up';
        });
        collapseElem.addEventListener('hide.bs.collapse', () => {
            chevron.className = 'bi bi-chevron-down';
        });
    }
}

/**
 * Carrossel de Lançamentos (Bootstrap 5 Carousel com controles e toque)
 */
function initLaunchCarousel() {
    const carouselEl = document.getElementById('carouselLancamentos');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');

    if (carouselEl && window.bootstrap && window.bootstrap.Carousel) {
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
            interval: 6500,
            ride: 'carousel',
            wrap: true,
            touch: true
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                bsCarousel.prev();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                bsCarousel.next();
            });
        }
    } else {
        // Fallback para listagem ou teste sem Bootstrap JS
        const grid = document.querySelector('.sillage-cards-grid');
        if (grid && prevBtn && nextBtn) {
            nextBtn.addEventListener('click', () => {
                grid.scrollBy({ left: 340, behavior: 'smooth' });
            });
            prevBtn.addEventListener('click', () => {
                grid.scrollBy({ left: -340, behavior: 'smooth' });
            });
        }
    }
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
