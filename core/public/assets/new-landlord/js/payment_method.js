

    function selectCard(el) {
        document.querySelectorAll('.method-card').forEach(card => {
            card.classList.remove('border-[#0C4D54]');
            card.classList.add('border-gray-200');
            const badge = card.querySelector('.check-badge');
            if (badge) {
                badge.classList.remove('flex');
                badge.classList.add('hidden');
            }
        });

        el.classList.remove('border-gray-200');
        el.classList.add('border-[#0C4D54]');
        const badge = el.querySelector('.check-badge');
        if (badge) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        }
    }
