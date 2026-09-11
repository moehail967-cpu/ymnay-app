{{--             JavaScript for Custom Specifications--}}
<script>
    (function($) {
        "use strict";

        let custom_spec_id = 0;

        // Add new custom specification row
        $(document).on('click', '.add_custom_spec', function(e) {
            e.preventDefault();

            let newId = ++custom_spec_id;

            let newSpecItem = `
        <div class="custom_specification_item custom-spec-item" data-id="${newId}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_2fr_auto] gap-4 items-end">
                <div class="">
                    <div>
                        <label class="text-sm font-medium text-[var(--color-text-main)] mb-1 block">{{ __('Specification Title') }}</label>
                        <input type="text"
                               name="custom_spec_title[]"
                               class="form-control"
                               placeholder="{{ __('e.g., Fabric Type, Care Instructions') }}"
                               maxlength="100">
                        <small class="text-xs text-[var(--color-text-muted)] mt-0.5 block">{{ __('Max 100 characters') }}</small>
                    </div>
                </div>
                <div class="">
                    <div>
                        <label class="text-sm font-medium text-[var(--color-text-main)] mb-1 block">{{ __('Specification Value') }}</label>
                        <input name="custom_spec_value[]"
                                  class="form-control"
                                  rows="2"
                                  placeholder="{{ __('e.g., 65% Cotton, 33% Polyester, 2% Elastane') }}"
                                  maxlength="500"/>
                        <small class="text-xs text-[var(--color-text-muted)] mt-0.5 block">{{ __('Max 500 characters') }}</small>
                    </div>
                </div>
                <div class="">
                    <div>
                        <div class="flex gap-2">
                            <button type="button" class="variant-repeater-btn add add_custom_spec" title="{{ __('Add') }}">
                                <i class="mdi mdi-plus"></i>
                            </button>
                            <button type="button" class="variant-repeater-btn remove remove_custom_spec" title="{{ __('Remove') }}">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

            $('.custom_specifications_container').append(newSpecItem);

            // Update remove button visibility
            updateRemoveButtons();

            // Animate the new item
            $('.custom_specifications_container .custom_specification_item:last-child')
                .hide()
                .slideDown(300);

            toastr.success('{{ __("New specification added") }}', 'Success');
        });

        // Remove custom specification row
        $(document).on('click', '.remove_custom_spec', function(e) {
            e.preventDefault();

            let $item = $(this).closest('.custom_specification_item');

            $item.slideUp(300, function() {
                $(this).remove();
                updateRemoveButtons();
            });

            toastr.info('{{ __("Specification removed") }}', 'Info');
        });

        // Update remove button visibility
        function updateRemoveButtons() {
            let $items = $('.custom_specification_item');

            if ($items.length <= 1) {
                $items.find('.remove_custom_spec').hide();
            } else {
                $items.find('.remove_custom_spec').show();
            }
        }

        // Initialize on page load
        $(document).ready(function() {
            updateRemoveButtons();

            // Real-time validation for custom specs
            $(document).on('input', 'input[name="custom_spec_title[]"], input[name="custom_spec_value[]"]', function() {
                let $field = $(this);
                let maxLength = parseInt($field.attr('maxlength'));
                let currentLength = $field.val().length;

                // Update character count
                let $small = $field.siblings('small');
                $small.text(`${currentLength}/${maxLength} characters`);

                // Add visual feedback
                if (currentLength > maxLength * 0.9) {
                    $small.removeClass('text-[var(--color-text-muted)]').addClass('text-[var(--color-warning)]');
                } else if (currentLength === maxLength) {
                    $small.removeClass('text-[var(--color-warning)]').addClass('text-[var(--color-danger)]');
                } else {
                    $small.removeClass('text-[var(--color-warning)] text-[var(--color-danger)]').addClass('text-[var(--color-text-muted)]');
                }
            });
        });

    })(jQuery);
</script>
{{--        for slug --}}
<script>
    $(document).ready(function() {
        let originalSlug = '';
        const baseUrl = '';

        // Convert text to slug
        function convertToSlug(text) {
            return text
                .toString()
                .trim()
                // Replace spaces with dashes
                .replace(/\s+/g, '-')
                // Remove punctuation and symbols, but allow Unicode letters/numbers
                .replace(/[^\p{L}\p{N}\-]+/gu, '')
                // Remove multiple dashes
                .replace(/-+/g, '-')
                // Trim leading/trailing dashes
                .replace(/^-+|-+$/g, '');
        }

        // Update slug preview URL
        function updateSlugPreview(slug) {
            const baseUrl = window.location.origin || 'https://yoursite.com'; // Fallback URL
            $('#slug-url-preview').text(baseUrl + '/product/' + slug);
        }

        // Auto-generate slug from name (only when not manually edited)
        $('#product-name').on('input', function() {
            const name = $(this).val();
            const newSlug = convertToSlug(name);

            // Only update if not currently editing and slug hasn't been manually changed
            if (!$('.slug-field-wrapper').hasClass('editing')) {
                $('#slug-display').text(newSlug);
                $('#product-slug').val(newSlug);
                updateSlugPreview(newSlug);
            }
        });

        // Enter edit mode
        $('#slug-edit-btn').on('click', function() {
            originalSlug = $('#product-slug').val();
            $('.slug-field-wrapper').addClass('editing');
            $('#product-slug').focus().select();
        });

        // Save slug changes
        $('#slug-save-btn').on('click', function() {
            const newSlug = $('#product-slug').val().trim();

            // Validate slug format
            const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
            if (newSlug && !slugPattern.test(newSlug)) {
                alert('{{ __('Slug can only contain lowercase letters, numbers, and hyphens') }}');
                return;
            }

            // Convert to proper slug format
            const formattedSlug = convertToSlug(newSlug);
            $('#product-slug').val(formattedSlug);
            $('#slug-display').text(formattedSlug);
            updateSlugPreview(formattedSlug);

            $('.slug-field-wrapper').removeClass('editing');

            // Show success message
            showToast('Slug updated successfully!', 'success');
        });

        // Cancel editing
        $('#slug-cancel-btn').on('click', function() {
            $('#product-slug').val(originalSlug);
            $('.slug-field-wrapper').removeClass('editing');
        });

        // Handle Enter key in slug input
        $('#product-slug').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#slug-save-btn').click();
            }
        });

        // Handle Escape key in slug input
        $('#product-slug').on('keydown', function(e) {
            if (e.which === 27) { // Escape key
                e.preventDefault();
                $('#slug-cancel-btn').click();
            }
        });

        // Real-time slug formatting while editing
        $('#product-slug').on('input', function() {
            const currentValue = $(this).val();
            const cursorPosition = this.selectionStart;
            const formattedValue = currentValue.toLowerCase().replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-');

            if (currentValue !== formattedValue) {
                $(this).val(formattedValue);
                // Maintain cursor position
                this.setSelectionRange(cursorPosition, cursorPosition);
            }

            // Update preview in real-time
            updateSlugPreview(formattedValue);
        });

        // Simple toast notification function using toastr
        function showToast(message, type = 'info') {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            }
        }

        // Initialize slug preview
        updateSlugPreview($('#product-slug').val());
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let slugDisplay = document.getElementById("slug-display");
    let slugInput = document.getElementById("product-slug");
    let editBtn = document.getElementById("slug-edit-btn");
    let saveBtn = document.getElementById("slug-save-btn");
    let cancelBtn = document.getElementById("slug-cancel-btn");
    let slugActions = document.querySelector(".slug-actions");
    let slugPreview = document.getElementById("slug-url-preview");

        function updatePreview(value) {
            const baseUrl = window.location.origin || document.location.protocol + '//' + document.location.host;
            document.getElementById('slug-url-preview').textContent = baseUrl + "/product/" + value;
        }

    // Initial Preview
    if (slugInput.value) {
    updatePreview(slugInput.value);
}
    // Edit click
    editBtn?.addEventListener("click", function () {
    slugDisplay.style.display = "none";
    slugInput.style.display = "block";
    slugActions.style.display = "inline-flex";
    slugInput.focus();
});
    // Save
    saveBtn?.addEventListener("click", function () {
    let val = slugInput.value.trim();
    slugDisplay.textContent = val;
    slugDisplay.style.display = "block";
    slugInput.style.display = "none";
    slugActions.style.display = "none";
    updatePreview(val);
});
    // Cancel
    cancelBtn?.addEventListener("click", function () {
    slugInput.value = slugDisplay.textContent.trim();
    slugDisplay.style.display = "block";
    slugInput.style.display = "none";
    slugActions.style.display = "none";
});
    // Live update preview
    slugInput?.addEventListener("input", function () {
    updatePreview(this.value.trim());
});
});
</script>
