@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('My Shops')}}
@endsection

@section('title')
    {{__('My Shops')}}
@endsection

@section('section')
    @php
        $user = Auth::guard('web')->user();
    @endphp

    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
            <div class="px-6 py-2.5 flex flex-col lg:flex-row gap-4 lg:gap-0 items-center justify-between">
                <div class="flex items-center w-full lg:w-auto">
                    <!-- Mobile Menu Button -->
                    <button id="menuBtn"
                        class="block lg:hidden text-sub2Title hover:text-teal-600 focus:outline-none pr-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="icon-base ti tabler-menu-2 icon-24px sm:icon-28px"></i>
                    </button>

                    <div class="ml-3 lg:ml-0 flex-1 lg:flex-none">
                        <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 leading-tight">
                            {{__('My Shops')}}</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            {{__('All your online stores in one place')}}</p>
                    </div>
                </div>

                <button
                    class="w-full lg:w-auto open-purchase-modal btn-primary text-white bg-primary px-4 py-2.5 sm:px-6 lg:px-6 sm:py-2.5 lg:py-2.5 rounded-lg font-normal lg:font-medium shadow-lg text-sm sm:text-base md:text-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 transition-all hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] min-w-[120px]">
                    {{__('Create Shop')}}
                </button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <div class="rounded-xl border border-borderCS" style="background-color: var(--section-bg-1, #ffffff)">
                <div class="p-6 border-b border-borderCS flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                    <div>
                        <h3 class="text-xl font-medium text-secondary">{{__('All Shops')}}</h3>
                        <p class="text-sm text-sub2Title mt-1">
                            {{__('Total')}}:
                            <span id="shop-count" class="font-semibold text-secondary">{{ $tenant_details->total() }}</span>
                        </p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <i class="ti tabler-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <input id="shop-search" type="text"
                            placeholder="{{__('Search by name, URL, date...')}}"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-borderCS rounded-lg outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition placeholder-gray-400 text-gray-700">
                        <button id="shop-search-clear" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="ti tabler-x text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-base font-medium text-secondary">{{__('#')}}</th>
                            <th class="text-left py-3 px-4 text-base font-medium text-secondary">{{__('Site')}}</th>
                            <th class="text-left py-3 px-4 text-base font-medium text-secondary">{{__('Created')}}</th>
                            <th class="text-right py-3 px-4 text-base font-medium text-secondary">{{__('Actions')}}</th>
                        </tr>
                        </thead>
                        <tbody id="shop-table-body">
                        @forelse($tenant_details as $key => $data)
                            @php
                                $url = '';
                                $central = '.' . env('CENTRAL_DOMAIN');

                                if (!empty($data->custom_domain?->custom_domain) && $data->custom_domain?->custom_domain_status == 'connected') {
                                    $custom_url = $data->custom_domain?->custom_domain;
                                    $url = tenant_url_with_protocol($custom_url);
                                } else {
                                    $local_url = $data->id . $central;
                                    $url = tenant_url_with_protocol($local_url);
                                }

                                $hash_token = hash_hmac('sha512', $user->username . '_' . $data->id, $data->unique_key);
                                $visit_url = $url;
                                $admin_login_url = $url . '/token-login/' . $hash_token;
                            @endphp

                            @php $row_date = $data->created_at?->format('d M Y') ?? ''; @endphp
                            <tr class="shop-row {{ !$loop->last ? 'border-b border-borderCS' : '' }}"
                                data-search="{{ strtolower($data->id . ' ' . $url . ' ' . $row_date) }}">
                                <td class="py-4 px-4">
                                    <div
                                        class="w-8 h-8 rounded-full bg-primary/10 text-[#0C4D54] flex items-center justify-center text-sm font-semibold">
                                        {{ $tenant_details->firstItem() + $key }}
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                                        <span class="text-base font-normal text-sub2Title">{{ $url }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-sm text-sub2Title">
                                        {{ $data->created_at?->format('d M Y') ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button
                                        class="action-btn text-gray-400 hover:text-sub2Title border rounded-md hover:bg-gray-50"
                                        data-visit-url="{{ $visit_url }}"
                                        data-admin-url="{{ $admin_login_url }}"
                                        data-tenant-id="{{ $data->id }}"
                                        data-delete-url="{{ route('landlord.user.shop.delete', $data->id) }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400">
                                    <i class="ti tabler-building-store text-4xl mb-2 block"></i>
                                    <p class="text-sm">{{__('No shops found.')}}</p>
                                    <button class="open-purchase-modal mt-3 text-sm text-primary hover:underline font-medium">
                                        {{__('Create your first shop')}}
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="no-search-results" class="hidden">
                            <td colspan="4" class="py-10 text-center text-gray-400">
                                <i class="ti tabler-search-off text-3xl mb-2 block"></i>
                                <p class="text-sm">{{__('No shops match your search.')}}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if($tenant_details->hasPages())
                    <div class="p-6 border-t border-borderCS">
                        {!! $tenant_details->links('vendor.pagination.tailwind') !!}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Action Popover -->
    <div id="action-popover"
        class="hidden fixed p-4 w-[180px] bg-white shadow-2xl rounded-2xl border border-gray-100 z-[9999]">
        <div class="flex flex-col items-center space-y-2">
            <a id="popover-visit-link" href="#" target="_blank"
                class="w-full bg-primary text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium hover:bg-[#0a3f45] transition-colors">
                <i class="ti tabler-external-link text-xs"></i>
                {{__('Visit Website')}}
            </a>
            <a id="popover-admin-link" href="#" target="_blank"
                class="w-full bg-teal-700 hover:bg-teal-800 text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium transition-colors">
                <i class="ti tabler-login text-xs"></i>
                {{__('Admin Login')}}
            </a>
            <button id="popover-delete-btn" type="button"
                class="w-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center gap-2 text-xs px-3 py-2 rounded-lg font-medium transition-colors">
                <i class="ti tabler-trash text-xs"></i>
                {{__('Delete Shop')}}
            </button>
        </div>
    </div>

    <!-- Delete Shop Confirmation Modal -->
    <div id="delete-shop-modal" class="hidden fixed inset-0 z-[99999] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" id="delete-modal-backdrop"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="ti tabler-alert-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{__('Delete Shop?')}}</h3>
                <p class="text-sm text-gray-500">
                    {{__('This action is permanent and cannot be undone. All data associated with this shop — including payment logs, media, and domain settings — will be permanently removed.')}}
                </p>
                <p class="text-sm font-semibold text-gray-700">
                    {{__('Type the shop name to confirm:')}} <span class="text-red-500 font-mono" id="delete-shop-name-hint"></span>
                </p>
                <input type="text" id="delete-confirm-input"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition"
                    placeholder="{{__('Type shop name here')}}">
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="button" id="delete-modal-cancel"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    {{__('Cancel')}}
                </button>
                <form id="delete-shop-form" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="delete-modal-confirm"
                        class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        {{__('Delete Permanently')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        document.querySelectorAll('.action-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var popover = document.getElementById('action-popover');
                var visitLink = document.getElementById('popover-visit-link');
                var adminLink = document.getElementById('popover-admin-link');
                var deleteBtn = document.getElementById('popover-delete-btn');

                visitLink.href = btn.dataset.visitUrl;
                adminLink.href = btn.dataset.adminUrl;
                deleteBtn.dataset.deleteUrl  = btn.dataset.deleteUrl;
                deleteBtn.dataset.tenantId   = btn.dataset.tenantId;

                var rect = btn.getBoundingClientRect();
                popover.style.top  = (window.scrollY + rect.bottom + 8) + 'px';
                popover.style.left = (window.scrollX + rect.right - 180) + 'px';
                popover.classList.toggle('hidden');
            });
        });

        document.addEventListener('click', function () {
            document.getElementById('action-popover').classList.add('hidden');
        });

        document.getElementById('popover-delete-btn').addEventListener('click', function () {
            document.getElementById('action-popover').classList.add('hidden');

            var tenantId  = this.dataset.tenantId;
            var deleteUrl = this.dataset.deleteUrl;

            document.getElementById('delete-shop-name-hint').textContent = tenantId;
            document.getElementById('delete-confirm-input').value = '';
            document.getElementById('delete-modal-confirm').disabled = true;
            document.getElementById('delete-shop-form').action = deleteUrl;

            document.getElementById('delete-shop-modal').classList.remove('hidden');
        });

        document.getElementById('delete-confirm-input').addEventListener('input', function () {
            var expected = document.getElementById('delete-shop-name-hint').textContent.trim();
            document.getElementById('delete-modal-confirm').disabled = this.value.trim() !== expected;
        });

        function closeDeleteModal() {
            document.getElementById('delete-shop-modal').classList.add('hidden');
        }

        document.getElementById('delete-modal-cancel').addEventListener('click', closeDeleteModal);
        document.getElementById('delete-modal-backdrop').addEventListener('click', closeDeleteModal);
    </script>
    <script>
        (function () {
            var searchInput  = document.getElementById('shop-search');
            var clearBtn     = document.getElementById('shop-search-clear');
            var noResults    = document.getElementById('no-search-results');
            var countEl      = document.getElementById('shop-count');
            var totalCount   = parseInt(countEl.textContent.trim(), 10);

            function filterTable(term) {
                var rows    = document.querySelectorAll('#shop-table-body .shop-row');
                var visible = 0;
                term = term.toLowerCase().trim();

                rows.forEach(function (row) {
                    var haystack = row.dataset.search || '';
                    var match = !term || haystack.includes(term);
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                noResults.classList.toggle('hidden', visible > 0 || rows.length === 0);
                countEl.textContent = term ? visible : totalCount;
                clearBtn.classList.toggle('hidden', !term);
            }

            searchInput.addEventListener('input', function () {
                filterTable(this.value);
            });

            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterTable('');
                searchInput.focus();
            });
        })();
    </script>
@endsection
