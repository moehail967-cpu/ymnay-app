
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
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest" style="min-width: 200px;">{{__("Product")}}</th>
                <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Brand")}}</th>
                <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Categories")}}</th>
                <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Tax")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Stock")}}</th>
                <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Variant")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__("Status")}}</th>
                <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__("Actions")}}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products['items'] as $product)
                @php
                    $threshold_amount = get_static_option('stock_threshold_amount') ?? 5;
                    $stock_over = $product?->inventory?->stock_count <= $threshold_amount;
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors {{ $stock_over ? 'product-row-warning' : '' }}">
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
                                <a href="{{ route('tenant.admin.product.edit', $product->id) }}" class="text-sm font-semibold text-dark hover:text-primary transition block truncate">
                                    {!! Str::limit($product->name, 30) !!}
                                </a>
                                <p class="text-xs text-muted truncate mt-0.5">{{Str::limit($product->summary, 40)}}</p>
                            </div>
                        </div>
                    </td>

                    <td class="hidden lg:table-cell px-4 py-3.5">
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

                    <td class="hidden lg:table-cell px-4 py-3.5">
                        @if($product?->product_tax_class)
                            <span class="text-xs text-dark">{{ $product?->product_tax_class?->name }}</span>
                        @else
                            <span class="text-xs text-muted">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold {{ $stock_over ? 'text-danger' : 'text-dark' }}"
                              title="{{ $product->inventory_detail_count === 0 ? __('Quantity') : __('Stock quantity') }}">
                            {{ $product?->inventory?->stock_count }}
                        </span>
                    </td>

                    <td class="hidden sm:table-cell px-4 py-3.5">
                        @if($product->inventory_detail_count)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Yes')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-secondary text-muted text-[10px] font-bold uppercase">
                                {{__('No')}}
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3.5">
                        <x-product::table.status :statuses="$statuses" :statusId="$product?->status_id" :id="$product->id"/>
                    </td>

                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">
                                <a href="{{ route('tenant.admin.product.edit', $product->id) }}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all"
                                   title="{{__('Edit')}}">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>

                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                <div class="row-action-menu hidden">
                                    <a href="{{ route('tenant.admin.product.edit', $product->id) }}" class="action-item">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                        {{__('Edit Product')}}
                                    </a>

                                    <a href="{{dynamicRoute($product->slug)}}" class="action-item" target="_blank">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-eye-outline text-info"></i></span>
                                        {{__('View Product')}}
                                    </a>

                                    <a href="{{ route('tenant.admin.product.clone', $product->id) }}" class="action-item">
                                        <span class="action-icon bg-success-soft"><i class="mdi mdi-content-copy text-success"></i></span>
                                        {{__('Duplicate')}}
                                    </a>

                                    @php
                                        $sku = html_entity_decode($product?->inventory?->sku ?? '');
                                        $isValidSku = preg_match('/^[A-Z0-9\-\. \$\/\+%]+$/', strtoupper($sku));
                                    @endphp
                                    @if($isValidSku)
                                    <button type="button" class="action-item barcode-view-btn"
                                            data-product-name="{{$product->name}}"
                                            data-sku="{{$product?->inventory?->sku}}"
                                            data-barcode="{{DNS1D::getBarcodePNG($sku, 'C39+', 3, 80, [1, 1, 1], true)}}">
                                        <span class="action-icon bg-warning-soft"><i class="mdi mdi-barcode text-warning"></i></span>
                                        {{__('View Barcode')}}
                                    </button>
                                    @endif

                                    <a href="{{ route('tenant.admin.product.export', $product->id) }}" class="action-item">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-download-outline text-info"></i></span>
                                        {{__('Export CSV')}}
                                    </a>

                                    <div class="menu-divider"></div>

                                    <button type="button" class="action-item action-danger delete-row"
                                            data-product-url="{{ route('tenant.admin.product.destroy', $product->id) }}">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="mdi mdi-package-variant-closed-remove text-3xl text-muted"></i>
                            <p class="text-sm text-muted">{{__('No products found')}}</p>
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

{{-- Barcode Modal --}}
<div id="barcode_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="barcode_modal_backdrop"></div>
    <div class="relative bg-surface rounded-2xl border border-main w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-main flex-shrink-0">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-barcode text-warning text-base"></i>
            </div>
            <div class="flex-1">
                <h5 class="text-sm font-bold text-dark font-urbanist" id="barcode_modal_title">{{__('Barcode')}}</h5>
                <p class="text-[11px] text-muted">{{__('Product barcode preview')}}</p>
            </div>
            <button type="button" id="barcode_modal_close" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                <i class="mdi mdi-close text-muted"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-5">
            <div class="barcode-canvas-wrapper">
                <canvas id="barcodeCanvas" width="700px" height="200px"></canvas>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main flex-shrink-0" style="background: var(--color-bg-secondary);">
            <button type="button" onclick="closeModal('barcode_modal')"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                {{__('Close')}}
            </button>
            <a href="#0" download="#" class="download-barcode-btn inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
                <i class="mdi mdi-download-outline text-base"></i> {{__('Download')}}
            </a>
        </div>
    </div>
</div>

<script>
    // Row action dropdown handler
    window.toggleRowMenu = function (btn) {
        var menu = btn.nextElementSibling;
        var isHidden = menu.classList.contains('hidden');

        document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });

        if (isHidden) {
            var rect = btn.getBoundingClientRect();
            var menuHeight = 320;
            var spaceBelow = window.innerHeight - rect.bottom;
            var spaceAbove = rect.top;

            menu.style.right = (window.innerWidth - rect.right) + 'px';
            menu.style.left = 'auto';

            if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.style.bottom = 'auto';
            } else {
                menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                menu.style.top = 'auto';
            }
            menu.classList.remove('hidden');
        }
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.row-action-wrap')) {
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        }
    });

    window.addEventListener('scroll', function (e) {
        if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
        document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
    }, true);

    document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
        wrap.addEventListener('scroll', function () {
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        });
    });

    // Make openModal/closeModal available globally for barcode modal
    window.openModal = window.openModal || function(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };
    window.closeModal = window.closeModal || function(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };
</script>
