@extends('tenant.admin.admin-master')
@section('title')
    {{ __('All Digital Product') }}
@endsection

@section('style')
@endsection

@section('content')

<x-flash-msg-tw/>

{{-- Search Panel Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-5">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3 cursor-pointer" id="product-list-title-flex">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-magnify text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Search Product Module')}}</h3>
                <p class="text-xs text-muted">{{__('Filter digital products by name, category, price and more')}}</p>
            </div>
        </div>
        <i class="mdi mdi-chevron-down text-muted text-lg transition-transform" id="search-toggle-icon"></i>
    </div>

    <div class="product-search-panel" id="product-search-form-wrap">
        <form id="product-search-form" action="{{ route('tenant.admin.digital.product.search') }}" method="get">
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-text-short text-primary"></i>
                            <input name="name" value="{{ request()->name ?? old('name') }}" placeholder="{{__('Product name...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="search-name">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-shape-outline text-primary"></i>
                            <input name="category" value="{{ old('category') }}" placeholder="{{__('Category...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="search-category">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Sub Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-shape-plus-outline text-primary"></i>
                            <input name="sub_category" value="{{ old('sub_category') }}" placeholder="{{__('Sub category...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Child Category')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-file-tree-outline text-primary"></i>
                            <input name="child_category" value="{{ old('child_category') }}" placeholder="{{__('Child category...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Price Range')}}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="flex items-center gap-2 bg-surface border border-main rounded-xl px-3 py-2 focus-within:border-primary transition">
                                <span class="text-xs text-muted">{{__('From')}}</span>
                                <input name="from_price" value="{{ old('from_price') }}" placeholder="0"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 w-full">
                            </div>
                            <div class="flex items-center gap-2 bg-surface border border-main rounded-xl px-3 py-2 focus-within:border-primary transition">
                                <span class="text-xs text-muted">{{__('To')}}</span>
                                <input name="to_price" value="{{ old('to_price') }}" placeholder="999"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 w-full">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Created Date Range')}}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-range-outline text-primary"></i>
                            <input name="date_range" value="{{ old('date_range') }}" placeholder="{{__('Select date range...')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="search-date_range">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Order By')}}</label>
                        <select name="order_by" class="lnd-input" id="search-order_by">
                            <option value="">{{__('Select Order By')}}</option>
                            <option value="asc">{{__('ASC')}}</option>
                            <option value="desc">{{__('DESC')}}</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="px-4 sm:px-6 py-3 border-t border-main flex items-center justify-end" style="background: var(--color-bg-secondary);">
                <button id="product-search-button" type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                    <i class="mdi mdi-magnify text-base"></i> {{__('Search')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Product List Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-package-variant-closed text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product List')}}</h3>
                <p class="text-xs text-muted">{{__('Manage all your digital products')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <x-bulk-action/>
            <div class="flex items-center gap-2">
                <label for="number-of-item" class="text-xs text-muted font-semibold whitespace-nowrap">{{__('Rows')}}</label>
                <select name="count" id="number-of-item" class="lnd-input !py-1.5 !text-xs !min-w-[60px]">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <a href="{{route('tenant.admin.digital.product.trash.all')}}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all">
                <i class="mdi mdi-delete-outline text-sm"></i> {{__('Trash')}}
            </a>
            <a href="{{route('tenant.admin.digital.product.create')}}"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-plus text-sm"></i> {{__('Add New Product')}}
            </a>
        </div>
    </div>

    <div id="product-table-body">
        {!! view("digitalproduct::admin.product.search", compact("products","statuses")) !!}
    </div>
</div>

@endsection

@section('scripts')
    <x-digitalproduct::table.status-js />
    <x-product::table.bulk-action-js :url="route('tenant.admin.digital.product.bulk.destroy')"/>
    <script>
        $(function (){
            $("#search-date_range").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M j, Y",
            });

            // Search panel toggle
            $(document).on("click","#product-list-title-flex", function (){
                let panel = document.getElementById('product-search-form-wrap');
                let icon = document.getElementById('search-toggle-icon');
                panel.classList.toggle('open');
                icon.style.transform = panel.classList.contains('open') ? 'rotate(180deg)' : '';
            });

            $(document).on("click","#product-search-button", function (e){
                e.preventDefault();
                $("#product-search-form").trigger("submit");
            });

            $(document).on("submit","#product-search-form", function (e){
                e.preventDefault();
                let form_data = $("#product-search-form").serialize().toString();
                form_data += "&count=" + $("#number-of-item").val();

                send_ajax_request("GET",null,$(this).attr("action") + "?" + form_data, () => {
                    $(".loader").fadeIn();
                }, (data) => {
                    $("#product-table-body").html(data);
                    $(".loader").fadeOut();
                }, (data) => {
                    prepare_errors(data);
                });
            });

            $(document).on("change","#number-of-item", function (e){
                e.preventDefault();
                let form_data = $("#product-search-form").serialize().toString();
                form_data += "&count=" + $(this).val();

                send_ajax_request("GET",null,$("#product-search-form").attr("action") + "?" + form_data, () => {
                    $(".loader").show();
                }, (data) => {
                    $("#product-table-body").html(data);
                    $(".loader").hide();
                }, (data) => {
                    prepare_errors(data);
                });
            });

            // Pagination
            $(document).on("click", ".pagination-list li a", function(e) {
                e.preventDefault();
                $(".pagination-list li a").removeClass("current");
                $(this).addClass("current");

                send_ajax_request("GET",null,$(this).attr("href"), () => {
                    $(".loader").show();
                }, (data) => {
                    $("#product-table-body").html(data);
                    $(".loader").hide();
                }, (data) => {
                    prepare_errors(data);
                });
            });

            // Delete row
            $(document).on("click", ".delete-row", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to revert this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{ __("Yes, delete it!") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        send_ajax_request("GET",null,$(this).data("product-url"), () => {
                            toastr.warning("{{ __('Request sent, please wait...') }}");
                        }, (data) => {
                            Swal.fire('{{ __("Deleted!") }}', '{{ __("Product has been deleted.") }}', 'success');
                            let product = $(this).closest('tr');
                            product.fadeOut(300, () => {
                                product.remove();
                                ajax_toastr_success_message(data);
                            });
                        }, (data) => {
                            prepare_errors(data);
                        });
                    }
                });
            });

            function send_ajax_request(request_type, request_data, url, before_send, success_response, errors) {
                $.ajax({
                    url: url,
                    type: request_type,
                    headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" },
                    beforeSend: (typeof before_send === "function") ? before_send : () => {},
                    processData: false,
                    contentType: false,
                    data: request_data,
                    success: (typeof success_response === "function") ? success_response : () => {},
                    error: (typeof errors === "function") ? errors : () => {}
                });
            }

            function prepare_errors(data) {
                let errors = data.responseJSON;
                if (errors.success != undefined) {
                    toastr.error(errors.custom_msg);
                }
                $.each(errors.errors, function (index, value) {
                    toastr.error(value[0]);
                });
            }

            function ajax_toastr_success_message(data) {
                if (data.success) {
                    toastr.success(data.msg);
                } else {
                    toastr.warning(data.msg);
                }
            }
        });
    </script>
@endsection
