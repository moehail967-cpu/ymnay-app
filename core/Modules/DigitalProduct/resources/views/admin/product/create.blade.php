@extends('tenant.admin.admin-master')
@section('title')
    {{__('Add New Digital Product')}}
@endsection
@section('site-title')
    {{__('Add New Digital Product')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ global_asset('assets/common/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{global_asset('assets/tenant/backend/css/bootstrap-taginput.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <x-summernote.css/>
    <style>
        .progress-steps .step { cursor: pointer; }
        .progress-steps .step:hover .step-number { opacity: 0.8; transform: scale(1.1); }
    </style>
@endsection
@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
        <h3 class="text-xl font-semibold text-dark font-urbanist">{{ __("Add Digital Product") }}</h3>
        <a href="{{route('tenant.admin.digital.product.all')}}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-hover transition">
            <i class="mdi mdi-arrow-left text-sm"></i> {{__('Back')}}
        </a>
    </div>
    <div class="bg-surface rounded-xl shadow-main border border-main product-form">
        <div class="p-4 sm:p-6">
            <div class="product-wizard mb-6">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label">{{__('General Info')}}</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label">{{__('Price & Tax')}}</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label">{{__('Additional')}}</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-label">{{__('Categories')}}</span>
                    </div>
                    <div class="step" data-step="5">
                        <span class="step-number">5</span>
                        <span class="step-label">{{__('File & Images')}}</span>
                    </div>
                    <div class="step" data-step="6">
                        <span class="step-number">6</span>
                        <span class="step-label">{{__('Tags & Label')}}</span>
                    </div>
                    <div class="step" data-step="7">
                        <span class="step-number">7</span>
                        <span class="step-label">{{__('Product Meta')}}</span>
                    </div>
                    <div class="step" data-step="8">
                        <span class="step-number">8</span>
                        <span class="step-label">{{__('Refund Policy')}}</span>
                    </div>
                </div>
            </div>
            <form data-request-route="{{ route("tenant.admin.digital.product.new") }}" method="post" id="product-create-form">
                @csrf
                <div class="tab-content mt-2">
                    <div class="tab-pane" id="step-1">
                        <x-digitalproduct::general-info/>
                    </div>
                    <div class="tab-pane" id="step-2" style="display:none;">
                        <x-digitalproduct::product-price :taxes="$data['taxes']"/>
                    </div>
                    <div class="tab-pane" id="step-3" style="display:none;">
                        <x-digitalproduct::product-additional-field :languages="$data['languages']" :authors="$data['authors']"/>
                    </div>
                    <div class="tab-pane" id="step-4" style="display:none;">
                        <x-digitalproduct::categories :categories="$data['categories']"/>
                    </div>
                    <div class="tab-pane" id="step-5" style="display:none;">
                        <x-digitalproduct::product-image/>
                    </div>
                    <div class="tab-pane" id="step-6" style="display:none;">
                        <x-digitalproduct::tags-and-badge :badges="$data['badges']"/>
                    </div>
                    <div class="tab-pane" id="step-7" style="display:none;">
                        <x-digitalproduct::meta-seo/>
                    </div>
                    <div class="tab-pane" id="step-8" style="display:none;">
                        <x-digitalproduct::policy/>
                    </div>
                </div>

                <div class="product-nav-buttons mt-6">
                    <button type="button" id="prev-btn" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold bg-secondary border border-main text-dark hover:border-hover transition" style="display:none;">
                        <i class="mdi mdi-arrow-left text-base"></i> {{__('Previous')}}
                    </button>
                    <div class="ml-auto flex items-center gap-2">
                        <button type="button" id="next-btn" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                            {{__('Next')}} <i class="mdi mdi-arrow-right text-base"></i>
                        </button>
                        <button type="submit" id="submit-btn" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-success hover:opacity-90 transition" style="display:none;">
                            <i class="mdi mdi-check-circle-outline text-base"></i> {{__('Create Product')}}
                        </button>
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
    <script src="{{ global_asset('assets/common/js/flatpickr.js') }}"></script>
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>

    <x-digitalproduct::product-file-uploader.js/>
    <x-media-upload.tw-js/>
    <x-summernote.js/>

    <script>
    (function($) {
        "use strict";
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 8;
            let stepErrors = new Map();
            let completedSteps = new Set();

            const stepRequirements = {
                1: { required: ['name', 'slug', 'summary'], custom: true },
                2: { required: [], custom: true },
                3: { required: [], custom: false },
                4: { required: ['category_id'], custom: false },
                5: { required: [], custom: true },
                6: { required: [], custom: true },
                7: { required: [], custom: false },
                8: { required: [], custom: false }
            };

            /* ── Helpers ─────────────────────────────────────────── */
            function getFieldLabel(fieldName) {
                const labels = {
                    'name': 'Product Name', 'slug': 'Product Slug',
                    'summary': 'Summary',   'description': 'Description',
                    'price': 'Regular Price', 'sale_price': 'Sale Price',
                    'category_id': 'Category', 'image_id': 'Product Image',
                    'file': 'Product File',    'tags': 'Tags'
                };
                return labels[fieldName] || fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }

            function showFieldError(field, message) {
                clearFieldError(field);
                field.addClass('is-invalid').removeClass('is-valid');
                const err = $('<div class="dp-field-error"></div>').text(message);
                // Slug input is hidden inside wrapper — attach error to wrapper instead
                if (field.attr('name') === 'slug') {
                    const wrapper = field.closest('.slug-field-wrapper');
                    if (wrapper.length) { wrapper.addClass('has-field-error'); wrapper.after(err); return; }
                }
                if (field.hasClass('select2-hidden-accessible')) field.next('.select2').after(err);
                else field.after(err);
            }

            function clearFieldError(field) {
                field.removeClass('is-invalid');
                field.next('.dp-field-error').remove();
                field.siblings('.dp-field-error').remove();
                if (field.attr('name') === 'slug') {
                    field.closest('.slug-field-wrapper').removeClass('has-field-error').next('.dp-field-error').remove();
                }
                if (field.hasClass('select2-hidden-accessible')) field.next('.select2').next('.dp-field-error').remove();
            }

            function markFieldValid(field) {
                field.removeClass('is-invalid').addClass('is-valid');
                clearFieldError(field);
            }

            function clearStepErrors(stepNum) {
                const pane = $('#step-' + stepNum);
                pane.find('.dp-field-error').remove();
                pane.find('.is-invalid').removeClass('is-invalid');
                pane.find('.note-editor.is-invalid').removeClass('is-invalid');
            }

            function showSummernoteError(ta, msg) {
                clearSummernoteError(ta);
                const ed = ta.next('.note-editor');
                if (ed.length) { ed.addClass('is-invalid'); ed.after($('<div class="dp-field-error text-danger mt-1" style="font-size:0.75rem;"></div>').text(msg)); }
            }

            function clearSummernoteError(ta) {
                const ed = ta.next('.note-editor');
                if (ed.length) { ed.removeClass('is-invalid'); ed.next('.dp-field-error').remove(); }
            }

            /* ── Per-field validation ─────────────────────────────── */
            function validateField(field, stepNum) {
                const name = field.attr('name');
                const val  = field.val();
                clearFieldError(field);

                const req = stepRequirements[stepNum];
                const isRequired = req && req.required.includes(name);

                if (isRequired && (!val || val.trim() === '')) {
                    showFieldError(field, getFieldLabel(name) + ' is required');
                    return false;
                }

                let passed = true;
                switch (name) {
                    case 'name':
                        if (val && val.length < 3)  { showFieldError(field, 'Product name must be at least 3 characters'); passed = false; }
                        else if (val && val.length > 191) { showFieldError(field, 'Product name cannot exceed 191 characters'); passed = false; }
                        break;
                    case 'slug':
                        if (val && !/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u.test(val)) {
                            showFieldError(field, 'Slug can only contain letters, numbers, and hyphens');
                            passed = false;
                        }
                        break;
                    case 'sale_price':
                        const rp = parseFloat($('input[name="price"]').val()) || 0;
                        if (rp && val && parseFloat(val) >= rp) { showFieldError(field, 'Sale price must be less than regular price'); passed = false; }
                        break;
                }

                // Green check for required fields that are valid
                if (passed && isRequired && val && val.trim() !== '') markFieldValid(field);
                return passed;
            }

            /* ── Per-step custom rules ────────────────────────────── */
            function validateStepCustomRules(stepNum, stepPane, showErrors) {
                let ok = true;
                switch (stepNum) {
                    case 1:
                        const ta = stepPane.find('[name="description"]');
                        if (ta.length && ta.hasClass('summernote')) {
                            try {
                                const clean = $('<div>').html(ta.summernote('code')).text().trim();
                                if (!clean || clean.length < 10) {
                                    if (showErrors) showSummernoteError(ta, 'Description must be at least 10 characters');
                                    ok = false;
                                } else { clearSummernoteError(ta); }
                            } catch(e) {}
                        }
                        const slugF = stepPane.find('[name="slug"]');
                        if (slugF.val() && !/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u.test(slugF.val())) {
                            if (showErrors) showFieldError(slugF, 'Slug can only contain letters, numbers, and hyphens');
                            ok = false;
                        }
                        break;

                    case 2:
                        const accessibility = $('select[name="accessibility"]').val();
                        if (accessibility !== 'free') {
                            const priceField = stepPane.find('[name="price"]');
                            const priceVal = priceField.val();
                            if (!priceVal || priceVal.trim() === '') {
                                if (showErrors) showFieldError(priceField, 'Regular Price is required for paid products');
                                ok = false;
                            } else {
                                const price = parseFloat(priceVal) || 0;
                                const sp    = parseFloat(stepPane.find('[name="sale_price"]').val()) || 0;
                                if (price && sp > 0 && sp >= price) {
                                    if (showErrors) showFieldError(stepPane.find('[name="sale_price"]'), 'Sale price must be less than regular price');
                                    ok = false;
                                }
                            }
                        }
                        break;

                    case 5:
                        // Require digital file upload
                        const fileInput = stepPane.find('input[name="file"]');
                        if (fileInput.length && (!fileInput[0].files || fileInput[0].files.length === 0)) {
                            if (showErrors) showFieldError(fileInput.closest('.dp-file-upload__drop-area').find('.dp-file-upload__msg'), 'Product file is required');
                            ok = false;
                        }
                        // Require feature image
                        if (!stepPane.find('[name="image_id"]').val()) {
                            if (showErrors) {
                                const btn = stepPane.find('.tw-media-open-btn').first();
                                if (btn.length) showFieldError(btn, 'Feature image is required');
                            }
                            ok = false;
                        }
                        break;

                    case 6:
                        const tagsField = stepPane.find('input[name="tags"]');
                        if (tagsField.length && (!tagsField.val() || tagsField.val().trim() === '')) {
                            if (showErrors) showFieldError(tagsField, 'At least one tag is required');
                            ok = false;
                        }
                        break;
                }
                return ok;
            }

            /* ── Step validation ──────────────────────────────────── */
            function validateStep(stepNum, showErrors) {
                const stepPane = $('#step-' + stepNum);
                const req = stepRequirements[stepNum];
                if (!req) return true;
                let isValid = true;

                if (showErrors) clearStepErrors(stepNum);

                req.required.forEach(function(fieldName) {
                    const field = stepPane.find('[name="' + fieldName + '"]');
                    if (field.length) {
                        if (!field.val() || field.val().trim() === '') {
                            isValid = false;
                            if (showErrors) showFieldError(field, getFieldLabel(fieldName) + ' is required');
                        } else {
                            if (!validateField(field, stepNum)) isValid = false;
                        }
                    }
                });

                if (req.custom && !validateStepCustomRules(stepNum, stepPane, showErrors)) isValid = false;

                if (isValid) stepErrors.delete(stepNum); else stepErrors.set(stepNum, true);
                return isValid;
            }

            /* ── Navigation state ─────────────────────────────────── */
            function updateNavigationState() {
                const canProceed = validateStep(currentStep, false);
                $('#next-btn').prop('disabled', !canProceed);
                if (canProceed) {
                    $('#next-btn').html('{{__("Next")}} <i class="mdi mdi-arrow-right text-base"></i>');
                } else {
                    $('#next-btn').html('Complete Required Fields <i class="mdi mdi-alert-outline text-base"></i>');
                }
            }

            function updateStepStatus(stepNum) {
                const isValid = validateStep(stepNum, false);
                const el = $('.product-wizard .step[data-step="' + stepNum + '"]');
                if (isValid) {
                    el.removeClass('has-errors');
                    if (stepNum < currentStep) el.addClass('completed');
                } else {
                    el.addClass('has-errors');
                }
            }

            /* ── Show step ────────────────────────────────────────── */
            function showStep(step) {
                $('.tab-pane').hide();
                $('#step-' + step).show();
                $('.product-wizard .step').removeClass('active completed has-errors');
                for (let i = 1; i <= totalSteps; i++) {
                    const el = $('.product-wizard .step[data-step="' + i + '"]');
                    if (i < step) el.addClass('completed');
                    else if (i === step) el.addClass('active');
                    if (stepErrors.has(i)) el.addClass('has-errors');
                }
                $('#prev-btn').toggle(step > 1);
                $('#next-btn').toggle(step < totalSteps);
                $('#submit-btn').toggle(step === totalSteps);
                updateNavigationState();
            }

            /* ── Real-time validation ─────────────────────────────── */
            function setupRealTimeValidation() {
                // blur → full validate + show error
                $(document).on('blur', 'input, textarea', function() {
                    if ($(this).hasClass('summernote')) return;
                    const field = $(this);
                    const pane = field.closest('.tab-pane');
                    if (!pane.length) return;
                    const stepNum = parseInt(pane.attr('id').replace('step-', ''));
                    if (stepNum && !isNaN(stepNum)) {
                        validateField(field, stepNum);
                        updateStepStatus(stepNum);
                        if (stepNum === currentStep) updateNavigationState();
                    }
                });

                // input → only re-validate if already marked invalid (clears as user fixes)
                $(document).on('input', 'input, textarea', function() {
                    if ($(this).hasClass('summernote')) return;
                    const field = $(this);
                    if (!field.hasClass('is-invalid') && !field.hasClass('is-valid')) return;
                    const pane = field.closest('.tab-pane');
                    if (!pane.length) return;
                    const stepNum = parseInt(pane.attr('id').replace('step-', ''));
                    if (stepNum && !isNaN(stepNum)) {
                        validateField(field, stepNum);
                        updateStepStatus(stepNum);
                        if (stepNum === currentStep) updateNavigationState();
                    }
                });

                // select change → validate immediately
                $(document).on('change', 'select', function() {
                    const field = $(this);
                    const pane = field.closest('.tab-pane');
                    if (!pane.length) return;
                    const stepNum = parseInt(pane.attr('id').replace('step-', ''));
                    if (stepNum && !isNaN(stepNum)) {
                        validateField(field, stepNum);
                        updateStepStatus(stepNum);
                        if (stepNum === currentStep) updateNavigationState();
                    }
                });

                // select2
                $(document).on('select2:select select2:unselect', '.select2', function() {
                    const field = $(this);
                    const stepNum = parseInt(field.closest('.tab-pane').attr('id').replace('step-', ''));
                    if (stepNum && !isNaN(stepNum)) {
                        setTimeout(function() {
                            validateField(field, stepNum);
                            updateStepStatus(stepNum);
                            if (stepNum === currentStep) updateNavigationState();
                        }, 100);
                    }
                });

                // media upload polling
                $(document).on('click', '.tw-media-open-btn', function() {
                    const pane = $(this).closest('.tab-pane');
                    const stepNum = parseInt(pane.attr('id').replace('step-', ''));
                    if (stepNum && !isNaN(stepNum)) {
                        const poll = setInterval(function() {
                            if (pane.find('[name="image_id"]').val()) {
                                clearInterval(poll);
                                updateStepStatus(stepNum);
                                if (stepNum === currentStep) updateNavigationState();
                            }
                        }, 500);
                        setTimeout(function() { clearInterval(poll); }, 10000);
                    }
                });

                // Summernote (rich editor) — only validate on blur
                setTimeout(function() {
                    $('[name="description"].summernote').on('summernote.blur', function() {
                        const ta = $(this);
                        const stepNum = parseInt(ta.closest('.tab-pane').attr('id').replace('step-', ''));
                        if (!stepNum || isNaN(stepNum)) return;
                        try {
                            const clean = $('<div>').html(ta.summernote('code')).text().trim();
                            if (!clean || clean.length < 10) showSummernoteError(ta, 'Description must be at least 10 characters');
                            else clearSummernoteError(ta);
                        } catch(e) {}
                        updateStepStatus(stepNum);
                        if (stepNum === currentStep) updateNavigationState();
                    });
                }, 1000);
            }

            /* ── Init ─────────────────────────────────────────────── */
            showStep(1);
            setupRealTimeValidation();

            /* ── Next / Prev ──────────────────────────────────────── */
            $('#next-btn').on('click', function() {
                if (currentStep < totalSteps) {
                    if (validateStep(currentStep, true)) {
                        completedSteps.add(currentStep);
                        currentStep++;
                        showStep(currentStep);
                        window.scrollTo({top: 0, behavior: 'smooth'});
                        toastr.success('{{ __('Step completed!') }}', '{{ __('Success') }}', { timeOut: 1500, progressBar: true });
                    } else {
                        toastr.error('{{ __('Please complete all required fields before proceeding') }}', '{{ __('Validation Error') }}');
                    }
                }
            });

            $('#prev-btn').on('click', function() {
                if (currentStep > 1) { currentStep--; showStep(currentStep); window.scrollTo({top: 0, behavior: 'smooth'}); }
            });

            /* ── Progress bar click ───────────────────────────────── */
            $(document).on('click', '.product-wizard .step', function() {
                const targetStep = parseInt($(this).data('step'));
                if (!targetStep || targetStep === currentStep) return;

                if (targetStep < currentStep) {
                    currentStep = targetStep; showStep(currentStep); window.scrollTo({top: 0, behavior: 'smooth'});
                } else {
                    let firstInvalid = null;
                    for (let i = currentStep; i < targetStep; i++) {
                        if (!validateStep(i, false)) { firstInvalid = i; break; }
                    }
                    if (firstInvalid === null) {
                        for (let i = currentStep; i < targetStep; i++) completedSteps.add(i);
                        currentStep = targetStep; showStep(currentStep); window.scrollTo({top: 0, behavior: 'smooth'});
                        toastr.success('{{ __('Jumped to step') }} ' + targetStep, '{{ __('Navigation') }}', { timeOut: 1500 });
                    } else {
                        validateStep(firstInvalid, true);
                        currentStep = firstInvalid; showStep(currentStep); window.scrollTo({top: 0, behavior: 'smooth'});
                        toastr.error('{{ __('Please complete Step') }} ' + firstInvalid + ' {{ __('before jumping ahead') }}', '{{ __('Validation Error') }}');
                    }
                }
            });

            /* ── Expose for submit handler ────────────────────────── */
            window.dpValidateAllSteps = function() {
                let firstErrorStep = null;
                for (let i = 1; i <= totalSteps; i++) {
                    if (!validateStep(i, true)) {
                        if (firstErrorStep === null) firstErrorStep = i;
                    }
                }
                if (firstErrorStep !== null) {
                    currentStep = firstErrorStep;
                    showStep(currentStep);
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
                return firstErrorStep === null;
            };
        });
    })(jQuery);
    </script>

    <script>
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });

            $('.select2').select2({
                placeholder: '{{__('Select an option')}}',
                language: {
                    noResults: function () {
                        return "{{__('No result found')}}"
                    }
                }
            });
        });


        let temp = false;
        $(document).on("change", ".product-form .form--control", function () {
            $(".product-form .form--control").each(function () {
                if ($(this).val() != '') {
                    temp = true;
                    return false;
                } else {
                    temp = false;
                }
            })
        })

        $(document).ready(function () {
            String.prototype.capitalize = String.prototype.capitalize || function () {
                return this.charAt(0).toUpperCase() + this.slice(1);
            }

            function convertToSlug(text) {
                return text
                    .toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '');
            }

            // Auto-generate slug from name and update display
            $('#product-name').on('keyup', function () {
                const slug = convertToSlug($(this).val());
                $('#product-slug').val(slug);
                $('#slug-display').text(slug);
                $('#slug-url-preview').text(window.location.origin + '/product/' + slug);
            });

            // Slug field toggle
            let slugOriginalValue = '';
            $(document).on('click', '#slug-edit-btn', function() {
                slugOriginalValue = $('#product-slug').val();
                $('.slug-field-wrapper').addClass('editing');
                $('#product-slug').focus();
            });
            $(document).on('click', '#slug-save-btn', function() {
                const newSlug = $('#product-slug').val();
                $('#slug-display').text(newSlug);
                $('#slug-url-preview').text(window.location.origin + '/product/' + newSlug);
                $('.slug-field-wrapper').removeClass('editing');
            });
            $(document).on('click', '#slug-cancel-btn', function() {
                $('#product-slug').val(slugOriginalValue);
                $('.slug-field-wrapper').removeClass('editing');
            });
            $(document).on('keydown', '#product-slug', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); $('#slug-save-btn').trigger('click'); }
                else if (e.key === 'Escape') { $('#slug-cancel-btn').trigger('click'); }
            });

            $(document).on("submit", "#product-create-form", function (e) {
                e.preventDefault();

                // Validate all steps before submission
                if (typeof window.dpValidateAllSteps === 'function' && !window.dpValidateAllSteps()) {
                    toastr.error('{{ __('Please fix all errors before submitting') }}', '{{ __('Validation Error') }}');
                    return false;
                }

                send_ajax_request("post", new FormData(e.target), $(this).attr("data-request-route"), function () {
                    toastr.warning("{{__('Request sent successfully')}}");
                }, function (data) {
                    if (data.success) {
                        toastr.success("{{__('Product Created Successfully')}}");
                        toastr.success("{{__('You are redirected to product list page')}}");

                        $("#product-create-form").trigger("reset");
                        temp = false;
                        setTimeout(function () {
                            window.location.href = "{{ route("tenant.admin.digital.product.all") }}";
                        }, 1000);
                    } else if (data.restricted) {
                        toastr.error("{{__('Sorry you can not upload more products due to your product upload limit')}}");

                        let nav_product = $('.product-limits-nav');
                        nav_product.find('span').css({'color': 'red', 'font-weight': 'bold'});
                        nav_product.effect("shake", {direction: "up left", times: 2, distance: 3}, 500);
                    } else if (!data.success) {
                        toastr.error(data.msg);
                    }
                }, function (xhr) {

                    ajax_toastr_error_message(xhr);
                });
            })

            let inventory_item_id = 0;
            $(document).on("click", ".delivery-item", function () {
                $(this).toggleClass("active");
                $(this).effect("shake", {direction: "up", times: 1, distance: 2}, 500);
                let delivery_option = "";
                $.each($(".delivery-item.active"), function () {
                    delivery_option += $(this).data("delivery-option-id") + " , ";
                })

                delivery_option = delivery_option.slice(0, -3)

                $(".delivery-option-input").val(delivery_option);
            });

            $(document).on("change", "#category", function () {
                let data = new FormData();
                data.append("_token", "{{ csrf_token() }}");
                data.append("category_id", $(this).val());

                send_ajax_request("post", data, '{{ route('tenant.admin.digital.category.sub-category') }}', function () {
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
                send_ajax_request("post", data, '{{ route('tenant.admin.digital.category.child-category') }}', function () {
                    child_category_wrapper.parent().css('position', 'relative')
                    child_category_wrapper.parent().append(`<div class="icon-container text-center">
                        <div class="loading-icon full-circle"></div>
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

            $(document).on('click', '.badge-item', function (e) {
                if ($(this).hasClass("active")) {
                    $(this).removeClass("active")
                    $("#badge_id_input").val('');
                } else {
                    $(".badge-item").removeClass("active");
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

            $(document).on('change' ,'#accessibility', function (){
                let value = $(this).val();
                let tax_price_div = $('#tax-price-info');

                if(value === 'free')
                {
                    tax_price_div.fadeOut();
                    tax_price_div.find('select#tax').val('');
                    tax_price_div.find('select').attr('selected', false);
                    tax_price_div.find('input').val('');
                } else {
                    tax_price_div.fadeIn();
                }
            });

            $(document).on('click', '.custom-plus', function (){
                let custom_wrapper = $('.custom-additional-field-row');

                let option_name_text = '{{__("Option Name")}}';
                let option_name_value = '{{__("Option Value")}}';
                let custom_wrapper_option = `<div class="dp-custom-field-row custom-additional-field-row">
                                            <div>
                                                <input type="text" class="lnd-input" value="" name="option_name[]"
                                                       placeholder="${option_name_text}">
                                            </div>
                                            <div>
                                                <input type="text" class="lnd-input" value="" name="option_value[]"
                                                       placeholder="${option_name_value}">
                                            </div>
                                            <div>
                                                <div class="custom-button flex gap-2">
                                                    <a class="variant-repeater-btn add custom-plus" href="javascript:void(0)"><i class="mdi mdi-plus"></i></a>
                                                    <a class="variant-repeater-btn remove custom-minus" href="javascript:void(0)"><i class="mdi mdi-minus"></i></a>
                                                </div>
                                            </div>
                                        </div>`;

                $(custom_wrapper.parent()).append(custom_wrapper_option);
            });

            $(document).on('click', '.custom-minus', function (){
                let custom_wrapper = $('.custom-additional-field-row');

                if(custom_wrapper.length > 1)
                {
                    $(this).closest('.dp-custom-field-row').remove();
                }
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

        $(window).bind('beforeunload', function () {
            if (temp) {
                return '{{__('Are you sure you want to leave?')}}';
            }
        });
    </script>
@endsection
