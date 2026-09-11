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
    <x-media-upload.css/>
    <x-summernote.css/>
    <x-product::variant-info.css/>

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #6c757d;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        /** {*/
        /*    margin: 0;*/
        /*    padding: 0;*/
        /*    box-sizing: border-box;*/
        /*    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;*/
        /*}*/

        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }

        .product-create-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .header h1 {
            font-weight: 600;
            color: var(--dark);
        }

        .back-btn {
            background: var(--light);
            color: var(--dark);
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }

        .back-btn:hover {
            background: #e9ecef;
        }

        .progress-container {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 2px;
            width: 100%;
            background: #e9ecef;
            z-index: 1;
        }

        .progress-bar {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            height: 2px;
            width: 0%;
            background: var(--primary);
            z-index: 2;
            transition: var(--transition);
        }

        .step {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .step.active .step-icon {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .step.completed .step-icon {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
            color: #6c757d;
        }

        .step.active .step-label {
            color: var(--primary);
        }

        .step.completed .step-label {
            color: var(--success);
        }

        .form-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .form-section {
            padding: 2rem;
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            color: var(--dark);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }

        .required::after {
            content: '*';
            color: var(--danger);
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #3251d4;
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-outline {
            background: transparent;
            color: var(--dark);
            border: 1px solid #ced4da;
        }

        .btn-outline:hover {
            background: #f8f9fa;
        }

        .variant-card {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }

        .variant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .add-variant-btn {
            background: var(--success);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remove-btn {
            background: var(--danger);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .image-upload-area {
            border: 2px dashed #ced4da;
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .image-upload-area:hover {
            border-color: var(--primary);
        }

        .upload-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .preview-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .preview-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0 0 0 8px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .progress-steps {
                flex-wrap: wrap;
                justify-content: center;
            }

            .step {
                margin: 0.5rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection
@section('content')
    <div class="product-create-container">
        <div class="header">
            <h1>{{ __('Create New Product') }}</h1>
            <a href="#" class="back-btn">← {{ __('Back to Products') }}</a>
        </div>

        <div class="progress-container">
            <div class="progress-steps">
                <div class="progress-bar" id="progress-bar"></div>

                <div class="step active" data-step="1">
                    <div class="step-icon">1</div>
                    <div class="step-label">{{ __('General Info') }}</div>
                </div>

                <div class="step" data-step="2">
                    <div class="step-icon">2</div>
                    <div class="step-label">{{ __('Pricing') }}</div>
                </div>

                <div class="step" data-step="3">
                    <div class="step-icon">3</div>
                    <div class="step-label">{{ __('Inventory') }}</div>
                </div>

                <div class="step" data-step="4">
                    <div class="step-icon">4</div>
                    <div class="step-label">{{ __('Media') }}</div>
                </div>

                <div class="step" data-step="5">
                    <div class="step-icon">5</div>
                    <div class="step-label">{{ __('Organization') }}</div>
                </div>

                <div class="step" data-step="6">
                    <div class="step-icon">6</div>
                    <div class="step-label">{{ __('Review') }}</div>
                </div>
            </div>
        </div>

        <form id="product-create-form">
            <div class="form-container">
                <!-- Step 1: General Information -->
                <div class="form-section active" id="step-1">
                    <h2 class="section-title">{{ __('General Information') }}</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product-name" class="form-label required">{{ __('Product Name') }}</label>
                            <input type="text" id="product-name" class="form-control" placeholder="{{ __('Enter product name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="product-slug" class="form-label required">{{ __('Slug') }}</label>
                            <input type="text" id="product-slug" class="form-control" placeholder="{{ __('Product URL slug') }}" required>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="product-summary" class="form-label">{{ __('Summary') }}</label>
                            <textarea id="product-summary" class="form-control" rows="3" placeholder="{{ __('Brief product description') }}"></textarea>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="product-description" class="form-label">{{ __('Description') }}</label>
                            <textarea id="product-description" class="form-control" rows="5" placeholder="{{ __('Detailed product description') }}"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="product-brand" class="form-label">{{ __('Brand') }}</label>
                            <select id="product-brand" class="form-control">
                                <option value="">{{ __('Select a brand') }}</option>
                                <option value="1">Brand 1</option>
                                <option value="2">Brand 2</option>
                                <option value="3">Brand 3</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div></div> <!-- Empty div for spacing -->
                        <button type="button" class="btn btn-primary next-step" data-next="2">
                            {{ __('Next: Pricing') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Pricing -->
                <div class="form-section" id="step-2">
                    <h2 class="section-title">{{ __('Pricing') }}</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product-cost" class="form-label">{{ __('Base Cost') }}</label>
                            <input type="number" id="product-cost" class="form-control" placeholder="0.00" step="0.01">
                            <small>{{ __('Purchase price of this product') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="product-price" class="form-label required">{{ __('Regular Price') }}</label>
                            <input type="number" id="product-price" class="form-control" placeholder="0.00" step="0.01" required>
                            <small>{{ __('This price will display with a strikethrough') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="product-sale-price" class="form-label">{{ __('Sale Price') }}</label>
                            <input type="number" id="product-sale-price" class="form-control" placeholder="0.00" step="0.01">
                            <small>{{ __('This will be your product selling price') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="product-taxable" class="form-label">{{ __('Is Taxable?') }}</label>
                            <select id="product-taxable" class="form-control">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>

                        <div class="form-group" id="tax-class-container" style="display: none;">
                            <label for="product-tax-class" class="form-label">{{ __('Tax Class') }}</label>
                            <select id="product-tax-class" class="form-control">
                                <option value="">{{ __('Select tax class') }}</option>
                                <option value="1">Standard</option>
                                <option value="2">Reduced</option>
                                <option value="3">Zero-rated</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline prev-step" data-prev="1">
                            ← {{ __('Previous') }}
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="3">
                            {{ __('Next: Inventory') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 3: Inventory -->
                <div class="form-section" id="step-3">
                    <h2 class="section-title">{{ __('Inventory Management') }}</h2>

                    <div class="form-group">
                        <label for="product-sku" class="form-label required">{{ __('SKU') }}</label>
                        <input type="text" id="product-sku" class="form-control" placeholder="{{ __('Product SKU') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="product-stock" class="form-label required">{{ __('Stock Quantity') }}</label>
                        <input type="number" id="product-stock" class="form-control" value="1" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="stock-management"> {{ __('Enable stock management') }}
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="backorders"> {{ __('Allow backorders') }}
                        </label>
                    </div>

                    <h3 class="mt-4 mb-3">{{ __('Product Variants') }}</h3>
                    <button type="button" class="add-variant-btn">+ {{ __('Add Variant') }}</button>

                    <div class="variant-card">
                        <div class="variant-header">
                            <h4>{{ __('Variant #1') }}</h4>
                            <button type="button" class="remove-btn">×</button>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">{{ __('Size') }}</label>
                                <select class="form-control">
                                    <option value="">{{ __('Select size') }}</option>
                                    <option value="s">Small</option>
                                    <option value="m">Medium</option>
                                    <option value="l">Large</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Color') }}</label>
                                <select class="form-control">
                                    <option value="">{{ __('Select color') }}</option>
                                    <option value="red">Red</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Additional Price') }}</label>
                                <input type="number" class="form-control" placeholder="0.00" step="0.01">
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Stock Quantity') }}</label>
                                <input type="number" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline prev-step" data-prev="2">
                            ← {{ __('Previous') }}
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="4">
                            {{ __('Next: Media') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 4: Media -->
                <div class="form-section" id="step-4">
                    <h2 class="section-title">{{ __('Media') }}</h2>

                    <div class="form-group">
                        <label class="form-label required">{{ __('Featured Image') }}</label>
                        <div class="image-upload-area">
                            <div class="upload-icon">📁</div>
                            <p>{{ __('Drag & drop your image here or click to browse') }}</p>
                            <button type="button" class="btn btn-outline mt-2">{{ __('Select Image') }}</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Product Gallery') }}</label>
                        <div class="image-upload-area">
                            <div class="upload-icon">🖼️</div>
                            <p>{{ __('Drag & drop multiple images here or click to browse') }}</p>
                            <button type="button" class="btn btn-outline mt-2">{{ __('Select Images') }}</button>
                        </div>

                        <div class="image-preview">
                            <!-- Image previews will appear here -->
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline prev-step" data-prev="3">
                            ← {{ __('Previous') }}
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="5">
                            {{ __('Next: Organization') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 5: Organization -->
                <div class="form-section" id="step-5">
                    <h2 class="section-title">{{ __('Organization') }}</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product-category" class="form-label required">{{ __('Category') }}</label>
                            <select id="product-category" class="form-control" required>
                                <option value="">{{ __('Select category') }}</option>
                                <option value="1">Category 1</option>
                                <option value="2">Category 2</option>
                                <option value="3">Category 3</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="product-subcategory" class="form-label">{{ __('Subcategory') }}</label>
                            <select id="product-subcategory" class="form-control">
                                <option value="">{{ __('Select subcategory') }}</option>
                                <option value="1">Subcategory 1</option>
                                <option value="2">Subcategory 2</option>
                                <option value="3">Subcategory 3</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="product-tags" class="form-label">{{ __('Tags') }}</label>
                            <input type="text" id="product-tags" class="form-control" placeholder="{{ __('Add tags separated by commas') }}">
                        </div>
                    </div>

                    <h3 class="mt-4 mb-3">{{ __('Shipping') }}</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product-weight" class="form-label">{{ __('Weight') }}</label>
                            <input type="number" id="product-weight" class="form-control" placeholder="0.00" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="product-dimensions" class="form-label">{{ __('Dimensions (L×W×H)') }}</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="number" class="form-control" placeholder="{{ __('Length') }}" step="0.01">
                                <input type="number" class="form-control" placeholder="{{ __('Width') }}" step="0.01">
                                <input type="number" class="form-control" placeholder="{{ __('Height') }}" step="0.01">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="product-shipping-class" class="form-label">{{ __('Shipping Class') }}</label>
                            <select id="product-shipping-class" class="form-control">
                                <option value="">{{ __('Select shipping class') }}</option>
                                <option value="1">Standard</option>
                                <option value="2">Express</option>
                                <option value="3">Free</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline prev-step" data-prev="4">
                            ← {{ __('Previous') }}
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-next="6">
                            {{ __('Next: Review') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 6: Review -->
                <div class="form-section" id="step-6">
                    <h2 class="section-title">{{ __('Review & Submit') }}</h2>

                    <div class="review-summary">
                        <div class="review-item">
                            <h4>{{ __('General Information') }}</h4>
                            <p><strong>{{ __('Product Name') }}:</strong> <span id="review-name"></span></p>
                            <p><strong>{{ __('Slug') }}:</strong> <span id="review-slug"></span></p>
                            <p><strong>{{ __('Description') }}:</strong> <span id="review-description"></span></p>
                        </div>

                        <div class="review-item">
                            <h4>{{ __('Pricing') }}</h4>
                            <p><strong>{{ __('Regular Price') }}:</strong> $<span id="review-price"></span></p>
                            <p><strong>{{ __('Sale Price') }}:</strong> $<span id="review-sale-price"></span></p>
                        </div>

                        <div class="review-item">
                            <h4>{{ __('Inventory') }}</h4>
                            <p><strong>{{ __('SKU') }}:</strong> <span id="review-sku"></span></p>
                            <p><strong>{{ __('Stock Quantity') }}:</strong> <span id="review-stock"></span></p>
                        </div>

                        <div class="review-item">
                            <h4>{{ __('Organization') }}</h4>
                            <p><strong>{{ __('Category') }}:</strong> <span id="review-category"></span></p>
                            <p><strong>{{ __('Tags') }}:</strong> <span id="review-tags"></span></p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline prev-step" data-prev="5">
                            ← {{ __('Previous') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            {{ __('Create Product') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

        <x-media-upload.markup/>
@endsection
@section('scripts')
            <script src="{{global_asset('assets/common/js/jquery-ui.min.js') }}" rel="stylesheet"></script>
            <script src="{{global_asset('assets/tenant/backend/js/bootstrap-taginput.min.js')}}"></script>
            <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
            <script src="{{global_asset('assets/common/js/slugify.js')}}"></script>

            <x-media-upload.js/>
            <x-summernote.js/>
            <x-product::variant-info.js :colors="$data['product_colors']" :sizes="$data['product_sizes']"
                                        :all-attributes="$data['all_attribute']"/>
            <x-unique-checker user="tenant" selector="input[name=sku]" table="product_inventories" column="sku"/>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Step navigation
                    const formSections = document.querySelectorAll('.form-section');
                    const steps = document.querySelectorAll('.step');
                    const progressBar = document.getElementById('progress-bar');
                    let currentStep = 1;

                    // Update progress bar
                    function updateProgress() {
                        const progressPercentage = ((currentStep - 1) / (steps.length - 1)) * 100;
                        progressBar.style.width = `${progressPercentage}%`;

                        steps.forEach((step, index) => {
                            const stepNum = parseInt(step.getAttribute('data-step'));
                            if (stepNum < currentStep) {
                                step.classList.add('completed');
                                step.classList.remove('active');
                            } else if (stepNum === currentStep) {
                                step.classList.add('active');
                                step.classList.remove('completed');
                            } else {
                                step.classList.remove('active', 'completed');
                            }
                        });
                    }

                    // Navigate to step
                    function goToStep(stepNumber) {
                        formSections.forEach(section => {
                            section.classList.remove('active');
                        });

                        document.getElementById(`step-${stepNumber}`).classList.add('active');
                        currentStep = stepNumber;
                        updateProgress();
                    }

                    // Next step buttons
                    document.querySelectorAll('.next-step').forEach(button => {
                        button.addEventListener('click', function() {
                            const nextStep = parseInt(this.getAttribute('data-next'));

                            // Validate current step before proceeding
                            if (validateStep(currentStep)) {
                                goToStep(nextStep);

                                // If going to review step, update review content
                                if (nextStep === 6) {
                                    updateReviewContent();
                                }
                            }
                        });
                    });

                    // Previous step buttons
                    document.querySelectorAll('.prev-step').forEach(button => {
                        button.addEventListener('click', function() {
                            const prevStep = parseInt(this.getAttribute('data-prev'));
                            goToStep(prevStep);
                        });
                    });

                    // Step validation
                    function validateStep(step) {
                        let isValid = true;

                        if (step === 1) {
                            const name = document.getElementById('product-name').value;
                            const slug = document.getElementById('product-slug').value;

                            if (!name) {
                                alert('{{ __('Product name is required') }}');
                                isValid = false;
                            } else if (!slug) {
                                alert('{{ __('Product slug is required') }}');
                                isValid = false;
                            }
                        }

                        // Add validation for other steps as needed

                        return isValid;
                    }

                    // Update review content
                    function updateReviewContent() {
                        document.getElementById('review-name').textContent = document.getElementById('product-name').value;
                        document.getElementById('review-slug').textContent = document.getElementById('product-slug').value;
                        document.getElementById('review-description').textContent = document.getElementById('product-description').value.substring(0, 100) + '...';
                        document.getElementById('review-price').textContent = document.getElementById('product-price').value || '0.00';
                        document.getElementById('review-sale-price').textContent = document.getElementById('product-sale-price').value || 'None';
                        document.getElementById('review-sku').textContent = document.getElementById('product-sku').value;
                        document.getElementById('review-stock').textContent = document.getElementById('product-stock').value;

                        const categorySelect = document.getElementById('product-category');
                        document.getElementById('review-category').textContent = categorySelect.options[categorySelect.selectedIndex].text || 'Not selected';

                        document.getElementById('review-tags').textContent = document.getElementById('product-tags').value || 'None';
                    }

                    // Auto-generate slug from product name
                    document.getElementById('product-name').addEventListener('blur', function() {
                        const slugField = document.getElementById('product-slug');
                        if (!slugField.value) {
                            const slug = this.value
                                .toLowerCase()
                                .replace(/[^a-z0-9 -]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/-+/g, '-');
                            slugField.value = slug;
                        }
                    });

                    // Show/hide tax class field
                    document.getElementById('product-taxable').addEventListener('change', function() {
                        const taxClassContainer = document.getElementById('tax-class-container');
                        taxClassContainer.style.display = this.value === 'yes' ? 'block' : 'none';
                    });

                    // Form submission
                    document.getElementById('product-create-form').addEventListener('submit', function(e) {
                        e.preventDefault();
                        alert('{{ __('Product created successfully!') }}');
                        // Here you would typically submit the form via AJAX or allow natural form submission
                    });

                    // Initialize progress bar
                    updateProgress();
                });
            </script>
@endsection
