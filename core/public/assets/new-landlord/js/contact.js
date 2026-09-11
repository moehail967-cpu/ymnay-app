/**
 * Form Validation - Vanilla JavaScript
 * Handles client-side validation for comment form with real-time feedback
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('comment-form');
    
    // Form fields
    const fullNameInput = document.getElementById('full-name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const locationInput = document.getElementById('location');
    const commentInput = document.getElementById('comment');
    
    // Error message elements
    const fullNameError = document.getElementById('full-name-error');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');
    const locationError = document.getElementById('location-error');
    const commentError = document.getElementById('comment-error');

    /**
     * Validation Rules
     */
    const validators = {
        fullName: (value) => {
            if (!value.trim()) {
                return 'Full name is required';
            }
            if (value.trim().length < 2) {
                return 'Full name must be at least 2 characters';
            }
            if (!/^[a-zA-Z\s]+$/.test(value)) {
                return 'Full name can only contain letters and spaces';
            }
            return null;
        },
        
        email: (value) => {
            if (!value.trim()) {
                return 'Email address is required';
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                return 'Please enter a valid email address';
            }
            return null;
        },
        
        phone: (value) => {
            if (!value.trim()) {
                return 'Phone number is required';
            }
            // Remove all non-numeric characters for validation
            const cleanedPhone = value.replace(/\D/g, '');
            if (cleanedPhone.length < 10 || cleanedPhone.length > 15) {
                return 'Please enter a valid phone number (10-15 digits)';
            }
            return null;
        },
        
        location: (value) => {
            if (!value.trim()) {
                return 'Location is required';
            }
            if (value.trim().length < 2) {
                return 'Location must be at least 2 characters';
            }
            return null;
        },
        
        comment: (value) => {
            if (!value.trim()) {
                return 'Comment is required';
            }
            if (value.trim().length < 10) {
                return 'Comment must be at least 10 characters';
            }
            return null;
        }
    };

    /**
     * Show error message
     */
    function showError(input, errorElement, message) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        input.classList.add('border-red-500', 'focus:ring-red-500');
        input.classList.remove('border-gray-200', 'focus:ring-orange-500');
        input.setAttribute('aria-invalid', 'true');
    }

    /**
     * Hide error message
     */
    function hideError(input, errorElement) {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500', 'focus:ring-red-500');
        input.classList.add('border-gray-200', 'focus:ring-orange-500');
        input.setAttribute('aria-invalid', 'false');
    }

    /**
     * Validate a single field
     */
    function validateField(input, errorElement, validatorKey) {
        const value = input.value;
        const error = validators[validatorKey](value);
        
        if (error) {
            showError(input, errorElement, error);
            return false;
        } else {
            hideError(input, errorElement);
            return true;
        }
    }

    /**
     * Real-time validation on blur (when user leaves the field)
     */
    fullNameInput.addEventListener('blur', function() {
        validateField(this, fullNameError, 'fullName');
    });

    emailInput.addEventListener('blur', function() {
        validateField(this, emailError, 'email');
    });

    phoneInput.addEventListener('blur', function() {
        validateField(this, phoneError, 'phone');
    });

    locationInput.addEventListener('blur', function() {
        validateField(this, locationError, 'location');
    });

    commentInput.addEventListener('blur', function() {
        validateField(this, commentError, 'comment');
    });

    /**
     * Clear error on input (real-time feedback)
     */
    fullNameInput.addEventListener('input', function() {
        if (!fullNameError.classList.contains('hidden')) {
            validateField(this, fullNameError, 'fullName');
        }
    });

    emailInput.addEventListener('input', function() {
        if (!emailError.classList.contains('hidden')) {
            validateField(this, emailError, 'email');
        }
    });

    phoneInput.addEventListener('input', function() {
        if (!phoneError.classList.contains('hidden')) {
            validateField(this, phoneError, 'phone');
        }
    });

    locationInput.addEventListener('input', function() {
        if (!locationError.classList.contains('hidden')) {
            validateField(this, locationError, 'location');
        }
    });

    commentInput.addEventListener('input', function() {
        if (!commentError.classList.contains('hidden')) {
            validateField(this, commentError, 'comment');
        }
    });

    /**
     * Form submission handler
     */
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all fields
        const isFullNameValid = validateField(fullNameInput, fullNameError, 'fullName');
        const isEmailValid = validateField(emailInput, emailError, 'email');
        const isPhoneValid = validateField(phoneInput, phoneError, 'phone');
        const isLocationValid = validateField(locationInput, locationError, 'location');
        const isCommentValid = validateField(commentInput, commentError, 'comment');
        
        // Check if all fields are valid
        const isFormValid = isFullNameValid && isEmailValid && isPhoneValid && isLocationValid && isCommentValid;
        
        if (isFormValid) {
            // Form is valid - collect data
            const formData = {
                fullName: fullNameInput.value.trim(),
                email: emailInput.value.trim(),
                phone: phoneInput.value.trim(),
                location: locationInput.value.trim(),
                comment: commentInput.value.trim()
            };
            
            console.log('Form submitted successfully!', formData);
            
            // Show success message
            showSuccessMessage();
            
            // Reset form
            form.reset();
            
            // Here you would typically send the data to your server
            // Example: sendToServer(formData);
            
        } else {
            // Focus on the first invalid field
            const firstInvalidField = form.querySelector('[aria-invalid="true"]');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            
            console.log('Form validation failed. Please check all fields.');
        }
    });

    /**
     * Show success message (optional enhancement)
     */
    function showSuccessMessage() {
        // Create success message element
        const successMessage = document.createElement('div');
        successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        successMessage.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-semibold">Form submitted successfully!</span>
            </div>
        `;
        
        document.body.appendChild(successMessage);
        
        // Animate in
        setTimeout(() => {
            successMessage.style.transform = 'translateX(0)';
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            successMessage.style.transform = 'translateX(400px)';
            setTimeout(() => {
                document.body.removeChild(successMessage);
            }, 300);
        }, 3000);
    }

    /**
     * Optional: Format phone number as user types
     */
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 0) {
            // Format as: (123) 456-7890
            if (value.length <= 3) {
                value = `(${value}`;
            } else if (value.length <= 6) {
                value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
            } else {
                value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
            }
        }
        
        e.target.value = value;
    });
});

/**
 * Optional: Send data to server
 * Uncomment and modify this function when you have a backend endpoint
 */
/*
async function sendToServer(formData) {
    try {
        const response = await fetch('/api/submit-comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const result = await response.json();
        console.log('Server response:', result);
        
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('There was an error submitting your form. Please try again.');
    }
}
*/



        /**
         * Office Address Card - Interactive Enhancements
         * Each .address-card will copy its own <address> contents when clicked.
         */

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.address-card');
            if (!cards || cards.length === 0) return;

            function showCopyFeedback(message) {
                const feedback = document.createElement('div');
                feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
                feedback.innerHTML = `
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-medium">${message}</span>
                    </div>
                `;

                document.body.appendChild(feedback);
                // Animate in
                setTimeout(() => { feedback.style.transform = 'translateX(0)'; }, 10);

                // Fade out then remove
                setTimeout(() => {
                    feedback.style.opacity = '0';
                    setTimeout(() => { if (feedback.parentNode) feedback.parentNode.removeChild(feedback); }, 300);
                }, 1600);
            }

            cards.forEach((card) => {
                // hover effects
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'scale(1.03)';
                   
                });

                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'scale(1)';
                    
                });

                // click to copy the nearest <address> contents
                card.addEventListener('click', function (e) {
                    const addressEl = card.querySelector('address');
                    let textToCopy = '';
                    if (addressEl) {
                        // preserve line breaks, but collapse extra whitespace
                        textToCopy = Array.from(addressEl.querySelectorAll('span')).map(s => s.innerText.trim()).filter(Boolean).join('\n');
                        if (!textToCopy) {
                            // fallback to full address text
                            textToCopy = addressEl.innerText.trim().replace(/\s+/g, ' ');
                        }
                    }

                    if (!textToCopy) return;

                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Determine label from heading
                        const heading = (card.querySelector('h2') && card.querySelector('h2').innerText) ? card.querySelector('h2').innerText.trim().toLowerCase() : '';
                        let label = 'Copied';
                        if (heading.includes('phone')) label = 'Phone number copied!';
                        else if (heading.includes('email')) label = 'Email address copied!';
                        else if (heading.includes('address') || heading.includes('office')) label = 'Address copied!';
                        showCopyFeedback(label);
                    }).catch((err) => {
                        console.error('Failed to copy content:', err);
                    });
                });

                // keyboard support
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        card.click();
                    }
                });

                // Accessibility
                if (!card.hasAttribute('tabindex')) card.setAttribute('tabindex', '0');
                card.setAttribute('role', 'button');
                card.setAttribute('aria-label', 'Interactive contact card. Click to copy.');
            });
        });
