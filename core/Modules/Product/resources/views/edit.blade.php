@extends('tenant.admin.admin-master')
@section('title')
    {!! __('Edit Product').' - '.$product->name !!}
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
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')
    @php
        $subCat = $product?->subCategory?->id ?? null;
        $childCat = $product?->childCategory?->pluck("id")->toArray() ?? null;
        $cat = $product?->category?->id ?? null;
        $selectedDeliveryOption = $product?->delivery_option?->pluck("delivery_option_id")?->toArray() ?? [];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end mb-6 gap-2">
{{--        <h3 class="text-xl font-semibold text-dark font-urbanist">{!! __('Edit Product').' - '.$product->name !!}</h3>--}}
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.dynamic.page', $product->slug) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-hover transition">
                <i class="mdi mdi-eye-outline text-sm"></i> {{__('View')}}
            </a>
            <a href="{{route('tenant.admin.product.all')}}"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-hover transition">
                <i class="mdi mdi-arrow-left text-sm"></i> {{__('Back')}}
            </a>
        </div>
    </div>

        <div class="bg-surface rounded-xl shadow-main border border-main product-form">
            <div class="p-4 sm:p-6">
                <div class="product-wizard mb-6">
                        <div class="progress-steps">
                            <div class="step completed" data-step="1">
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
                                <span class="step-number">&#10003;</span>
                                <span class="step-label">{{__('Finalize')}}</span>
                            </div>
                        </div>
                </div>

                {{-- Sticky Save Bar --}}
                <div id="product-save-bar"
                     class="sticky top-0 z-[200] flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200 shadow-sm mb-4">
                    <span class="flex items-center gap-2 text-sm text-amber-700 font-medium" id="save-bar-idle-msg">
                        <i class="mdi mdi-pencil-outline text-amber-500"></i>
                        {{__('Edit any field and save when ready')}}
                    </span>
                    <span class="hidden flex items-center gap-2 text-sm text-amber-700 font-medium" id="save-bar-dirty-msg">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        {{__('You have unsaved changes')}}
                    </span>
                    <button type="button" id="sticky-save-btn"
                            class="inline-flex items-center gap-2 px-5 py-1.5 rounded-lg text-sm font-semibold text-white bg-success hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline"></i> {{__('Save Now')}}
                    </button>
                </div>

                    <form data-request-route="{{ route("tenant.admin.product.edit", $product->id) }}" method="post"
                          id="product-edit-form">
                        @csrf
                        <input name="id" type="hidden" value="{{ $product?->id }}">

                        <div class="tab-content mt-2">
                            {{-- Step 1: Basic Information --}}
                            <div class="tab-pane" id="step-basic">
                                <x-product::general-info :brands="$data['brands']" :product="$product"/>
                            </div>

                            {{-- Step 2: Pricing --}}
                            <div class="tab-pane" style="display:none;" id="step-pricing">
                                <x-product::product-price :product="$product" :taxClasses="$data['tax_classes']"/>
                            </div>

                            {{-- Step 3: Inventory & Variants --}}
                            <div class="tab-pane" style="display:none;" id="step-inventory">
                                <x-product::product-inventory :units="$data['units']"
                                                              :inventory="$product?->inventory"
                                                              :uom="$product?->uom"
                                                              :product="$product"/>
                                <x-product::product-attribute
                                    :inventorydetails="$product?->inventory?->inventoryDetails"
                                    :colors="$data['product_colors']"
                                    :sizes="$data['product_sizes']"
                                    :allAttributes="$data['all_attribute']"
                                    :product="$product"/>
                            </div>

                            {{-- Step 4: Media --}}
                            <div class="tab-pane" style="display:none;" id="step-media">
                                <x-product::product-image :product="$product"/>
                            </div>

                            {{-- Step 5: Categories & Delivery --}}
                            <div class="tab-pane" style="display:none;" id="step-categories">
                                <x-product::categories :sub_categories="$sub_categories"
                                                       :categories="$data['categories']"
                                                       :child_categories="$child_categories"
                                                       :selected_child_cat="$childCat" :selected_sub_cat="$subCat"
                                                       :selectedcat="$cat"/>
                                <x-product::delivery-option :selected_delivery_option="$selectedDeliveryOption"
                                                            :deliveryOptions="$data['deliveryOptions']"
                                                            :product="$product"/>
                            </div>

                            {{-- Step 6: Settings --}}
                            <div class="tab-pane" style="display:none;" id="step-setting">
                                <x-product::settings :product="$product"/>
                                <x-product::policy :product="$product"/>
                            </div>

                            {{-- Step 7: Finalize --}}
                            <div class="tab-pane" style="display:none;" id="step-final">
                                <x-product::tags-and-badge :badges="$data['badges']" :tag="$product?->tag"
                                                           :singlebadge="$product?->badge_id"/>
                                <x-product::meta-seo :meta_data="$product->metaData" :product="$product"/>
                            </div>
                        </div>
                    </form>
            </div>
        </div>

    <x-media-upload.tw-markup/>
