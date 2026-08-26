@extends('tenant.admin.admin-master')
@section('title')
    {{ __('All Reviews') }}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-flash-msg-tw/>

<div class="bg-surface rounded-xl shadow-main border border-main">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-star-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Review List')}}</h3>
                <p class="text-xs text-muted">{{__('Manage digital product reviews')}}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="reviewTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__("ID")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Image")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Product")}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("User")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Rating")}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Review Text")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__("Actions")}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($review_list as $review)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}}{{$review->id}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="product-table-img-wrap">
                            {!! render_image_markup_by_attachment_id($review?->product?->image_id) !!}
                        </div>
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$review?->product?->name}}</span>
                    </td>

                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-xs text-dark font-medium">{{$review?->user?->name}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div>
                            <span class="text-xs font-semibold text-dark">{{$review->rating}} {{__('Star')}}</span>
                            <div class="flex items-center gap-0.5 mt-1">
                                @for($i = 0; $i < $review->rating; $i++)
                                    <span class="dp-star checked mdi mdi-star"></span>
                                @endfor
                                @for($i = 0; $i < 5 - $review->rating; $i++)
                                    <span class="dp-star mdi mdi-star"></span>
                                @endfor
                            </div>
                        </div>
                    </td>

                    <td class="hidden lg:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{Str::limit($review->review_text, 60)}}</span>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <a href="{{dynamicRoute($review?->product->slug)}}" class="tw-btn-icon tw-btn-icon-view" target="_blank" title="{{__('View Product')}}">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="mdi mdi-star-off-outline text-3xl text-muted"></i>
                            <p class="text-sm text-muted">{{__('No Review Available')}}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($review_list->hasPages())
        <div class="p-4 border-t border-main">
            {!! $review_list->links() !!}
        </div>
    @endif
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#reviewTable')) {
                $('#reviewTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }
        });
    })(jQuery);
    </script>
@endsection
