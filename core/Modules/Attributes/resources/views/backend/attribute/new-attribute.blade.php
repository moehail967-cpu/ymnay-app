@extends('tenant.admin.admin-master')

@section('title') {{__('New Variant')}} @endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main max-w-2xl mx-auto">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-plus-circle-outline text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Variant')}}</h3>
                <p class="text-xs text-muted">{{__('Create a new product attribute with terms')}}</p>
            </div>
        </div>
        @can('product-attribute-list')
            <a href="{{ route('tenant.admin.products.attributes.all') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:border-hover transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Variants')}}
            </a>
        @endcan
    </div>

    {{-- Form --}}
    @can('product-attribute-create')
    <form action="{{ route('tenant.admin.products.attributes.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="p-5 space-y-5">
            {{-- Title --}}
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Title')}} <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="{{__('Title')}}" class="lnd-input">
            </div>

            {{-- Terms Repeater --}}
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Terms')}}</label>
                <div id="terms_container" class="space-y-2">
                    <div class="term-row flex items-center gap-2">
                        <input type="text" name="terms[]" placeholder="{{__('terms')}}" class="lnd-input flex-1">
                        <button type="button" class="add_term w-9 h-9 rounded-lg bg-success-soft border border-main flex items-center justify-center text-success hover:text-white hover:bg-success hover:border-success transition-all flex-shrink-0">
                            <i class="mdi mdi-plus text-base"></i>
                        </button>
                        <button type="button" class="remove_term w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all flex-shrink-0">
                            <i class="mdi mdi-minus text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
            </button>
        </div>
    </form>
    @endcan
</div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            // Add new term row
            $(document).on('click', '.add_term', function (e) {
                e.preventDefault();
                var newRow = '<div class="term-row flex items-center gap-2">' +
                    '<input type="text" name="terms[]" placeholder="{{__('terms')}}" class="lnd-input flex-1">' +
                    '<button type="button" class="add_term w-9 h-9 rounded-lg bg-success-soft border border-main flex items-center justify-center text-success hover:text-white hover:bg-success hover:border-success transition-all flex-shrink-0">' +
                    '<i class="mdi mdi-plus text-base"></i></button>' +
                    '<button type="button" class="remove_term w-9 h-9 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:text-white hover:bg-danger hover:border-danger transition-all flex-shrink-0">' +
                    '<i class="mdi mdi-minus text-base"></i></button>' +
                    '</div>';
                $('#terms_container').append(newRow);
            });

            // Remove term row (keep at least 1)
            $(document).on('click', '.remove_term', function (e) {
                e.preventDefault();
                if ($('#terms_container .term-row').length > 1) {
                    $(this).closest('.term-row').remove();
                }
            });
        });
    })(jQuery);
    </script>
@endsection
