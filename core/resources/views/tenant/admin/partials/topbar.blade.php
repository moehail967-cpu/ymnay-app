@php
    $permission_limit = tenant()?->payment_log?->package;
    $page_count = \App\Models\Page::count();
    $blog_count = \Modules\Blog\Entities\Blog::count();
    $product_count = \Modules\Product\Entities\Product::withTrashed()->count();

    $page_limit = $permission_limit?->page_permission_feature == -1 ? __('Unlimited') : $permission_limit?->page_permission_feature;
    $blog_limit = $permission_limit?->blog_permission_feature == -1 ? __('Unlimited') : $permission_limit?->blog_permission_feature;
    $product_limit = $permission_limit?->product_permission_feature == -1 ? __('Unlimited') : $permission_limit?->product_permission_feature;
    $storage_limit = $permission_limit?->storage_permission_feature == -1 ? __('Unlimited') : $permission_limit?->storage_permission_feature;

    $storage_count = get_tenant_storage_info('mb');
    $storage_permission_feature = empty($permission_limit?->storage_permission_feature) ? 1 : $permission_limit?->storage_permission_feature;
    $storage_remaining_percent = 100-($storage_count/$storage_permission_feature)*100;

    // Inventory Warnings
    $threshold_amount = get_static_option('stock_threshold_amount');

    $inventory_product_items = \Modules\Product\Entities\ProductInventoryDetail::where('stock_count', '<', ($threshold_amount ?? 0)+1)
    ->whereHas('is_inventory_warn_able', function ($query) {
        $query->where('is_inventory_warn_able', 1);
    })
    ->select('id', 'product_id')
    ->get();

    $inventory_product_items_id = !empty($inventory_product_items) ? $inventory_product_items->pluck('product_id')->toArray() : [];

    $products = \Modules\Product\Entities\Product::with('inventory')
                    ->where('is_inventory_warn_able', 1)
                    ->whereHas('inventory', function ($query) use ($threshold_amount) {
                        $query->where('stock_count', '<', ($threshold_amount ?? 0) + 1);
                    })
                    ->select('id')
                    ->get();

    $products_id = !empty($products) ? $products->pluck('id')->toArray() : [];

    $every_filtered_product_id = array_unique(array_merge($inventory_product_items_id, $products_id));
    $all_products = \Modules\Product\Entities\Product::whereIn('id', $every_filtered_product_id)->select('id', 'name', 'is_inventory_warn_able')->get();

    $allocatedStorage = $permission_limit?->storage_permission_feature;
    $oneThirdOfStorage = $allocatedStorage - ($allocatedStorage * 20) / 100;
@endphp

