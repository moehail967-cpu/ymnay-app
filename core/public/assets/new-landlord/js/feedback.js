document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.testimonial-card');
    
    cards.forEach(card => {
        const textElement = card.querySelector('.testimonial-text');
        const toggleButton = card.querySelector('[data-toggle]');
        
        // Check if text is long enough to need truncation
        const fullHeight = textElement.scrollHeight;
        const visibleHeight = textElement.clientHeight;
        
        // If content is overflowing, show the "See more" button
        if (fullHeight > visibleHeight) {
            toggleButton.classList.remove('hidden');
        }
        
        // Toggle functionality
        toggleButton.addEventListener('click', function() {
            const isExpanded = textElement.classList.contains('line-clamp-none');
            
            if (isExpanded) {
                // Collapse
                textElement.classList.remove('line-clamp-none');
                textElement.classList.add('line-clamp-12');
                toggleButton.textContent = 'See more';
            } else {
                // Expand
                textElement.classList.remove('line-clamp-12');
                textElement.classList.add('line-clamp-none');
                toggleButton.textContent = 'See less';
            }
        });
    });
});