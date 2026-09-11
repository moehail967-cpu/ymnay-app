// Payment Method Selection JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Get the banner container (purchase button)
    const purchaseBanner = document.querySelector('.mt-6.bg-primary.rounded-lg');
    
    // Initially hide the purchase button banner
    if (purchaseBanner) {
        purchaseBanner.classList.add('hidden');
    }
    
    // Function to handle payment button clicks
    function handlePaymentClick(event) {
        // Prevent default button behavior
        event.preventDefault();
        
        // Find the closest payment button
        const clickedButton = event.currentTarget;
        
        // Get all payment buttons with 'checks' class
        const allPaymentButtons = document.querySelectorAll('.checks');
        
        // Remove active state from all buttons
        allPaymentButtons.forEach(btn => {
            // Remove border color - add gray, remove sectionC
            btn.classList.remove('border-sectionC');
            btn.classList.add('border-gray-300');
            
            // Hide all checkmarks (if they exist)
            const checkmark = btn.querySelector('.absolute.bg-primary');
            if (checkmark) {
                checkmark.classList.add('hidden');
                checkmark.classList.remove('flex');
            }
        });
        
        // Add active state to clicked button
        clickedButton.classList.remove('border-gray-300');
        clickedButton.classList.add('border-sectionC');
        
        // Show checkmark for clicked button (if it exists)
        const currentCheckmark = clickedButton.querySelector('.absolute.bg-primary');
        if (currentCheckmark) {
            currentCheckmark.classList.remove('hidden');
            currentCheckmark.classList.add('flex');
        }
        
        // Show purchase button banner
        if (purchaseBanner) {
            purchaseBanner.classList.remove('hidden');
        }
    }
    
    // Attach click event to all existing payment buttons with 'checks' class
    const paymentButtons = document.querySelectorAll('.checks');
    paymentButtons.forEach(button => {
        button.addEventListener('click', handlePaymentClick);
    });
    
    // Optional: Watch for dynamically added buttons
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    // Check if the added node has 'checks' class
                    if (node.classList && node.classList.contains('checks')) {
                        node.addEventListener('click', handlePaymentClick);
                    }
                    // Check if any child nodes have 'checks' class
                    const childButtons = node.querySelectorAll && node.querySelectorAll('.checks');
                    if (childButtons) {
                        childButtons.forEach(btn => {
                            btn.addEventListener('click', handlePaymentClick);
                        });
                    }
                }
            });
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