<!-- Editorial Topbar -->
<header class="bg-secondary border-b border-main sticky top-0 z-30">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">

        <!-- Left: Hamburger (mobile) + Search Bar -->
        <div class="flex items-center gap-3 flex-1">
            <button onclick="toggleSidebar()"
                class="lg:hidden p-2 rounded-lg text-muted hover:bg-muted hover:text-dark transition-colors flex-shrink-0">
                <i class="mdi mdi-menu text-xl"></i>
            </button>
            <button onclick="toggleSidebarCollapse()"
                class="hidden lg:block p-2 rounded-lg text-muted hover:bg-muted hover:text-dark transition-colors flex-shrink-0"
                title="Toggle sidebar">
                <i class="mdi mdi-menu text-xl"></i>
            </button>

        </div>

        <!-- Right: Actions + Profile -->
        <div class="flex items-center gap-1.5">

            {{-- Tenant Info Dropdown --}}
            <div class="relative" id="tenantInfoDropdown">
                <button onclick="toggleDropdown('tenantInfoDropdown')"
                    class="relative p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors"
                    title="{{__('Resource Usage')}}">
                    <i class="mdi mdi-lightbulb-on-outline text-xl"></i>
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-72 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <h6 class="text-sm font-semibold text-dark">{{__('Resource Usage')}}</h6>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-subtle">{{__('Pages')}}</span>
                            <span class="text-xs font-bold text-dark">{{$page_count.'/'.$page_limit}}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-subtle">{{__('Blogs')}}</span>
                            <span class="text-xs font-bold {{ $blog_count == $permission_limit?->blog_permission_feature ? 'text-danger' : 'text-dark' }}">
                                {{$blog_count}}/{{$blog_limit}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-subtle">{{__('Products')}}</span>
                            <span class="text-xs font-bold {{ $product_count == $permission_limit?->product_permission_feature ? 'text-danger' : 'text-dark' }}">
                                {{$product_count}}/{{$product_limit}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-subtle">{{__('Storage')}}</span>
                            <span class="text-xs font-bold {{ $storage_remaining_percent >= $oneThirdOfStorage ? 'text-danger' : 'text-dark' }}">
                                {{round($storage_count, 3)}}/{{$storage_limit != 'Unlimited' ? $allocatedStorage : $storage_limit}} MB
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Warnings (Notifications) --}}
            <div class="relative" id="notifDropdown">
                <button onclick="toggleDropdown('notifDropdown')"
                    class="relative p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors">
                    <i class="mdi mdi-bell-outline text-xl"></i>
                    @if(count($all_products) > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"></span>
                    @endif
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-80 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <h6 class="text-sm font-semibold text-dark">{{__('Stock Reminder').' ('.count($all_products).')'}}</h6>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-subtle">
                        @forelse($all_products->take(10) as $product)
                            @php
                                $inventory = $product?->inventory?->stock_count;
                                $variant = $product->inventoryDetail->where('stock_count', '<=', $threshold_amount)->first();
                                $variant = !empty($variant) ? $variant->stock_count : [];
                                $stock = min($inventory, $variant);
                            @endphp
                            <a href="{{route('tenant.admin.product.edit', $product->id).'/inventory-tab'}}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-muted transition-colors">
                                <span
                                    class="flex-shrink-0 w-8 h-8 bg-warning-soft text-warning rounded-full flex items-center justify-center">
                                    <i class="mdi mdi-cart-arrow-down text-sm"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-dark truncate">{{$product->name}}</p>
                                    <p class="text-xs text-danger mt-0.5">{{sprintf(__('Remaining stock is %u'), $stock)}}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-subtle text-sm">
                                {{__('No data available')}}
                            </div>
                        @endforelse
                    </div>
                    @if(count($all_products) > 0)
                        <button type="button"
                            class="block w-full text-center px-4 py-2.5 text-sm text-brand font-medium hover:bg-muted border-t border-subtle"
                            data-bs-toggle="modal" data-bs-target="#warningModal">
                            {{__('See all warnings')}}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Messages / Activity --}}
            <div class="relative" id="msgDropdown">
                <button onclick="toggleDropdown('msgDropdown')"
                    class="relative p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors">
                    <i class="mdi mdi-email-outline text-xl"></i>
                    @if($new_message)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"></span>
                    @endif
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-80 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <h6 class="text-sm font-semibold text-dark">{{ $new_message . ' ' . __('Messages') }}</h6>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-subtle">
                        @foreach($all_messages as $message)
                            <a href="{{route(route_prefix().'admin.contact.message.view', $message->id)}}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-muted transition-colors">
                                <span
                                    class="flex-shrink-0 w-8 h-8 bg-blue-50 text-info rounded-full flex items-center justify-center">
                                    <i class="mdi mdi-email text-sm"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-dark truncate">{{__('Message from')}}
                                        <strong>{{ optional($message->form)->title }}</strong></p>
                                    <p class="text-xs text-subtle mt-0.5">
                                        {{ $message->created_at->diffForHumans() }}
                                        @if($message->status === 1)
                                            <span class="text-danger font-medium ml-1">{{__('New')}}</span>
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{route(route_prefix().'admin.contact.message.all')}}"
                        class="block text-center px-4 py-2.5 text-sm text-brand font-medium hover:bg-muted border-t border-subtle">
                        {{__('See All')}}
                    </a>
                </div>
            </div>

            {{-- Dark Mode Toggle --}}
{{--            <form method="post" action="{{route(route_prefix().'admin.general.basic.settings')}}" class="hidden sm:block">--}}
{{--                @csrf--}}
{{--                <input type="hidden" name="dark_mode_enable_check" value="on">--}}
{{--                <input type="hidden" name="dark_mode_enable" value="on">--}}
{{--                <button type="submit"--}}
{{--                    class="p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors"--}}
{{--                    title="{{__('Toggle Dark Mode')}}">--}}
{{--                    <i class="mdi mdi-theme-light-dark text-xl"></i>--}}
{{--                </button>--}}
{{--            </form>--}}

            {{-- Divider --}}
            <div class="hidden sm:block w-px h-8 bg-main mx-1.5"></div>

            {{-- Profile Dropdown --}}
            <div class="relative" id="profileDropdownContainer">
                <button onclick="toggleDropdown('profileDropdownContainer')"
                    class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-muted transition-colors">
                    @php
                        if (auth('admin')->user()->image != null) {
                            $image_id = auth('admin')->user()->image;
                        } else {
                            $image_id = get_static_option('placeholder_image');
                        }
                    @endphp
                    <div class="hidden sm:block text-right mr-1">
                        <p class="text-sm font-semibold text-dark leading-tight">{{auth('admin')->user()->name}}</p>
                        <p class="text-[10px] font-semibold text-subtle uppercase tracking-wider">
                            {{auth('admin')->user()->username}}</p>
                    </div>
                    <div class="relative">
                        @if($image_id != null)
                            <img src="{{ get_attachment_image_by_id($image_id)['img_url'] ?? global_asset('assets/landlord/uploads/media-uploader/no-image.jpg') }}"
                                alt="Profile" class="w-9 h-9 rounded-full object-cover ring-2 ring-sidebar-hover">
                        @else
                            <img src="{{global_asset('assets/landlord/uploads/media-uploader/no-image.jpg')}}" alt="Profile"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-sidebar-hover">
                        @endif
                    </div>
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-56 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <p class="text-sm font-semibold text-dark">{{auth('admin')->user()->name}}</p>
                        <p class="text-xs text-muted">{{auth('admin')->user()->email}}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{route('tenant.my.package.order.payment.logs')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-package-variant text-base text-success"></i>
                            {{__('Package Orders')}}
                        </a>
                        <a href="{{route('tenant.admin.edit.profile')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-account-edit text-base text-info"></i>
                            {{__('Edit Profile')}}
                        </a>
                        <a href="{{route('tenant.admin.change.password')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-key text-base text-warning"></i>
                            {{__('Change Password')}}
                        </a>

                        {{-- Visit Website --}}
                        <a href="{{tenant_url_with_protocol(tenant()->domain?->domain)}}" target="_blank"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-open-in-new text-base text-subtle"></i>
                            {{__('Visit Website')}}
                        </a>
                    </div>
                    <div class="border-t border-subtle">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('tenanat_logout_submit_btn').dispatchEvent(new MouseEvent('click'));"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-danger hover:bg-danger-soft transition-colors">
                            <i class="mdi mdi-logout text-base"></i>
                            {{__('Sign Out')}}
                            <form id="logout-form" action="{{ route('tenant.admin.logout') }}" method="POST"
                                class="hidden">
                                @csrf
                                <button class="hidden" type="submit" id="tenanat_logout_submit_btn"></button>
                            </form>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Dropdown toggle
    function toggleDropdown(id) {
        const container = document.getElementById(id);
        const panel = container.querySelector('.dropdown-panel');
        const isHidden = panel.classList.contains('hidden');

        // Close all other dropdowns
        document.querySelectorAll('.dropdown-panel').forEach(p => p.classList.add('hidden'));

        if (isHidden) {
            panel.classList.remove('hidden');
        }
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown-panel').forEach(panel => {
            if (!panel.parentElement.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });
    });

    // Fullscreen toggle
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    // Ctrl+K focuses the search input
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const search = document.getElementById('menuSearch');
            if (search) {
                search.focus();
                search.select();
            }
        }
    });
</script>

<!--  All Warnings Modal (Bootstrap modal - kept for compatibility) -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('All Stock Warnings')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @forelse($all_products as $product)
                        @php
                            $inventory = $product?->inventory?->stock_count;
                            $variant = $product->inventoryDetail->where('stock_count', '<=', $threshold_amount)->first();
                            $variant = !empty($variant) ? $variant->stock_count : [];
                            $stock = min($inventory, $variant);
                        @endphp

                        <div class="col-lg-12 col-md-12">
                            <div class="card warning-details-card mb-2">
                                <a class="dropdown-item" href="{{route('tenant.admin.product.edit', $product->id).'/inventory-tab'}}">
                                    <div class="preview-thumbnail d-flex">
                                        <div class="preview-icon text-warning">
                                            <i class="mdi mdi-cart-arrow-down"></i>
                                        </div>
                                        <h6 class="preview-subject font-weight-normal mb-1">{{$product->name}}</h6>
                                    </div>
                                    <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                        <p class="text-gray ellipsis mb-0 text-danger"> {{sprintf(__('Remaining stock is %u'), $stock)}} </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <h6 class="p-3 mb-0 text-center">{{__('No data available')}}</h6>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>
