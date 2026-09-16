(function () {
    'use strict';

    const menuButton = document.querySelector('[data-ym-menu-toggle]');
    const mobileMenu = document.querySelector('[data-ym-mobile-menu]');
    if (menuButton && mobileMenu) {
        const closeMenu = () => {
            menuButton.setAttribute('aria-expanded', 'false');
            mobileMenu.hidden = true;
        };
        menuButton.addEventListener('click', () => {
            const open = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!open));
            mobileMenu.hidden = open;
        });
        mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
        window.addEventListener('resize', () => { if (window.innerWidth > 820) closeMenu(); });
    }

    document.querySelectorAll('[data-ym-faq]').forEach(button => {
        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            const answer = document.getElementById(button.getAttribute('aria-controls'));
            button.setAttribute('aria-expanded', String(!expanded));
            if (answer) answer.hidden = expanded;
        });
    });

    const previewDialog = document.querySelector('[data-ym-preview-dialog]');
    const previewImage = previewDialog?.querySelector('[data-ym-preview-image]');
    const previewFrame = previewDialog?.querySelector('[data-ym-dialog-preview]');
    const previewLink = previewDialog?.querySelector('[data-ym-preview-link]');
    let previewOpener = null;
    let switchingDialog = false;
    const openPreview = button => {
        if (!previewDialog || !previewImage) return;
        previewOpener = button;
        previewImage.src = button.dataset.image || '';
        previewImage.alt = `معاينة قالب ${button.dataset.name || ''}`;
        previewDialog.querySelector('#ym-preview-title').textContent = `معاينة قالب ${button.dataset.name || ''}`;
        if (previewLink) {
            previewLink.hidden = !button.dataset.url;
            if (button.dataset.url) previewLink.href = button.dataset.url;
            else previewLink.removeAttribute('href');
        }
        if (themesDialog?.open) {
            switchingDialog = true;
            themesDialog.close();
        }
        previewDialog.showModal();
        switchingDialog = false;
    };
    document.querySelectorAll('[data-ym-theme-preview]').forEach(button => button.addEventListener('click', () => openPreview(button)));
    previewDialog?.querySelector('[data-ym-close-dialog]')?.addEventListener('click', () => previewDialog.close());
    previewDialog?.addEventListener('close', () => {
        if (previewOpener?.closest('[data-ym-themes-dialog]') && themesDialog) themesDialog.showModal();
        previewOpener?.focus();
    });
    previewDialog?.addEventListener('click', event => { if (event.target === previewDialog) previewDialog.close(); });
    previewDialog?.querySelectorAll('[data-ym-preview-size]').forEach(button => button.addEventListener('click', () => {
        previewDialog.querySelectorAll('[data-ym-preview-size]').forEach(item => item.classList.toggle('active', item === button));
        previewFrame?.classList.toggle('mobile', button.dataset.ymPreviewSize === 'mobile');
        previewFrame?.classList.toggle('desktop', button.dataset.ymPreviewSize !== 'mobile');
    }));

    const themesDialog = document.querySelector('[data-ym-themes-dialog]');
    const themesOpener = document.querySelector('[data-ym-open-themes]');
    themesOpener?.addEventListener('click', () => themesDialog?.showModal());
    themesDialog?.querySelector('[data-ym-close-themes]')?.addEventListener('click', () => themesDialog.close());
    themesDialog?.addEventListener('close', () => {
        if (!switchingDialog && !previewDialog?.open) themesOpener?.focus();
    });
    themesDialog?.addEventListener('click', event => { if (event.target === themesDialog) themesDialog.close(); });
})();
