@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{ __('Wallet Settings') }}
@endsection

@section('title')
    {{ __('Wallet Settings') }}
@endsection

@section('section')
    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl px-6 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button id="menuBtn"
                            class="block mr-3 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                        <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                    </button>
                    <div class="">
                        <h1 class="text-lg font-medium text-secondary">{{ __('Wallet Settings') }}</h1>
                        <p class="text-xs lg:text-sm text-gray-500 mt-1">{{ __('Manage your wallet preferences and notifications') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <form action="{{ route('landlord.user.wallet.settings') }}" method="POST">
                @csrf
                <div class="flex flex-col gap-4">

                    <!-- Wallet Balance Card -->
                    <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-[#e8f0f5]">
                                <i class="ti tabler-wallet text-xl text-[#1b3f50] icon-28px"></i>
                            </div>
                            <div>
                                <p class="text-sm text-sub2Title font-medium">{{ __('Wallet Balance') }}</p>
                                <p class="text-2xl font-medium text-secondary">
                                    {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Package Renewal Card -->
                    <div class="bg-white rounded-2xl border border-borderCS overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 py-4 cursor-pointer select-none"
                             onclick="toggleRenewalSection()">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary">
                                    <i class="ti tabler-refresh text-white text-xl icon-22px"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-secondary">{{ __('Package Renewal') }}</p>
                                    <p class="text-base text-sub2Title">{{ __('Enable automatic renewal for your packages') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer" onclick="event.stopPropagation()">
                                    <input type="checkbox" name="renewal_using_wallet" class="sr-only peer"
                                           id="renewal_toggle"
                                           {{ !empty($settings) && $settings->renew_package ? 'checked' : '' }}
                                           onchange="toggleTenantList()">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-green-500 transition-colors duration-300 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                                <i id="renewal-chevron" class="ti tabler-chevron-down text-sub2Title text-xl transition-transform duration-300"></i>
                            </div>
                        </div>

                        <!-- Body: Tenant List -->
                        <div id="renewal-body" class="{{ !empty($settings) && $settings->renew_package ? '' : 'hidden' }}">
                            <hr class="border-borderCS"/>
                            <div class="p-4 flex flex-col gap-3">
                                @php
                                    $user = Auth::guard('web')->user();
                                    $user_tenants = [];
                                    if (!empty($user->wallet_tenant_list)) {
                                        $user_tenants = $user->wallet_tenant_list->pluck('tenant_id')->toArray();
                                    }
                                    $tenant_list = $user?->tenant_details->where('expire_date', '!=', null);
                                @endphp

                                @forelse($tenant_list as $tenant)
                                    <div class="bg-gray-50 rounded-xl border border-gray-100 px-5 py-4 flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-medium text-secondary">{{ $tenant->id }}</p>
                                            <p class="text-sm text-sub2Title mt-0.5" id="label-tenant-{{ $loop->index }}">
                                                {{ in_array($tenant->id, $user_tenants) ? __('Auto-renewal enabled') : __('Auto-renewal disabled') }}
                                            </p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="tenants_list[]" value="{{ $tenant->id }}"
                                                   class="sr-only peer tenant-checkbox"
                                                   data-index="{{ $loop->index }}"
                                                   {{ in_array($tenant->id, $user_tenants) ? 'checked' : '' }}
                                                   onchange="updateTenantLabel({{ $loop->index }}, this.checked)">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-green-500 transition-colors duration-300 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No packages found') }}</p>
                                @endforelse

                                <div class="rounded-xl border px-5 py-3.5" style="background-color:#f0faf4; border-color:#bbf7d0;">
                                    <p class="text-sm" style="color:#15803d;">
                                        {{ __('Note: Enabled packages will automatically renew before expiration using your wallet balance.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Alert Card -->
                    <div class="bg-white rounded-2xl border border-borderCS overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-primary">
                                    <i class="ti tabler-bell text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-secondary">{{ __('Balance Alert') }}</p>
                                    <p class="text-base text-sub2Title">{{ __('Get notified when balance is low') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="wallet_balance_alert" class="sr-only peer"
                                       id="alert_toggle"
                                       {{ !empty($settings) && $settings->wallet_alert ? 'checked' : '' }}
                                       onchange="toggleAlertAmount()">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-green-500 transition-colors duration-300 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>

                        <!-- Body -->
                        <div id="alert-body" class="{{ !empty($settings) && $settings->wallet_alert ? '' : 'hidden' }}">
                            <div class="px-5 py-5 flex flex-col gap-3">
                                <label class="text-sm font-semibold text-gray-700">
                                    {{ __('Minimum Alert Amount') }} ({{ site_currency_symbol() }})
                                </label>
                                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                                    <span class="px-4 text-gray-500 text-sm border-r border-gray-200 py-3">{{ site_currency_symbol() }}</span>
                                    <input type="number" name="minimum_alert_amount" min="0"
                                           value="{{ !empty($settings) ? $settings->minimum_amount : '' }}"
                                           class="flex-1 px-4 py-3 text-sm text-gray-800 outline-none bg-white"
                                           placeholder="{{ __('Enter amount') }}">
                                </div>
                                <p class="text-xs text-gray-400">{{ __("You'll receive an alert when your wallet balance drops below this amount") }}</p>

                                <div class="rounded-xl border px-5 py-3.5" style="background-color:#f0faf4; border-color:#bbf7d0;">
                                    <p class="text-sm" style="color:#15803d;">
                                        {{ __('Note: Alerts will be sent to your registered email and displayed in your dashboard') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-start pb-2 ">
                        <button type="submit"
                                class="px-8 py-3 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90 bg-primary w-full lg:w-auto">
                            {{ __('Save Changes') }}
                        </button>
                    </div>

                </div>
            </form>
        </main>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function toggleRenewalSection() {
            const body = document.getElementById('renewal-body');
            const chevron = document.getElementById('renewal-chevron');
            body.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        function toggleTenantList() {
            const toggle = document.getElementById('renewal_toggle');
            const body = document.getElementById('renewal-body');
            if (toggle.checked) {
                body.classList.remove('hidden');
            } else {
                body.classList.add('hidden');
                document.querySelectorAll('.tenant-checkbox').forEach(cb => {
                    cb.checked = false;
                    const idx = cb.dataset.index;
                    updateTenantLabel(idx, false);
                });
            }
        }

        function updateTenantLabel(index, isChecked) {
            const label = document.getElementById('label-tenant-' + index);
            if (label) {
                label.textContent = isChecked ? '{{ __("Auto-renewal enabled") }}' : '{{ __("Auto-renewal disabled") }}';
            }
        }

        function toggleAlertAmount() {
            const toggle = document.getElementById('alert_toggle');
            const body = document.getElementById('alert-body');
            if (toggle.checked) {
                body.classList.remove('hidden');
            } else {
                body.classList.add('hidden');
            }
        }
    </script>
@endsection
