@extends('tenant.admin.admin-master')
@section('title')
    {{ __('Trashed Products') }}
@endsection

@section('style')
    <x-product::variant-info.css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

@endsection

@section('content')
    @php
        $allProduct = '';
            if (!$products->isEmpty())
                {
                    if (count($products) > 1)
                        {
                            $allProduct = $products->pluck('id')->toArray();
                            $allProduct = implode('|',$allProduct);
                        } else {
                            $allProduct = current(current($products))->id;
                        }
                }
    @endphp

    <x-landlord-flash-msg/>
{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-delete-clock-outline text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Trash')}}</h3>
                <p class="text-xs text-muted">{{__('Manage trashed products')}}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="javascript:void(0)"
               class="delete-all inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all"
               data-product-delete-all-url="{{ route('tenant.admin.product.trash.empty') }}">
                <i class="mdi mdi-delete-forever-outline text-sm"></i>
                {{__('Empty Trash')}}
            </a>
            <a class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition"
               href="{{route('tenant.admin.product.all')}}">
                <i class="mdi mdi-arrow-left text-sm"></i>
                {{__('Back')}}
            </a>
        </div>
    </div>

    {{-- Bulk Action --}}
    <div class="px-4 sm:px-6 py-3 border-b border-main">
        <x-bulk-action/>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="myTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 w-10">
                        <div class="mark-all-checkbox">
                            <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-main text-primary focus:ring-primary">
                        </div>
                    </th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest" style="min-width: 200px;">{{__("Name")}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Brand")}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Categories")}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Stock Qty")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__("Actions")}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 py-3.5">
                            <x-bulk-delete-checkbox :id="$product->id"/>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="product-table-img-wrap flex-shrink-0">
                                    {!! render_image_markup_by_attachment_id($product->image_id) !!}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-sm font-semibold text-dark block truncate">{{ $product->name }}</span>
                                    <p class="text-xs text-muted truncate mt-0.5">{{Str::words($product->summary, 10)}}</p>
                                </div>
                            </div>
                        </td>

                        <td class="hidden md:table-cell px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="product-table-img-wrap flex-shrink-0">
                                    {!! render_image_markup_by_attachment_id($product?->brand?->image_id) !!}
                                </div>
                                <span class="text-xs text-dark font-medium">{{ $product?->brand?->name }}</span>
                            </div>
                        </td>

                        <td class="hidden md:table-cell px-4 py-3.5">
                            <div class="space-y-0.5">
                                @if($product?->category?->name)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-secondary text-[10px] font-semibold text-dark">
                                        {{ $product?->category?->name }}
                                    </span>
                                @endif
                                @if($product?->subCategory?->name)
                                    <span class="block text-[10px] text-muted">{{ $product?->subCategory?->name }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="hidden sm:table-cell px-4 py-3.5">
                            <span class="text-sm font-bold text-dark">{{ $product?->inventory?->stock_count }}</span>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('tenant.admin.product.trash.restore', $product->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-success-soft border border-main text-success text-xs font-semibold hover:bg-success hover:text-white hover:border-success transition-all"
                                   title="{{__('Restore')}}">
                                    <i class="mdi mdi-restore text-sm"></i>
                                </a>
                                <a data-product-delete-url="{{ route('tenant.admin.product.trash.delete', $product->id) }}"
                                   href="javascript:void(0)"
                                   class="product-delete inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all"
                                   title="{{__('Permanent Delete')}}">
                                    <i class="mdi mdi-delete-forever-outline text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="mdi mdi-delete-empty-outline text-3xl text-muted"></i>
                                <p class="text-sm text-muted">{{__('No Trashed Product Available')}}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
    <x-product::table.bulk-action-js :url="route('tenant.admin.product.trash.bulk.destroy')"/>
    <script>
        $(document).on("click", ".delete-all", function (e) {
            e.preventDefault();
            let el = $(this);
            let delete_url = el.data('product-delete-all-url');
            let allIds = '{{$allProduct}}';

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1F51FF',
                cancelButtonColor: '#D2042D',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (allIds != '') {
                        $(this).html('<i class="mdi mdi-loading mdi-spin mr-1"></i>{{__("Deleting")}}');
                        $.ajax({
                            'type': "POST",
                            'url': delete_url,
                            'data': {
                                _token: "{{csrf_token()}}",
                                ids: allIds
                            },
                            success: function (data) {
                                toastr.success('{{ __('Trash in Empty') }}');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000)
                            }
                        });
                    }
                }
            });
        });

        $(document).on("click", ".product-delete", function (e) {
            e.preventDefault();
            let delete_url = $(this).data('product-delete-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1F51FF',
                cancelButtonColor: '#D2042D',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.replace(delete_url);
                }
            });
        });
    </script>
@endsection