@endsection
@section('scripts')
    <script src="{{ global_asset('assets/common/js/jquery-ui.min.js') }}" rel="stylesheet"></script>
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
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 7;
            let completedSteps = new Set();
            let stepErrors = new Map(); // Track errors for each step
            let temp = false;

            // Define required fields and validation rules for each step
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

            updateProgress();
            setupRealTimeValidation();

            function setupRealTimeValidation() {
                // Real-time validation for all form inputs
                $(document).on('input blur change', '.form--control, input, select, textarea', function() {
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

                    // Set temp flag for unsaved changes
                    temp = true;
                    $('#save-bar-idle-msg').addClass('hidden');
                    $('#save-bar-dirty-msg').removeClass('hidden');
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
                    temp = true;
                    $('#save-bar-idle-msg').addClass('hidden');
                    $('#save-bar-dirty-msg').removeClass('hidden');
                });

                // Validate media upload — watch for TW modal close
                $(document).on('click', '.tw-media-open-btn', function() {
                    const stepNumber = getStepNumberFromId($(this).closest('.tab-pane').attr('id'));
                    if (stepNumber) {
                        const pollForImageId = setInterval(() => {
                            const imageIdInput = $(`.tab-pane:eq(${stepNumber - 1})`).find('input[name="image_id"]');
                            if (imageIdInput.val()) {
                                clearInterval(pollForImageId);
                                validateStep(stepNumber, false);
                                updateStepStatus(stepNumber);
                                updateNavigationState();
                            }
                        }, 500);
                        setTimeout(() => clearInterval(pollForImageId), 10000);
                    }
                });
            }

            // Sticky save bar button — submits the same form
            $(document).on('click', '#sticky-save-btn', function() {
                $('#product-edit-form').trigger('submit');
            });

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

                // Custom field validation
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

                    case 'description':
                        if (value && value.length < 10) {
                            showFieldError(field, 'Description must be at least 10 characters long');
                            return false;
                        }
                        break;

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
                        const description = stepPane.find('[name="description"]').val();
                        if (description && description.length < 10) {
                            if (showErrors) {
                                showFieldError(stepPane.find('[name="description"]'), 'Description must be at least 10 characters long');
                            }
                            isValid = false;
                        }
                        break;

                    case 2: // Pricing

                        const price = parseFloat(stepPane.find('[name="price"]').val()) || 0;
                        const salePrice = parseFloat(stepPane.find('[name="sale_price"]').val()) || 0;

                        if (salePrice && (isNaN(salePrice) || parseFloat(salePrice) <= 0)) {
                            showFieldError(field, 'Price must be a valid positive number');
                            return false;
                        }

                        if(price){
                            if (salePrice > 0 && salePrice >= price) {
                                if (showErrors) {
                                    showFieldError(stepPane.find('[name="sale_price"]'), 'Sale price must be less than regular price');
                                }
                                isValid = false;
                            }
                        }
                        break;

                    case 4: // Media
                        const imageId = stepPane.find('[name="image_id"]').val();
                        if (!imageId) {
                            if (showErrors) {
                                const uploadBtn = stepPane.find('.tw-media-open-btn').first();
                                showFieldError(uploadBtn, 'At least one product image is required');
                            }
                            isValid = false;
                        }
                        break;

                    case 4: // Categories
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

                // Refresh Bootstrap Tags Input when step 7 (Finalize) becomes visible
                if (currentStep === totalSteps && $.fn.tagsinput) {
                    setTimeout(function() {
                        $('input.tags_input').tagsinput('refresh');
                    }, 50);
                }

                updateNavigationState();
            }

            function updateNavigationState() {
                const stepValid = validateStep(currentStep, false);
                const isLastStep = currentStep === totalSteps;

                $('.prev-step').prop('disabled', currentStep === 1);
                // Next: free navigation, hidden on last step
                $('.next-step').prop('disabled', false).toggle(!isLastStep);
                // Submit: only in bottom nav on last step; sticky bar handles other steps
                $('.submit-form').toggle(isLastStep);

                // Soft warning icon on Next if step has issues (no blocking)
                if (!stepValid) {
                    $('.next-step').html('Next <i class="mdi mdi-alert-circle-outline ml-1 text-amber-300"></i>');
                } else {
                    $('.next-step').html('Next <i class="mdi mdi-arrow-right ml-1"></i>');
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

            // Progress bar step click navigation — always free, no blocking
            $(document).on('click', '.step[data-step]', function() {
                const targetStep = parseInt($(this).data('step'));
                if (targetStep === currentStep) return;

                currentStep = targetStep;
                updateProgress();
                $('html, body').animate({
                    scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                }, 300);
            });

            // Navigation event handlers
            $(document).on('click', '.next-step', function() {
                // Always navigate — just warn if step has issues
                if (!validateStep(currentStep, false)) {
                    toastr.warning('{{__("This step has some incomplete fields — you can still proceed and fix later.")}}', '', {
                        timeOut: 3000,
                        progressBar: true
                    });
                }
                completedSteps.add(currentStep);
                currentStep++;
                updateProgress();

                $('html, body').animate({
                    scrollTop: $(`#step-${getStepName(currentStep)}`).offset().top - 100
                }, 300);
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
                    // 'price': 'Price',
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
            }

            // Form submission handler
            $(document).on('submit', '#product-edit-form', function(e) {
                e.preventDefault();

                // Sync all Summernote editors to their textareas before validation
                if ($.fn.summernote) {
                    $(this).find('textarea').each(function () {
                        var $ta = $(this);
                        try {
                            if ($ta.data('summernote')) {
                                var code = $ta.summernote('code');
                                $ta.val(code);
                            }
                        } catch (e2) {}
                    });
                }

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
                            .html('<i class="mdi mdi-loading mdi-spin mr-2"></i>{{__("Updating...")}}');
                    },
                    success: function(data) {
                        if (data.success) {
                            toastr.success("{{__('Product updated successfully!')}}");

                            // Reset temp flag — restore save bar to idle state
                            temp = false;
                            $('#save-bar-dirty-msg').addClass('hidden');
                            $('#save-bar-idle-msg').removeClass('hidden');

                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);

                        } else {
                            toastr.error(data.message || 'An error occurred');
                            $('.submit-form').prop('disabled', false)
                                .html('<i class="mdi mdi-check mr-2"></i>{{__("Update Product")}}');
                        }
                    },
                    error: function(xhr) {
                        // Re-enable submit button
                        $('.submit-form').prop('disabled', false)
                            .html('<i class="mdi mdi-check mr-2"></i>{{__("Update Product")}}');

                        if (xhr.status === 422) {
                            // Validation errors from server
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
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

        // Additional functionality from original edit page
        $(document).on('change', '.item_attribute_name', function (){
            let value = $(this).find("option:selected").text();
            let oldValue = $(this).closest(".inventory_item").find(`input[value=${value}]`);

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

        $(document).on("change",".product-form .form--control", function (){
            temp = true;
            $('#save-bar-idle-msg').addClass('hidden');
            $('#save-bar-dirty-msg').removeClass('hidden');
        });

        $(document).ready(function () {

            $(document).on('change', '.is_taxable_wrapper select[name=is_taxable]', function () {
                $('.tax_classes_wrapper').toggle();
                $('.tax_classes_wrapper select[name=tax_class]').prop('selectedIndex', 0);
            });

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

            $(document).on('click', '.repeater_button .remove', function (e) {
                if($('.repeater_button .remove').length > 1){
                    $(this).closest('.inventory_item').remove();
                }
            });

            $(document).on('click', '.remove_details_attribute', function (e) {
                $(this).parent().parent().remove();
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
