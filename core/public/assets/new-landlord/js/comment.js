
/**
 * Modern Comments Section - Vanilla JavaScript Implementation
 * Features: Form validation, dynamic comment addition, reply functionality
 */

document.addEventListener('DOMContentLoaded', () => {
  // Get DOM elements (guarded - some pages don't include the comment UI)
  const commentForm = document.getElementById('comment-form');
  const commentsList = document.getElementById('comments-list');
  const commentsCount = document.getElementById('comments-count');
  const replyButtons = document.querySelectorAll('.reply-btn');

  // Form input elements (may be null on pages without the form)
  const firstNameInput = document.getElementById('first-name');
  const lastNameInput = document.getElementById('last-name');
  const emailInput = document.getElementById('email');
  const commentInput = document.getElementById('comment');

  // Error message elements
  const firstNameError = document.getElementById('first-name-error');
  const lastNameError = document.getElementById('last-name-error');
  const emailError = document.getElementById('email-error');
  const commentError = document.getElementById('comment-error');

  /**
   * Email validation using regex
   * @param {string} email - Email address to validate
   * @returns {boolean} - True if valid email format
   */
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * Show error message for a specific field
   * @param {HTMLElement} input - Input element
   * @param {HTMLElement} errorElement - Error message element
   * @param {string} message - Error message text
   */
  function showError(input, errorElement, message) {
    input.classList.add('border-red-500', 'focus:ring-red-500');
    input.classList.remove('border-gray-200', 'focus:ring-orange-500');
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
  }

  /**
   * Hide error message for a specific field
   * @param {HTMLElement} input - Input element
   * @param {HTMLElement} errorElement - Error message element
   */
  function hideError(input, errorElement) {
    input.classList.remove('border-red-500', 'focus:ring-red-500');
    input.classList.add('border-gray-200', 'focus:ring-orange-500');
    errorElement.classList.add('hidden');
  }

  /**
   * Validate all form fields
   * @returns {boolean} - True if all fields are valid
   */
  function validateForm() {
    let isValid = true;

    // Validate First Name
    if (firstNameInput.value.trim() === '') {
      showError(firstNameInput, firstNameError, 'First name is required');
      isValid = false;
    } else {
      hideError(firstNameInput, firstNameError);
    }

    // Validate Last Name
    if (lastNameInput.value.trim() === '') {
      showError(lastNameInput, lastNameError, 'Last name is required');
      isValid = false;
    } else {
      hideError(lastNameInput, lastNameError);
    }

    // Validate Email
    if (emailInput.value.trim() === '') {
      showError(emailInput, emailError, 'Email address is required');
      isValid = false;
    } else if (!isValidEmail(emailInput.value.trim())) {
      showError(emailInput, emailError, 'Please enter a valid email address');
      isValid = false;
    } else {
      hideError(emailInput, emailError);
    }

    // Validate Comment
    if (commentInput.value.trim() === '') {
      showError(commentInput, commentError, 'Comment is required');
      isValid = false;
    } else {
      hideError(commentInput, commentError);
    }

    return isValid;
  }

  /**
   * Get current date formatted as "Month Day, Year"
   * @returns {string} - Formatted date string
   */
  function getCurrentDate() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return now.toLocaleDateString('en-US', options);
  }

  /**
   * Generate random avatar URL
   * @returns {string} - Avatar URL
   */
  function getRandomAvatar() {
    const randomNum = Math.floor(Math.random() * 70) + 1;
    return `https://i.pravatar.cc/150?img=${randomNum}`;
  }

  /**
   * Create new comment HTML element
   * @param {Object} commentData - Comment data object
   * @returns {HTMLElement} - Comment article element
   */
  function createCommentElement(commentData) {
    const article = document.createElement('article');
    article.className =
      'comment-item bg-white p-6  border-b border-borderCS animate-fadeIn';

    article.innerHTML = `
                <div class="flex items-start gap-4">
                    <img src="${commentData.avatar}" 
                         alt="${commentData.name}" 
                         class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0">
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-secondary">${commentData.name}</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-quate">
                                ${commentData.date}
                            </span>
                        </div>
                        
                        <p class="text-gray-700 leading-relaxed mb-4">
                            ${commentData.comment}
                        </p>
                        
                        <button class="reply-btn inline-flex items-center text-xl font-normal text-sectionC transition-colors duration-200">
                            Reply
                        </button>
                    </div>
                </div>
            `;

    // Add reply button event listener
    const replyBtn = article.querySelector('.reply-btn');
    replyBtn.addEventListener('click', handleReplyClick);

    return article;
  }

  /**
   * Update comments count in the UI
   */
  function updateCommentsCount() {
    const totalComments = document.querySelectorAll('.comment-item').length;
    if (commentsCount) {
      commentsCount.textContent = totalComments.toString().padStart(2, '0');
    }
    return totalComments;
  }

  /**
   * Handle form submission
   * @param {Event} e - Submit event
   */
  function handleSubmit(e) {
    e.preventDefault();

    // Validate form
    if (!validateForm()) {
      return;
    }

    // Get form data
    const firstName = firstNameInput.value.trim();
    const lastName = lastNameInput.value.trim();
    const email = emailInput.value.trim();
    const comment = commentInput.value.trim();

    // Create comment data object
    const commentData = {
      name: `${firstName} ${lastName}`,
      date: getCurrentDate(),
      comment: comment,
      avatar: getRandomAvatar(),
    };

    // Create and add new comment to the top of the list
    const newComment = createCommentElement(commentData);
    if (commentsList) {
      commentsList.insertBefore(newComment, commentsList.firstChild);
    }

    // Update comments count (only updates UI if element exists)
    updateCommentsCount();

    // Clear form fields
    commentForm.reset();

    // Remove any error states
    hideError(firstNameInput, firstNameError);
    hideError(lastNameInput, lastNameError);
    hideError(emailInput, emailError);
    hideError(commentInput, commentError);

    // Scroll to the new comment
    if (newComment && typeof newComment.scrollIntoView === 'function') {
      newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  /**
   * Handle reply button click
   * @param {Event} e - Click event
   */
  function handleReplyClick(e) {
    const commentItem = e.target.closest('.comment-item');
    if (!commentItem) return;
    const nameEl = commentItem.querySelector('h3');
    const userName = nameEl ? nameEl.textContent : '';

    // Scroll to comment form if exists
    if (commentForm && typeof commentForm.scrollIntoView === 'function') {
      commentForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Focus on comment textarea and add reply prefix
    if (commentInput) {
      commentInput.focus();
      commentInput.value = `@${userName} `;
    }

    // Optional: visual feedback if commentForm exists
    if (commentForm) {
      commentForm.classList.add('ring-2', 'ring-orange-500');
      setTimeout(() => {
        commentForm.classList.remove('ring-2', 'ring-orange-500');
      }, 1000);
    }
  }

  // Add event listeners for form submission
  if (commentForm) {
    commentForm.addEventListener('submit', handleSubmit);
  }

  // Add event listeners for existing reply buttons
  if (replyButtons && replyButtons.length) {
    replyButtons.forEach(button => {
      button.addEventListener('click', handleReplyClick);
    });
  }

  // Add real-time validation on input (only if the inputs exist)
  if (firstNameInput) {
    firstNameInput.addEventListener('input', () => {
      if (firstNameInput.value.trim() !== '') {
        hideError(firstNameInput, firstNameError);
      }
    });
  }

  if (lastNameInput) {
    lastNameInput.addEventListener('input', () => {
      if (lastNameInput.value.trim() !== '') {
        hideError(lastNameInput, lastNameError);
      }
    });
  }

  if (emailInput) {
    emailInput.addEventListener('input', () => {
      if (
        emailInput.value.trim() !== '' &&
        isValidEmail(emailInput.value.trim())
      ) {
        hideError(emailInput, emailError);
      }
    });
  }

  if (commentInput) {
    commentInput.addEventListener('input', () => {
      if (commentInput.value.trim() !== '') {
        hideError(commentInput, commentError);
      }
    });
  }

  // Add simple fade-in animation via Tailwind
  const style = document.createElement('style');
  style.textContent = `
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                .animate-fadeIn {
                    animation: fadeIn 0.5s ease-out;
                }
            `;
  document.head.appendChild(style);

  // search functionality
  const searchInput = document.getElementById('searchInput');
  const categoriesList = document.getElementById('categoriesList');
  const recentPostsList = document.getElementById('recentPostsList');

  if (searchInput) {
    searchInput.addEventListener('input', function (e) {
      const searchTerm = e.target.value.toLowerCase();
      // Filter categories
      if (categoriesList) {
        const categoryItems = categoriesList.querySelectorAll('.category-item');
        categoryItems.forEach(item => {
          const categoryText = (item.dataset.category || '').toLowerCase();
          item.style.display = categoryText.includes(searchTerm) ? '' : 'none';
        });
      }

      // Filter recent posts
      if (recentPostsList) {
        const postItems = recentPostsList.querySelectorAll('.post-item');
        postItems.forEach(item => {
          const postTitle = (item.dataset.postTitle || '').toLowerCase();
          item.style.display = postTitle.includes(searchTerm) ? '' : 'none';
        });
      }
    });
  }

  // Category click handlers
  const categoryButtons = document.querySelectorAll('.category-btn');
  if (categoryButtons && categoryButtons.length) {
    categoryButtons.forEach(button => {
      button.addEventListener('click', function () {
        categoryButtons.forEach(btn => {
          btn.classList.remove(
            'text-orange-500',
            'font-medium',
            'border-b-2',
            'border-orange-500'
          );
          btn.classList.add('text-gray-700');
        });

        this.classList.remove('text-gray-700');
        this.classList.add(
          'text-orange-500',
          'font-medium',
          'border-b-2',
          'border-orange-500'
        );

        const categoryName = this.closest('.category-item')
          ? this.closest('.category-item').dataset.category
          : '';
        console.log('Selected Category:', categoryName);
      });
    });
  }

  // Recent post click handlers
  const postItems = document.querySelectorAll('.post-item');
  if (postItems && postItems.length) {
    postItems.forEach(item => {
      item.addEventListener('click', function () {
        const postTitle = this.dataset.postTitle;
        const postIndex = this.dataset.index;
        console.log(`Post clicked - Index: ${postIndex}, Title: ${postTitle}`);
      });
    });
  }

  // Social share click handlers
  const socialButtons = document.querySelectorAll('.social-btn');
  if (socialButtons && socialButtons.length) {
    socialButtons.forEach(button => {
      button.addEventListener('click', function () {
        const platform = this.dataset.platform;
        console.log(`Share on ${platform}`);
      });
    });
  }
});
