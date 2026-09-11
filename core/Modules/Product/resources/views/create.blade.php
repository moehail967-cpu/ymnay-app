@extends('tenant.admin.admin-master')
@section('title')
    {{__('Add new Product')}}
@endsection
@section('site-title')
    {{__('Add new Product')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/tenant/backend/css/bootstrap-taginput.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <x-summernote.css/>
    <x-product::variant-info.css/>
    @include('product::components.product-css')
    <style>
        .progress-steps .step { cursor: pointer; }
        .progress-steps .step:hover .step-number { opacity: 0.8; transform: scale(1.1); }
        .progress-steps .step[data-step] { transition: opacity .2s; }
    </style>
@endsection
@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end mb-6 gap-2">
        <a href="{{route('tenant.admin.product.all')}}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-hover transition">
            <i class="mdi mdi-arrow-left text-sm"></i> {{__('Back')}}
        </a>
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main product-form">
        <div class="p-4 sm:p-6">
            <div class="product-wizard mb-6">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label">{{__('Basic Info')}}</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label">{{__('Pricing')}}</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label">{{__('Inventory')}}</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-label">{{__('Media')}}</span>
                    </div>
                    <div class="step" data-step="5">
                        <span class="step-number">5</span>
                        <span class="step-label">{{__('Categories')}}</span>
                    </div>
                    <div class="step" data-step="6">
                        <span class="step-number">6</span>
                        <span class="step-label">{{__('Settings')}}</span>
                    </div>
                    <div class="step" data-step="7">
                        <span class="step-number">7</span>
                        <span class="step-label">{{__('Finalize')}}</span>
                    </div>
                </div>
            </div>
            <form data-request-route="{{ route("tenant.admin.product.create") }}" method="post"
                      id="product-create-form">
                    @csrf
                 <div class="tab-content mt-2">
                            {{-- Step 1: Basic Information --}}
                            <div class="tab-pane" id="step-basic">
                                <x-product::general-info :brands="$data['brands']"/>
                            </div>

                            {{-- Step 2: Pricing --}}
                            <div class="tab-pane" id="step-pricing" style="display:none;">
                                <x-product::product-price :taxClasses="$data['tax_classes']"/>
                            </div>

                            {{-- Step 3: Inventory & Variants --}}
                            <div class="tab-pane" id="step-inventory" style="display:none;">
                                <x-product::product-inventory :units="$data['units']"/>
                                <x-product::product-attribute
                                    :colors="$data['product_colors']"
                                    :sizes="$data['product_sizes']"
                                    :all-attributes="$data['all_attribute']"/>
                            </div>

                            {{-- Step 4: Media --}}
                            <div class="tab-pane" id="step-media" style="display:none;">
                                <x-product::product-image/>
                            </div>

                            {{-- Step 5: Categories & Delivery --}}
                            <div class="tab-pane" id="step-categories" style="display:none;">
                                <x-product::categories :categories="$data['categories']"/>
                                <x-product::delivery-option :deliveryOptions="$data['deliveryOptions']"/>
                            </div>

                            {{-- Step 6: Settings --}}
                            <div class="tab-pane" id="step-setting" style="display:none;">
                                <x-product::settings/>
                                <x-product::policy/>
                            </div>

                            {{-- Step 7: Finalize --}}
                            <div class="tab-pane" id="step-final" style="display:none;">
                                <x-product::tags-and-badge :badges="$data['badges']"/>
                                <x-product::meta-seo/>
                            </div>
                        </div>
            </form>

        </div>
    </div>
    <x-media-upload.tw-markup/>
@endsection
@section('scripts')
            <script src="{{global_asset('assets/common/js/jquery-ui.min.js') }}" rel="stylesheet"></script>
            <script src="{{global_asset('assets/tenant/backend/js/bootstrap-taginput.min.js')}}"></script>
            <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
            <script src="{{global_asset('assets/common/js/slugify.js')}}"></script>
            @include('product::components.product-js')

            <x-media-upload.tw-js/>
            <x-summernote.js/>
            <x-product::variant-info.js :colors="$data['product_colors']" :sizes="$data['product_sizes']"
                                        :all-attributes="$data['all_attribute']"/>
            <x-unique-checker user="tenant" selector="input[name=sku]" table="product_inventories" column="sku"/>

            <script>

                // todo:: listen changes event
                $(document).ready(function() {
                    let currentStep = 1;
                    const totalSteps = 7;
                    let completedSteps = new Set();
                    let stepErrors = new Map(); // Track errors for each step
                    const stepRequirements = {
                        1: { // Basic Info
                            required: ['name', 'slug', 'description'],
                            custom: ['slug_format', 'name_length']
                        },
                        2: { // Pricing
                            required: ['sale_price'],
                            custom: ['price_validation', 'tax_validation']
                        },
                        3: { // Inventory
                            required: ['sku', 'uom', 'unit_id'],
                            custom: ['sku_unique', 'stock_validation']
                        },
                        4: { // Media
                            required: ['image_id'],
                            custom: ['image_validation']
                        },
                        5: { // Categories
                            required: ['category_id'],
                            custom: ['category_validation']
                        },
                        6: { // Settings
                            required: [],
                            custom: ['settings_validation']
                        },
                        7: { // Final
                            required: [],
                            custom: ['meta_validation']
                        }
                    };
// Fix brand field validation issue
                    updateProgress();
                    setupRealTimeValidation();


                    function setupRealTimeValidation() {
                        $(document).on('input blur change', '.form--control, input, select, textarea', function() {
                            // Skip if this is a Summernote textarea (it's hidden)
                            if ($(this).hasClass('summernote') || $(this).next('.note-editor').length) {
                                return;
                            }

                            const field = $(this);
                            const fieldName = field.attr('name');
                            const currentStepPane = field.closest('.tab-pane');
                            const stepId = currentStepPane.attr('id');
                            const stepNumber = getStepNumberFromId(stepId);

                            if (stepNumber) {
                                validateField(field, stepNumber);
                                updateStepStatus(stepNumber);
                                updateNavigationState();
                            }
                        });

                        // Validate on select2 change
                        $(document).on('select2:select select2:unselect', '.select2', function() {
                            const field = $(this);
                            const stepNumber = getStepNumberFromId(field.closest('.tab-pane').attr('id'));
                            if (stepNumber) {
                                setTimeout(() => {
                                    validateField(field, stepNumber);
                                    updateStepStatus(stepNumber);
                                    updateNavigationState();
                                }, 100);
                            }
                        });
                        // Validate media upload — watch for TW modal close
                        $(document).on('click', '.tw-media-open-btn', function() {
                            const stepNumber = getStepNumberFromId($(this).closest('.tab-pane').attr('id'));

                            if (stepNumber) {
                                // Poll for hidden input value change after modal interaction
                                const pollForImageId = setInterval(() => {
                                    const imageIdInput = $(`.tab-pane:eq(${stepNumber - 1})`).find('input[name="image_id"]');
                                    if (imageIdInput.val()) {
                                        clearInterval(pollForImageId);
                                        validateStep(stepNumber, false);
                                        updateStepStatus(stepNumber);
                                        updateNavigationState();
                                    }
                                }, 500);

                                // Clear polling after 10 seconds
                                setTimeout(() => clearInterval(pollForImageId), 10000);
                            }
                        });
                        // 2. ADD THIS: Summernote validation setup
                        setupSummernoteValidation();

                    }

                    // 3. NEW FUNCTION: Setup Summernote-specific validation
                    function setupSummernoteValidation() {
                        // Wait for Summernote to initialize, then set up validation
                        setTimeout(function() {
                            $('.summernote').each(function() {
                                const textarea = $(this);
                                const fieldName = textarea.attr('name');

                                if (fieldName === 'description') {
                                    // Initialize Summernote with callbacks
                                    if (textarea.hasClass('note-editor')) {
                                        // Already initialized, just add callback
                                        textarea.summernote('code', textarea.val());
                                    } else {
                                        // Initialize with validation callbacks
                                        textarea.on('summernote.change', function(we, contents, $editable) {
                                            validateSummernoteField(textarea, contents);
                                        });

                                        textarea.on('summernote.blur', function() {
                                            const contents = textarea.summernote('code');
                                            validateSummernoteField(textarea, contents);
                                        });
                                    }
                                }
                            });
                        }, 1000);
                    }

// 4. NEW FUNCTION: Validate Summernote fields
                    function validateSummernoteField(textarea, contents) {
                        const fieldName = textarea.attr('name');
                        const stepNumber = getStepNumberFromId(textarea.closest('.tab-pane').attr('id'));

                        if (fieldName === 'description' && stepNumber) {
                            // Clear previous errors
                            clearFieldError(textarea);

                            // Clean HTML content for validation
                            const cleanText = $('<div>').html(contents).text().trim();

                            if (!cleanText || cleanText.length < 10) {
                                showSummernoteError(textarea, 'Description must be at least 10 characters long');
                            } else {
                                clearSummernoteError(textarea);
                            }

                            // Update step status
                            updateStepStatus(stepNumber);
                            updateNavigationState();
                        }
                    }

// 5. NEW FUNCTION: Show Summernote-specific errors
                    function showSummernoteError(textarea, message) {
                        clearSummernoteError(textarea);

                        // Add error class to the note-editor wrapper
                        const noteEditor = textarea.next('.note-editor');
                        if (noteEditor.length) {
                            noteEditor.addClass('is-invalid');

                            // Add error message after the note-editor
                            const errorDiv = $('<div class="error-message text-danger mt-1 small"></div>').text(message);
                            noteEditor.after(errorDiv);
                        }
                    }

// 6. NEW FUNCTION: Clear Summernote-specific errors
                    function clearSummernoteError(textarea) {
                        const noteEditor = textarea.next('.note-editor');
                        if (noteEditor.length) {
                            noteEditor.removeClass('is-invalid');
                            noteEditor.next('.error-message').remove();
                        }
                    }
                    function validateField(field, stepNumber) {
                        const fieldName = field.attr('name');
                        const value = field.val();

                        // Clear previous errors for this field
                        clearFieldError(field);

                        // Check if field is required for this step
                        const requirements = stepRequirements[stepNumber];
                        if (requirements && requirements.required.includes(fieldName)) {
                            if (!value || value.trim() === '') {
                                showFieldError(field, `${getFieldLabel(fieldName)} is required`);
                                return false;
                            }
                        }
                        // return validateCustomField(field, fieldName, value, stepNumber);
                        switch (fieldName) {
                            case 'description':
                                // Strip HTML tags to check actual content length
                                const cleanText = $('<div>').html(value).text().trim();
                                if (!cleanText || cleanText.length < 10) {
                                    showFieldError(field, 'Description must be at least 10 characters long');
                                    return false;
                                }
                                break;
                            // ... other cases
                        }
                        return validateCustomField(field, fieldName, value, stepNumber);
                    }

                    function validateCustomField(field, fieldName, value, stepNumber) {
                        switch (fieldName) {
                            case 'name':
                                if (value && value.length < 3) {
                                    showFieldError(field, 'Product name must be at least 3 characters long');
                                    return false;
                                }
                                if (value && value.length > 191) {
                                    showFieldError(field, 'Product name cannot exceed 191 characters');
                                    return false;
                                }
                                break;

                            // case 'slug':
                            //     if (value && !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value)) {
                            //         showFieldError(field, 'Slug can only contain lowercase letters, numbers, and hyphens');
                            //         return false;
                            //     }
                            //     break;
                            case 'slug':
                                if (value && !/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u.test(value)) {
                                    showFieldError(field, 'Slug can only contain letters, numbers, and hyphens (any language)');
                                    return false;
                                }
                                break;


                            // case 'description':
                            //     if (value && value.length < 10) {
                            //         showFieldError(field, 'Description must be at least 10 characters long');
                            //         return false;
                            //     }
                            //     break;

                            case 'price':
                                if (value && (isNaN(value) || parseFloat(value) <= 0)) {
                                    showFieldError(field, 'Price must be a valid positive number');
                                    return false;
                                }
                                break;

                            case 'sale_price':

                                const regularPrice = parseFloat($('input[name="price"]').val()) || 0;

                                if(regularPrice){
                                    if (value && parseFloat(value) >= regularPrice) {
                                        showFieldError(field, 'Sale price must be less than regular price');
                                        return false;
                                    }
                                }
                                break;

                            case 'sku':
                                if (value && value.length < 2) {
                                    showFieldError(field, 'SKU must be at least 2 characters long');
                                    return false;
                                }
                                // You can add SKU uniqueness check here with AJAX
                                break;

                            case 'uom':
                                if (value && (isNaN(value) || parseFloat(value) <= 0)) {
                                    showFieldError(field, 'Unit of measurement must be a positive number');
                                    return false;
                                }
                                break;
                        }

                        // Tax validation
                        if (fieldName === 'tax_class') {
                            const isTaxable = $('select[name="is_taxable"]').val();
                            if (isTaxable === 'yes' && (!value || value === '')) {
                                showFieldError(field, 'Tax class is required when product is taxable');
                                return false;
                            }
                        }

                        return true;
                    }

                    function validateStep(stepNumber, showErrors = true) {
                        const stepName = getStepName(stepNumber);
                        const stepPane = $(`#step-${stepName}`);
                        let isValid = true;
                        let errors = [];

                        // Clear previous step errors
                        if (showErrors) {
                            clearStepErrors(stepPane);
                        }

                        const requirements = stepRequirements[stepNumber];
                        if (!requirements) return true;

                        // Validate required fields
                        requirements.required.forEach(fieldName => {
                            const field = stepPane.find(`[name="${fieldName}"]`);
                            if (field.length) {
                                const value = field.val();
                                if (!value || value.trim() === '') {
                                    isValid = false;
                                    errors.push(`${getFieldLabel(fieldName)} is required`);
                                    if (showErrors) {
                                        showFieldError(field, `${getFieldLabel(fieldName)} is required`);
                                    }
                                } else {
                                    // Validate the field content
                                    if (!validateField(field, stepNumber)) {
                                        isValid = false;
                                    }
                                }
                            }
                        });

                        // Custom step validations
                        if (!validateStepCustomRules(stepNumber, stepPane, showErrors)) {
                            isValid = false;
                        }

                        // Update step error tracking
                        if (isValid) {
                            stepErrors.delete(stepNumber);
                        } else {
                            stepErrors.set(stepNumber, errors);
                        }

                        return isValid;
                    }

                    function validateStepCustomRules(stepNumber, stepPane, showErrors) {
                        let isValid = true;

                        switch (stepNumber) {
                            case 1: // Basic Info
                                // Handle Summernote description validation
                                const descriptionTextarea = stepPane.find('[name="description"]');
                                if (descriptionTextarea.length && descriptionTextarea.hasClass('summernote')) {
                                    const summernoteContent = descriptionTextarea.summernote('code');
                                    const cleanDescription = $('<div>').html(summernoteContent).text().trim();

                                    if (!cleanDescription || cleanDescription.length < 10) {
                                        if (showErrors) {
                                            showSummernoteError(descriptionTextarea, 'Description must be at least 10 characters long');
                                        }
                                        isValid = false;
                                    } else {
                                        clearSummernoteError(descriptionTextarea);
                                    }
                                }

                                // Validate slug if present
                                // const slug = stepPane.find('[name="slug"]').val();
                                // if (slug && !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) {
                                //     if (showErrors) {
                                //         showFieldError(stepPane.find('[name="slug"]'), 'Slug can only contain lowercase letters, numbers, and hyphens');
                                //     }
                                //     isValid = false;
                                // }
                                // break;
                                const slug = stepPane.find('[name="slug"]').val();
                                if (slug && !/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u.test(slug)) {
                                    if (showErrors) {
                                        showFieldError(stepPane.find('[name="slug"]'), 'Slug can only contain letters, numbers, and hyphens (any language)');
                                    }
                                    isValid = false;
                                }
                                break;


                            case 2: // Pricing
                                const price = parseFloat(stepPane.find('[name="price"]').val()) || 0;
                                const salePrice = parseFloat(stepPane.find('[name="sale_price"]').val()) || 0;

                                if (salePrice && (isNaN(salePrice) || parseFloat(salePrice) <= 0)) {
                                    if (showErrors) {
                                        showFieldError(stepPane.find('[name="sale_price"]'), 'Sale price must be a valid positive number');
                                    }
                                    isValid = false;
                                }

                                if (price && salePrice > 0 && salePrice >= price) {
                                    if (showErrors) {
                                        showFieldError(stepPane.find('[name="sale_price"]'), 'Sale price must be less than regular price');
                                    }
                                    isValid = false;
                                }
                                break;

                            case 4: // Media
                                const imageIdInput = stepPane.find('input[name="image_id"]');
                                const imageId = imageIdInput.val();

                                if (!imageId || imageId.trim() === '') {
                                    if (showErrors) {
                                        const uploadBtn = stepPane.find('.tw-media-open-btn').first();
                                        showFieldError(uploadBtn, 'At least one product image is required');
                                    }
                                    isValid = false;
                                }
                                break;

                            case 5: // Categories
                                const categoryId = stepPane.find('[name="category_id"]').val();
                                if (!categoryId) {
                                    if (showErrors) {
                                        showFieldError(stepPane.find('[name="category_id"]'), 'Please select a category');
                                    }
                                    isValid = false;
                                }
                                break;
                        }

                        return isValid;
                    }

                    function updateProgress() {
                        $('.step').removeClass('active completed has-errors');

                        $('.step').each(function() {
                            const stepNum = parseInt($(this).data('step'));

                            if (stepNum < currentStep) {
                                $(this).addClass('completed');
                            } else if (stepNum === currentStep) {
                                $(this).addClass('active');
                            }

                            // Mark steps with errors
                            if (stepErrors.has(stepNum)) {
                                $(this).addClass('has-errors');
                            }
                        });

                        // Update progress line
                        const completedCount = Array.from($('.step')).filter(step =>
                            parseInt($(step).data('step')) < currentStep
                        ).length;

                        document.documentElement.style.setProperty('--completed-steps', completedCount);
                        document.documentElement.style.setProperty('--total-steps', totalSteps - 1);

                        // Show/hide tab content
                        $('.tab-pane').hide().removeClass('active');
                        const currentStepName = getStepName(currentStep);
                        $(`#step-${currentStepName}`).show().addClass('active');

                        updateNavigationState();
                    }

                    function updateNavigationState() {
                        const canProceed = validateStep(currentStep, false);

                        $('.prev-step').prop('disabled', currentStep === 1);
                        $('.next-step').prop('disabled', !canProceed).toggle(currentStep < totalSteps);
                        $('.submit-form').toggle(currentStep === totalSteps && canProceed);

                        // Update next button text based on validation
                        if (canProceed) {
                            $('.next-step').html('Next<i class="mdi mdi-arrow-right ml-2"></i>');
                        } else {
                            $('.next-step').html('Complete Required Fields<i class="mdi mdi-alert-outline ml-2"></i>');
                        }
                    }

                    function updateStepStatus(stepNumber) {
                        const isValid = validateStep(stepNumber, false);
                        const stepElement = $(`.step[data-step="${stepNumber}"]`);

                        if (isValid) {
                            stepElement.removeClass('has-errors');
                            if (stepNumber < currentStep) {
                                stepElement.addClass('completed');
                            }
                        } else {
                            stepElement.addClass('has-errors');
                        }
                    }

                    // Progress bar step click navigation
                    $(document).on('click', '.step[data-step]', function() {
                        const targetStep = parseInt($(this).data('step'));

                        if (targetStep === currentStep) return;

                        if (targetStep < currentStep) {
                            // Going back — always allowed
                            currentStep = targetStep;
                            updateProgress();
                            $('html, body').animate({
                                scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                            }, 300);
                        } else {
                            // Going forward — validate all steps up to target
                            let firstInvalidStep = null;

                            for (let i = currentStep; i < targetStep; i++) {
                                if (!validateStep(i, false)) {
                                    firstInvalidStep = i;
                                    break;
                                }
                            }

                            if (firstInvalidStep === null) {
                                // All intermediate steps valid — mark them completed and jump
                                for (let i = currentStep; i < targetStep; i++) {
                                    completedSteps.add(i);
                                }
                                currentStep = targetStep;
                                updateProgress();
                                $('html, body').animate({
                                    scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                                }, 300);
                                toastr.success('{{ __('Jumped to step') }} ' + targetStep, '{{ __('Navigation') }}', { timeOut: 1500 });
                            } else {
                                // Highlight errors on the invalid step and jump there
                                validateStep(firstInvalidStep, true);
                                currentStep = firstInvalidStep;
                                updateProgress();
                                $('html, body').animate({
                                    scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                                }, 300);
                                toastr.error(`{{ __('Please complete Step') }} ${firstInvalidStep} {{ __('before jumping ahead') }}`, '{{ __('Validation Error') }}');
                            }
                        }
                    });

                    // Navigation event handlers
                    $(document).on('click', '.next-step', function() {
                        if (validateStep(currentStep, true)) {
                            completedSteps.add(currentStep);
                            currentStep++;
                            updateProgress();

                            // Scroll to top of new step
                            $('html, body').animate({
                                scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                            }, 300);

                            toastr.success('{{ __('Step completed successfully!') }}', '{{ __('Success') }}', {
                                timeOut: 2000,
                                progressBar: true
                            });
                        } else {
                            toastr.error('{{ __('Please complete all required fields before proceeding') }}', '{{ __('Validation Error') }}');
                        }
                    });

                    $(document).on('click', '.prev-step', function() {
                        if (completedSteps.has(currentStep)) {
                            completedSteps.delete(currentStep);
                        }
                        currentStep--;
                        updateProgress();

                        // Scroll to top of step
                        $('html, body').animate({
                            scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                        }, 300);
                    });

                    // Utility functions
                    function getStepName(step) {
                        const steps = ['basic', 'pricing', 'inventory', 'media', 'categories', 'setting', 'final'];
                        return steps[step - 1] || 'basic';
                    }

                    function getStepNumberFromId(stepId) {
                        if (!stepId) return null;
                        const stepNames = ['basic', 'pricing', 'inventory', 'media', 'categories', 'setting', 'final'];
                        const stepName = stepId.replace('step-', '');
                        return stepNames.indexOf(stepName) + 1;
                    }

                    function getFieldLabel(fieldName) {
                        const labels = {
                            'name': 'Product Name',
                            'slug': 'Product Slug',
                            'description': 'Description',
                            'price': 'Price',
                            'sale_price': 'Sale Price',
                            'sku': 'SKU',
                            'uom': 'Unit of Measurement',
                            'unit_id': 'Unit',
                            'image_id': 'Product Image',
                            'category_id': 'Category',
                            'tax_class': 'Tax Class'
                        };
                        return labels[fieldName] || fieldName.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    }

                    function showFieldError(field, message) {
                        clearFieldError(field);

                        field.addClass('is-invalid');

                        const errorDiv = $('<div class="error-message text-danger mt-1 small"></div>').text(message);

                        // Handle different field types
                        if (field.hasClass('select2-hidden-accessible')) {
                            field.next('.select2').after(errorDiv);
                        } else if (field.closest('.tw-media-upload-wrapper').length) {
                            field.closest('.tw-media-upload-wrapper').addClass('has-error').after(errorDiv);
                        } else if (field.is('select')) {
                            field.after(errorDiv);
                        } else {
                            field.after(errorDiv);
                        }
                    }

                    function clearFieldError(field) {
                        field.removeClass('is-invalid');
                        field.next('.error-message').remove();
                        field.siblings('.error-message').remove();
                        field.closest('.form-group').find('.error-message').remove();

                        // Clear select2 errors
                        if (field.hasClass('select2-hidden-accessible')) {
                            field.next('.select2').removeClass('is-invalid').next('.error-message').remove();
                        }

                        // Clear media upload errors
                        field.closest('.tw-media-upload-wrapper').removeClass('has-error').next('.error-message').remove();
                    }

                    function clearStepErrors(stepPane) {
                        stepPane.find('.error-message').remove();
                        stepPane.find('.is-invalid').removeClass('is-invalid');
                        stepPane.find('.has-error').removeClass('has-error');

                        // Clear Summernote errors
                        stepPane.find('.note-editor.is-invalid').removeClass('is-invalid');
                    }

                    // Form submission handler
                    // Fixed Form submission handler
                    $(document).on('submit', '#product-create-form', function(e) {
                        e.preventDefault();

                        // Validate all steps before submission
                        let allValid = true;
                        let firstErrorStep = null;

                        for (let i = 1; i <= totalSteps; i++) {
                            if (!validateStep(i, true)) {
                                allValid = false;
                                if (firstErrorStep === null) {
                                    firstErrorStep = i;
                                }
                            }
                        }

                        if (!allValid) {
                            toastr.error('{{ __('Please fix all validation errors before submitting') }}', '{{ __('Validation Error') }}');

                            // Jump to first step with errors
                            if (firstErrorStep) {
                                currentStep = firstErrorStep;
                                updateProgress();
                            }
                            return false;
                        }

                        // Get form data
                        let form = $(this);
                        let formData = new FormData(this);
                        let submitUrl = form.attr("data-request-route");

                        // Make AJAX request
                        $.ajax({
                            url: submitUrl,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || "{{csrf_token()}}"
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                $('.submit-form').prop('disabled', true)
                                    .html('<i class="mdi mdi-loading mdi-spin mr-2"></i>{{__("Creating...")}}');
                            },
                            success: function(data) {

                                if (data.success) {
                                    toastr.success("{{ __('Product Created Successfully') }}");
                                    toastr.success("{{ __('Redirecting to product list...') }}");

                                    // Reset form and temp flag
                                    form.trigger("reset");
                                    temp = false;

                                    // Redirect after delay
                                    setTimeout(function() {
                                        window.location.href = "{{ route('tenant.admin.product.all') }}";
                                    }, 2000); // Increased delay to see success message

                                } else if (data.restricted) {
                                    toastr.error("{{ __('Sorry you cannot upload more products due to your product upload limit') }}");
                                    $('.submit-form').prop('disabled', false)
                                        .html('<i class="mdi mdi-check mr-2"></i>{{__("Create Product")}}');

                                } else {
                                    toastr.error(data.message || 'An error occurred');
                                    $('.submit-form').prop('disabled', false)
                                        .html('<i class="mdi mdi-check mr-2"></i>{{__("Create Product")}}');
                                }
                            },
                            error: function(xhr) {
                                // Re-enable submit button
                                $('.submit-form').prop('disabled', false)
                                    .html('<i class="mdi mdi-check mr-2"></i>{{__("Create Product")}}');

                                if (xhr.status === 422) {
                                    // Validation errors from server
                                    let errors = xhr.responseJSON.errors;

                                    $.each(errors, function(field, messages) {
                                        // Show error in multiple ways to ensure it's visible
                                        form.find('.' + field + '-error').text(messages[0]);
                                        toastr.error(field + ': ' + messages[0]);

                                        // Find and highlight the field
                                        let fieldElement = form.find('[name="' + field + '"]');
                                        if (fieldElement.length) {
                                            fieldElement.addClass('is-invalid');
                                            // Find which step this field belongs to
                                            let stepPane = fieldElement.closest('.tab-pane');
                                            if (stepPane.length) {
                                                let stepId = stepPane.attr('id');
                                                let stepNumber = getStepNumberFromId(stepId);
                                                if (stepNumber && stepNumber !== currentStep) {
                                                    // Jump to step with error
                                                    currentStep = stepNumber;
                                                    updateProgress();
                                                }
                                            }
                                        }
                                    });

                                } else if (xhr.status === 500) {
                                    toastr.error('{{ __('Server error occurred. Please try again.') }}');

                                } else if (xhr.status === 0) {
                                    toastr.error('{{ __('Network error. Please check your connection.') }}');

                                } else {
                                    toastr.error('{{ __('An unexpected error occurred. Status: ') }}' + xhr.status);
                                }
                            }
                        });
                    });

                });



// for attribute:
                $(document).on('change', '.item_attribute_name', function (){
                    // todo:: get value from selected value
                    let value = $(this).find("option:selected").text();
                    // todo:: target variant container
                    let oldValue = $(this).closest(".inventory_item").find(`input[value=${value}]`);
                    // todo:: check old value length is bigger then 0 that mean's this value is already selected

                    let attribute_warning = $(this).parents('.row').siblings('.attribute-warning');
                    attribute_warning.css('color', 'black');

                    if(oldValue.length > 0){
                        toastr.warning(`{{ __("You can't select same attribute within a same variant if you need then please create a new variant") }}`)
                        $(this).find("option").each(function (){
                            $(this).attr("selected", false)
                        })
                        $(this).find("option:first-child").attr("selected", true);

                        attribute_warning.css('color', 'red');

                        return false;
                    }

                    let terms = $(this).find('option:selected').data('terms');
                    let terms_html = '<option value=""><?php echo e(__("Select variant value")); ?></option>';
                    terms.map(function (term) {
                        terms_html += '<option value="' + term + '">' + term + '</option>';
                    });
                    $(this).closest('.inventory_item').find('.item_attribute_value').html(terms_html);
                });


                $(document).ready(function() {
                    $('.select2').select2({
                        placeholder: '{{__('Select an option')}}',
                        language: {
                            noResults: function() {
                                return "{{__('No result found')}}"
                            }
                        }
                    });
                });

                // reapeter
                $(document).ready(function () {
                    $(document).on('click', '.repeater_button .remove', function (e) {
                        // if($('.repeater_button .remove').length > 1){
                            $(this).closest('.inventory_item').remove();
                        // }
                    });
                    $(document).on('click', '.remove_details_attribute', function (e) {
                        $(this).parent().parent().remove();
                    });
                });

                let temp = false;
                $(document).on("change",".product-form .form--control", function (){
                    $(".product-form .form--control").each(function (){
                        if($(this).val() != ''){
                            temp = true;
                            return false;
                        }else{
                            temp = false;
                        }
                    })
                })

                $(document).ready(function () {

                    $(document).on('change', '.is_taxable_wrapper select[name=is_taxable]', function () {
                        $('.tax_classes_wrapper').toggle();
                        $('.tax_classes_wrapper select[name=tax_class]').prop('selectedIndex', 0);
                    });


                    let inventory_item_id = 0;
                    $(document).on("click", ".delivery-option-item", function () {
                        $(this).toggleClass("active");
                        $(this).effect("shake", {direction: "up", times: 1, distance: 2}, 500);
                        let delivery_option = "";
                        $.each($(".delivery-option-item.active"), function () {
                            delivery_option += $(this).data("delivery-option-id") + " , ";
                        })

                        delivery_option = delivery_option.slice(0, -3)

                        $(".delivery-option-input").val(delivery_option);
                    });

                    $(document).on("change", "#category", function () {
                        let data = new FormData();
                        data.append("_token", "{{ csrf_token() }}");
                        data.append("category_id", $(this).val());

                        send_ajax_request("post", data, '{{ route('tenant.admin.category.sub-category') }}', function () {
                            $("#sub_category").html("<option value=''>{{__('Select Sub Category')}}</option>");
                            $("#child_category").html("<option value=''>{{__('Select Child Category')}}</option>");
                            $("#select2-child_category-container").html('');
                        }, function (data) {
                            $("#sub_category").html(data.html);
                        }, function () {

                        });
                    });

                    $(document).on("change", "#sub_category", function () {
                        let data = new FormData();
                        data.append("_token", "{{ csrf_token() }}");
                        data.append("sub_category_id", $(this).val());

                        let child_category_wrapper = $("#child_category");
                        send_ajax_request("post", data, '{{ route('tenant.admin.category.child-category') }}', function () {
                            child_category_wrapper.parent().css('position', 'relative')
                            child_category_wrapper.parent().append(`<div class="icon-container text-center">
                                <div class="product-loading-icon"></div>
                            </div>`);

                            child_category_wrapper.html("<option value=''>{{__('Select Child Category')}}</option>");
                            $("#select2-child_category-container").html('');

                        }, function (data) {
                            child_category_wrapper.html(data.html);
                        }, function () {

                        });

                        child_category_wrapper.parent().css('position', 'unset');
                        $('.icon-container').remove();
                    });

                    $(document).on('click', '.badge-select-item', function (e) {
                        if ($(this).hasClass("active"))
                        {
                            $(this).removeClass("active")
                            $("#badge_id_input").val('');
                        } else {
                            $(".badge-select-item").removeClass("active");
                            $(this).addClass("active");
                            $("#badge_id_input").val($(this).attr("data-badge-id"));
                        }

                        $(this).effect("shake", {direction: "up", times: 1, distance: 2}, 500);
                    });

                    $(document).on("click", ".close-icon", function () {
                        $('#tw_media_modal').addClass('hidden');
                        $('#tw_media_modal_backdrop').addClass('hidden');
                        $('body').removeClass('overflow-hidden');
                    });


                    function send_ajax_request(request_type, request_data, url, before_send, success_response, errors) {
                        $.ajax({
                            url: url,
                            type: request_type,
                            headers: {
                                'X-CSRF-TOKEN': "{{csrf_token()}}",
                            },
                            beforeSend: (typeof before_send !== "undefined" && typeof before_send === "function") ? before_send : () => {
                                return "";
                            },
                            processData: false,
                            contentType: false,
                            data: request_data,
                            success: (typeof success_response !== "undefined" && typeof success_response === "function") ? success_response : () => {
                                return "";
                            },
                            error: (typeof errors !== "undefined" && typeof errors === "function") ? errors : () => {
                                return "";
                            }
                        });
                    }

                    function prepare_errors(data, form, msgContainer, btn) {
                        let errors = data.responseJSON;

                        if (errors.success != undefined) {
                            toastr.error(errors.msg.errorInfo[2]);
                            toastr.error(errors.custom_msg);
                        }

                        $.each(errors.errors, function (index, value) {
                            toastr.error(value[0]);
                        });
                    }


                    function ajax_toastr_error_message(xhr) {
                        $.each(xhr.responseJSON.errors, function (key, value) {
                            toastr.error((key.capitalize()).replace("-", " ").replace("_", " "), value);
                        });
                    }

                    function ajax_toastr_success_message(data) {
                        if (data.success) {
                            toastr.success(data.msg)
                        } else {
                            toastr.warning(data.msg);
                        }
                    }
                });
            </script>
@endsection
