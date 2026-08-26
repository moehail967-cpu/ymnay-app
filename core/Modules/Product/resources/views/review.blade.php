@extends('tenant.admin.admin-master')
@section('title')
    {{ __('All Reviews') }}
@endsection

@section('content')
<x-flash-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-star-half-full text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Product Review List')}}</h3>
                <p class="text-xs text-muted">{{__('View product reviews from customers')}}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__("ID")}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Image")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest" style="min-width: 150px;">{{__("Product")}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("User")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Rating")}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Review Text")}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__("Actions")}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($review_list as $review)
                    @if(!empty($review->product))
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="hidden sm:table-cell px-4 py-3.5">
                            <span class="text-[11px] font-bold text-primary">{{__('#')}}{{$review->id}}</span>
                        </td>

                        <td class="hidden md:table-cell px-4 py-3.5">
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
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                    {{$review->rating}} {{__('Star')}}
                                </span>
                                <div class="flex items-center gap-0.5 mt-1">
                                    @for($i = 0; $i < $review->rating; $i++)
                                        <span class="product-star checked mdi mdi-star"></span>
                                    @endfor
                                    @for($i = 0; $i < 5 - $review->rating; $i++)
                                        <span class="product-star mdi mdi-star"></span>
                                    @endfor
                                </div>
                            </div>
                        </td>

                        <td class="hidden lg:table-cell px-4 py-3.5">
                            <p class="text-xs text-muted line-clamp-2">{{$review->review_text}}</p>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end">
                                <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-soft border border-main text-primary text-xs font-semibold hover:bg-primary hover:text-white hover:border-primary transition-all"
                                   href="{{dynamicRoute($review?->product?->slug)}}"
                                   target="_blank"
                                   title="{{__('View Product')}}">
                                    <i class="mdi mdi-eye-outline text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endif
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
        <div class="px-4 sm:px-6 py-4 border-t border-main">
            {!! $review_list->links() !!}
        </div>
    @endif
</div>
@endsection
