
<div class="tw-table-wrap">
    <table class="w-full text-left" id="myTable">
        <thead>
            <tr class="border-b border-main">
                <th class="px-4 py-3 w-10 no-sort">
                    <div class="mark-all-checkbox">
                        <input type="checkbox" class="all-checkbox w-4 h-4 rounded border-main text-primary focus:ring-primary">
                    </div>
                </th>
                <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__("ID")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest" style="min-width: 200px;">{{__("Name")}}</th>
                <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Type")}}</th>
                <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Categories")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Price")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Status")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__("Actions")}}</th>
            </tr>
        </thead>
        <tbody>
        @forelse($products['items'] as $product)
            <tr class="border-b border-main hover:bg-muted transition-colors">
                <td class="px-4 py-3.5">
                    <x-bulk-delete-checkbox :id="$product->id"/>
                </td>

                <td class="hidden md:table-cell px-4 py-3.5">
                    <span class="text-[11px] font-bold text-primary">{{__('#')}}{{$product->id}}</span>
                </td>

                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="product-table-img-wrap flex-shrink-0">
                            {!! render_image_markup_by_attachment_id($product->image_id) !!}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('tenant.admin.digital.product.edit', $product->id) }}" class="text-sm font-semibold text-dark hover:text-primary transition block truncate">
                                {!! Str::limit($product->name, 30) !!}
                            </a>
                            <p class="text-xs text-muted truncate mt-0.5">{{Str::words($product->summary, 5)}}</p>
                            @if($product->file == 'no file added')
                                <small class="text-danger text-[10px] font-semibold">{{__('No file added')}}</small>
                            @endif
                        </div>
                    </div>
                </td>

                <td class="hidden lg:table-cell px-4 py-3.5">
                    <span class="text-xs text-dark font-medium">{{ $product->productType()->name ?? '' }}</span>
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

                <td class="px-4 py-3.5">
                    @php
                        $price = $product->regular_price;
                        $regular_price = null;
                        if (!empty($product->sale_price) && $product->sale_price > 0)
                        {
                            $price = $product->sale_price;
                            $regular_price = $product->regular_price;
                        }
                    @endphp

                    @if($price > 0)
                        <span class="text-sm font-bold text-dark">{{ amount_with_currency_symbol($price) }}</span>
                        @if(!empty($regular_price))
                            <span class="block text-xs text-muted"><del>{{amount_with_currency_symbol($regular_price)}}</del></span>
                        @endif
                    @else
                        <span class="text-sm font-bold text-success">{{ __('Free') }}</span>
                    @endif
                </td>

                <td class="px-4 py-3.5">
                    <x-product::table.status :statuses="$statuses" :statusId="$product?->status_id" :id="$product->id"/>
                </td>

                <td class="px-4 py-3.5">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{dynamicRoute($product->slug)}}" class="tw-btn-icon tw-btn-icon-view" target="_blank" title="{{__('View')}}">
                            <i class="mdi mdi-eye-outline"></i>
                        </a>
                        <a href="{{ route('tenant.admin.digital.product.edit', $product->id) }}" class="tw-btn-icon tw-btn-icon-edit" title="{{__('Edit')}}">
                            <i class="mdi mdi-pencil-outline"></i>
                        </a>
                        <a href="{{ route('tenant.admin.digital.product.clone', $product->id) }}" class="tw-btn-icon" style="color: var(--color-info);" title="{{__('Clone')}}">
                            <i class="mdi mdi-content-copy"></i>
                        </a>
                        <button class="tw-btn-icon tw-btn-icon-danger delete-row" data-product-url="{{ route('tenant.admin.digital.product.destroy', $product->id) }}" title="{{__('Delete')}}">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <i class="mdi mdi-package-variant-closed-remove text-3xl text-muted"></i>
                        <p class="text-sm text-muted">{{__('No Product Available')}}</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="product-pagination">
    <div class="pagination-info">
        <p><strong>{{__('Per Page:')}}</strong> <span>{{ $products["per_page"] }}</span></p>
        <p><strong>{{__('From:')}}</strong> <span>{{ $products["from"] }}</span> <strong>{{__('To:')}}</strong> <span>{{ $products["to"] }}</span></p>
        <p><strong>{{__('Total Page:')}}</strong> <span>{{ $products["total_page"] }}</span></p>
        <p><strong>{{__('Total:')}}</strong> <span>{{ $products["total_items"] }}</span></p>
    </div>

    <div>
        <ul class="pagination-list">
            @if(count($products["links"]) > 1)
                @php
                    $links = $products["links"];
                    $current_page = $products["current_page"];
                @endphp
                @foreach(generateDynamicPagination($current_page, count($links)-1) as $index => $link)
                    @if($link === '...')
                        <li><a data-page="{{ $current_page }}" href="{{ $current_page }}" class="page-number">{{ $link }}</a></li>
                    @else
                        <li><a data-page="{{ $link }}" href="{{ $links[$link] }}" class="page-number {{ $link === $current_page ? 'current' : '' }}">{{ $link }}</a></li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>
