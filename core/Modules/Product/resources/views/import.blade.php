@extends('tenant.admin.admin-master')
@section('title')
    {{__('Import Product')}}
@endsection
@section('site-title')
    {{__('Import Product')}}
@endsection

@section('content')

{{-- Import Result Alerts --}}
@if(isset($importResult))
    @if($importResult['status'] == 'success')
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success-soft border border-main mb-4">
            <i class="mdi mdi-check-circle-outline text-success text-lg flex-shrink-0"></i>
            <div class="text-sm text-dark font-medium">{{ $importResult['message'] }}</div>
        </div>
    @elseif($importResult['status'] == 'partial')
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success-soft border border-main mb-4">
            <i class="mdi mdi-check-circle-outline text-success text-lg flex-shrink-0"></i>
            <div class="text-sm text-dark font-medium">{{ $importResult['imported'] }} {{__('product(s) imported successfully.')}}</div>
        </div>
        @if(!empty($importResult['reasons']))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-warning-soft border border-main mb-4">
                <i class="mdi mdi-alert-outline text-warning text-lg flex-shrink-0"></i>
                <div>
                    <p class="text-sm text-dark font-medium mb-2">{{ $importResult['skipped'] }} {{__('product(s) were skipped:')}}</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($importResult['reasons'] as $reason)
                            <li class="text-sm text-muted">{{ $reason }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @elseif($importResult['status'] == 'failed')
        <div class="flex items-start gap-3 p-4 rounded-xl bg-danger-soft border border-main mb-4">
            <i class="mdi mdi-alert-circle-outline text-danger text-lg flex-shrink-0"></i>
            <div>
                <p class="text-sm text-dark font-medium">{{ $importResult['message'] }}</p>
                @if(!empty($importResult['reasons']))
                    <ul class="list-disc list-inside space-y-1 mt-2">
                        @foreach($importResult['reasons'] as $reason)
                            <li class="text-sm text-muted">{{ $reason }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @elseif($importResult['status'] == 'error')
        <div class="flex items-start gap-3 p-4 rounded-xl bg-danger-soft border border-main mb-4">
            <i class="mdi mdi-alert-circle-outline text-danger text-lg flex-shrink-0"></i>
            <div class="text-sm text-dark font-medium">{{ $importResult['message'] }}</div>
        </div>
    @endif
@endif

@if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-xl bg-success-soft border border-main mb-4">
        <i class="mdi mdi-check-circle-outline text-success text-lg flex-shrink-0"></i>
        <div class="text-sm text-dark font-medium">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="flex items-start gap-3 p-4 rounded-xl bg-danger-soft border border-main mb-4">
        <i class="mdi mdi-alert-circle-outline text-danger text-lg flex-shrink-0"></i>
        <div class="text-sm text-dark font-medium">{{ session('error') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="flex items-start gap-3 p-4 rounded-xl bg-danger-soft border border-main mb-4">
        <i class="mdi mdi-alert-circle-outline text-danger text-lg flex-shrink-0"></i>
        <div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-muted">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Main Import Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-file-import-outline text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Import Products from CSV')}}</h3>
            <p class="text-xs text-muted">{{__('Upload a CSV file to bulk import products')}}</p>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-5">

        {{-- Sample Download Section --}}
        <div class="flex items-start gap-4 p-4 rounded-xl bg-info-soft border border-main">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-file-delimited-outline text-primary text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-dark font-urbanist mb-1">{{__('Need help with CSV format?')}}</h4>
                <p class="text-xs text-muted mb-3">
                    {{__('Download a sample CSV with example data from your existing products, or download an empty template to get started.')}}
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('tenant.products.download.sample') }}"
                       class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                        <i class="mdi mdi-download"></i>
                        {{__('Download Sample CSV')}}
                    </a>
                    <a href="{{ route('tenant.products.download.empty') }}"
                       class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                        <i class="mdi mdi-download-outline"></i>
                        {{__('Download Empty Template')}}
                    </a>
                </div>
            </div>
        </div>

        {{-- Required Fields Info --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-warning-soft border border-main">
            <i class="mdi mdi-alert-outline text-warning text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <h5 class="text-sm font-bold text-dark font-urbanist mb-2">{{__('Required Fields')}}</h5>
                <ul class="space-y-1.5">
                    <li class="text-sm text-muted"><strong class="text-dark">name</strong> - {{__('Product name (required)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">slug</strong> - {{__('URL friendly name (required, must be unique)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">summary</strong> - {{__('Short product summary (optional)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">description</strong> - {{__('Detailed product description (required, min 10 characters)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">status_id</strong> - {{__('Product status ID (1 = active, 2 = inactive)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">sale_price</strong> - {{__('Sale price (required)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">sku</strong> - {{__('Stock keeping unit (required, must be unique)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">stock_count</strong> - {{__('Available stock quantity (required)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">uom</strong> - {{__('Unit of measurement (required)')}}</li>
                    <li class="text-sm text-muted"><strong class="text-dark">image_url</strong> - {{__('Direct URL to product image (optional, http/https)')}}</li>
                </ul>
                <p class="text-xs text-muted mt-2">
                    <strong class="text-dark">{{__('Note:')}}</strong> {{__('The sample CSV includes all fields. Optional fields can be left empty.')}}
                </p>
            </div>
        </div>

        {{-- Upload Form --}}
        <form action="{{ route('tenant.products.import.preview') }}" method="POST" enctype="multipart/form-data" id="csvUploadForm">
            @csrf
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                    {{__('Upload CSV')}} <span class="text-danger">*</span>
                </label>
                <div class="product-upload-area" id="uploadArea">
                    <div class="flex flex-col items-center gap-2">
                        <i class="mdi mdi-cloud-upload-outline text-4xl text-muted"></i>
                        <p class="text-sm text-dark font-medium">{{__('Click to upload')}} <span class="text-muted font-normal">{{__('or drag and drop')}}</span></p>
                        <p class="text-xs text-muted">{{__('CSV files only (.csv, .txt)')}}</p>
                    </div>
                    <input type="file" name="csv_file" id="csv_file" class="hidden" required accept=".csv,.txt">
                </div>

                {{-- File selected indicator --}}
                <div id="fileSelected" class="hidden mt-3 flex items-center gap-2.5 p-3 rounded-xl bg-success-soft border border-main">
                    <i class="mdi mdi-check-circle text-success text-lg"></i>
                    <span class="text-sm text-dark font-medium">{{__('File selected:')}}</span>
                    <span class="text-sm text-muted" id="fileName"></span>
                </div>

                {{-- Error message for invalid file types --}}
                <div id="fileError" class="hidden mt-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-danger-soft border border-main">
                        <i class="mdi mdi-alert-circle-outline text-danger text-lg flex-shrink-0"></i>
                        <span class="text-sm text-muted">{{__('Please upload a valid CSV file (.csv or .txt).')}}</span>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-success text-white text-sm font-semibold hover:opacity-90 transition" id="submitBtn">
                    <i class="mdi mdi-eye-outline"></i>
                    {{__('Preview & Map Fields')}}
                    <span class="hidden" id="submitSpinner">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('csv_file');
            const fileSelected = document.getElementById('fileSelected');
            const fileNameDisplay = document.getElementById('fileName');
            const fileError = document.getElementById('fileError');
            const submitBtn = document.getElementById('submitBtn');
            const submitSpinner = document.getElementById('submitSpinner');

            // Show selected file name
            function showFileName(input) {
                fileError.classList.add('hidden');
                const file = input.files[0];
                if (file) {
                    const validTypes = ['text/csv', 'text/plain', 'application/csv'];
                    if (!validTypes.includes(file.type) && !file.name.match(/\.(csv|txt)$/i)) {
                        fileError.classList.remove('hidden');
                        fileInput.value = '';
                        fileSelected.classList.add('hidden');
                        return;
                    }
                    fileNameDisplay.textContent = file.name;
                    fileSelected.classList.remove('hidden');
                } else {
                    fileSelected.classList.add('hidden');
                }
            }

            // Handle click to trigger file input
            uploadArea.addEventListener('click', () => {
                fileInput.click();
            });

            // Handle file input change
            fileInput.addEventListener('change', () => {
                showFileName(fileInput);
            });

            // Drag-and-drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.classList.remove('dragover');
                }, false);
            });

            uploadArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                showFileName(fileInput);
            }, false);

            // Show spinner on form submit
            document.getElementById('csvUploadForm').addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                submitSpinner.classList.remove('hidden');
            });
        });
    </script>
@endsection
