document.addEventListener('DOMContentLoaded', function() {
    const popover = document.getElementById('action-popover');
    const visitLink = document.getElementById('popover-visit-link');
    const adminLink = document.getElementById('popover-admin-link');
    let activeBtn = null;

    if (popover) {
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                
                if (activeBtn === btn && !popover.classList.contains('hidden')) {
                    popover.classList.add('hidden');
                    activeBtn = null;
                    return;
                }

                activeBtn = btn;
                visitLink.href = btn.getAttribute('data-visit-url') || '#';
                adminLink.href = btn.getAttribute('data-admin-url') || '#';
                
                const rect = btn.getBoundingClientRect();
                popover.classList.remove('hidden');
                
                const popoverHeight = popover.offsetHeight;
                const popoverWidth = popover.offsetWidth;
                
                // Vertical
                let top = rect.bottom + 8;
                if (top + popoverHeight > window.innerHeight) {
                    top = rect.top - popoverHeight - 8;
                }
                
                // Horizontal
                let left = rect.right - popoverWidth;
                if (left < 10) left = 10;
                if (left + popoverWidth > window.innerWidth - 10) {
                    left = window.innerWidth - popoverWidth - 10;
                }
                
                popover.style.top = top + 'px';
                popover.style.left = left + 'px';
            });
        });

        document.addEventListener('click', (e) => {
            if (!popover.contains(e.target) && !e.target.closest('.action-btn')) {
                popover.classList.add('hidden');
                activeBtn = null;
            }
        });
        
        window.addEventListener('scroll', () => {
            popover.classList.add('hidden');
            activeBtn = null;
        }, true);
    }
});

// Page Load Animations
window.addEventListener('load', () => {
    const animatedElements = document.querySelectorAll('.will-animate');
    animatedElements.forEach(el => {
        el.classList.remove('will-animate');
    });
});