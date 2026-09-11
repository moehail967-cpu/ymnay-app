document.addEventListener('DOMContentLoaded', function () {
  // ============================================
  //      1. FORM VALIDATION
  //   ============================================

  const firstNameInput = document.getElementById('firstName');
  const emailInput = document.getElementById('email');
  const firstNameError = document.getElementById('firstNameError');
  const emailError = document.getElementById('emailError');

  // First Name Validation
  if (firstNameInput) {
    firstNameInput.addEventListener('blur', function () {
      if (this.value.trim() === '') {
        firstNameError.classList.remove('hidden');
        this.classList.add('border-red-500');
      } else {
        firstNameError.classList.add('hidden');
        this.classList.remove('border-red-500');
      }
    });

    firstNameInput.addEventListener('input', function () {
      if (this.value.trim() !== '') {
        firstNameError.classList.add('hidden');
        this.classList.remove('border-red-500');
      }
    });
  }

  // Email Validation
  if (emailInput) {
    emailInput.addEventListener('blur', function () {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(this.value.trim())) {
        emailError.classList.remove('hidden');
        this.classList.add('border-red-500');
      } else {
        emailError.classList.add('hidden');
        this.classList.remove('border-red-500');
      }
    });

    emailInput.addEventListener('input', function () {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailPattern.test(this.value.trim())) {
        emailError.classList.add('hidden');
        this.classList.remove('border-red-500');
      }
    });
  }

  // Theme cards: make selection single-choice (only one card active at a time)
  const themeCards = document.querySelectorAll('.group');

  // Deselect all cards
  function deselectAllThemeCards() {
    themeCards.forEach(c => {
      c.classList.remove('border-sectionC');
      c.classList.add('border-transparent');
      const checkmark = c.querySelector('.absolute.top-4.right-4');
      if (checkmark) {
        checkmark.classList.add('hidden');
      }
    });
  }

  // Select a specific card
  function selectThemeCard(card) {
    if (!card) return;
    deselectAllThemeCards();
    card.classList.remove('border-transparent');
    card.classList.add('border-sectionC');
    const checkmark = card.querySelector('.absolute.top-4.right-4');
    if (checkmark) {
      checkmark.classList.remove('hidden');
    }
  }

  // If multiple cards were pre-selected in markup, keep only the first selected
  const preSelected = Array.from(themeCards).find(c => c.classList.contains('border-sectionC'));
  if (preSelected) {
    selectThemeCard(preSelected);
  } else {
    // Ensure all are deselected by default
    deselectAllThemeCards();
  }

  // Add click and keyboard support
  themeCards.forEach(card => {
    // Make the card focusable and announceable as a button for keyboard users
    if (!card.hasAttribute('tabindex')) card.setAttribute('tabindex', '0');
    if (!card.hasAttribute('role')) card.setAttribute('role', 'button');

    card.addEventListener('click', function () {
      selectThemeCard(this);
    });

    // Support keyboard activation (Enter / Space)
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        selectThemeCard(this);
      }
    });
  });

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
    const currentCheckmark = clickedButton.querySelector(
      '.absolute.bg-primary',
    );
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
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType === 1) {
          // Check if the added node has 'checks' class
          if (node.classList && node.classList.contains('checks')) {
            node.addEventListener('click', handlePaymentClick);
          }
          // Check if any child nodes have 'checks' class
          const childButtons =
            node.querySelectorAll && node.querySelectorAll('.checks');
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
    subtree: true,
  });
});
