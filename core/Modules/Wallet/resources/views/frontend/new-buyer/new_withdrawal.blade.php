@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{ __('New Withdrawal') }}
@endsection

@section('title')
    {{ __('New Withdrawal') }}
@endsection

@section('section')
    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9 px-4 lg:px-0">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-y rounded-tr-3xl">
            <div class="flex items-center justify-between pr-8 pt-3 pb-4">
                <div class="flex items-center">
                    <button id="menuBtn"
                            class="block ml-5 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                        <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                    </button>
                    <div class="ml-4 lg:ml-16">
                        <h1 class="text-lg font-medium text-secondary">{{ __('New Withdrawal') }}</h1>
                        <p class="text-xs lg:text-sm text-sub2Title mt-1">{{ __('Create a new withdrawal request') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="mt-4 lg:mt-8 lg:pl-16 lg:pr-9">
            <x-error-msg-tw/>
            <x-flash-msg-tw/>
            <form action="{{ route('tenant.withdraw.submit') }}" method="POST" id="withdrawForm">
                @csrf
                <div class="flex flex-col gap-4">

                    <!-- Wallet Balance -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background-color:#e8f0f5;">
                                <i class="ti tabler-wallet text-xl" style="color:#1b3f50;"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">{{ __('Wallet Balance') }}</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Withdraw Amount -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5">
                        <p class="text-sm font-semibold text-gray-800 mb-3">{{ __('Withdraw Amount') }}</p>
                        <div class="flex items-center border border-borderCS rounded-xl overflow-hidden bg-white focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                            <span class="px-4 text-gray-500 text-sm border-r border-borderCS py-3">{{ site_currency_symbol() }}</span>
                            <input id="withdrawAmount" type="number" name="amount" step="1" min="10"
                                   max="{{ $balance->balance ?? 0 }}"
                                   value="{{ old('amount') }}"
                                   class="flex-1 px-4 py-3 text-sm text-gray-800 outline-none bg-white"
                                   placeholder="{{ __('Enter amount') }}" required>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            {{ __('Minimum amount:') }} {{ float_amount_with_currency_symbol(10) }} &nbsp;|&nbsp;
                            {{ __('Maximum amount:') }} {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                        </p>
                        @error('amount')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Select Withdrawal Method -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <p class="text-sm font-semibold text-gray-800 mb-5">{{ __('Select Withdrawal Method') }}</p>

                        <input type="hidden" name="selected_payment_gateway" id="selected_payment_gateway"
                               value="{{ old('selected_payment_gateway') }}">

                        @if($withdraw_gateways && $withdraw_gateways->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($withdraw_gateways as $gateway)
                                    <div class="method-card flex flex-col items-center justify-center relative rounded-xl border-2 border-gray-200 h-[72px] px-3 gap-1 cursor-pointer transition-all duration-150 {{ old('selected_payment_gateway') == $gateway->gateway_id ? 'border-[#0C4D54] bg-[#f0f7f8]' : '' }}"
                                         data-gateway-id="{{ $gateway->gateway_id }}"
                                         onclick="selectGateway('{{ $gateway->gateway_id }}')">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-[#e8f0f5]">
                                            <i class="ti tabler-wallet text-[#1b3f50]"></i>
                                        </div>
                                        <span class="text-[10px] text-gray-600 truncate w-full text-center leading-tight font-medium">
                                            {{ $gateway->name }}
                                        </span>
                                        <span class="check-badge hidden absolute right-1.5 top-1.5 w-5 h-5 rounded-full bg-[#0C4D54] text-white items-center justify-center">
                                            <i class="ti tabler-check" style="font-size:11px;"></i>
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @error('selected_payment_gateway')
                                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                            @enderror

                            <!-- Dynamic Gateway Fields -->
                            <div id="gatewayFieldsContainer" class="mt-5">
                                @foreach($withdraw_gateways as $gateway)
                                    @if($gateway->fields && count($gateway->fields) > 0)
                                        <div class="gateway-specific-fields {{ old('selected_payment_gateway') == $gateway->gateway_id ? '' : 'hidden' }}"
                                             data-gateway="{{ $gateway->gateway_id }}">
                                            <div class="bg-gray-50 rounded-xl border border-gray-100 p-5">
                                                <p class="text-sm font-semibold text-gray-800 mb-4">
                                                    <i class="ti tabler-edit mr-1"></i>
                                                    {{ $gateway->name }} - {{ __('Account Details') }}
                                                </p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @foreach($gateway->fields as $field)
                                                        @php
                                                            $fieldName = 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($field['label']));
                                                            $isRequired = isset($field['required']) && $field['required'];
                                                        @endphp

                                                        <div class="{{ in_array($field['type'] ?? 'text', ['textarea']) ? 'md:col-span-2' : '' }}">
                                                            <label class="text-sm font-medium text-gray-700 mb-1 block">
                                                                {{ $field['label'] }}
                                                                @if($isRequired)
                                                                    <span class="text-red-500">*</span>
                                                                @endif
                                                            </label>

                                                            @if(($field['type'] ?? 'text') == 'textarea')
                                                                <textarea name="{{ $fieldName }}" id="{{ $fieldName }}"
                                                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 gateway-field"
                                                                    placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                                    rows="3"
                                                                    data-label="{{ $field['label'] }}"
                                                                    data-required="{{ $isRequired ? '1' : '0' }}">{{ old($fieldName) }}</textarea>
                                                            @else
                                                                <input type="{{ $field['type'] ?? 'text' }}"
                                                                    name="{{ $fieldName }}" id="{{ $fieldName }}"
                                                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 gateway-field"
                                                                    placeholder="{{ $field['placeholder'] ?? $field['label'] }}"
                                                                    value="{{ old($fieldName) }}"
                                                                    data-label="{{ $field['label'] }}"
                                                                    data-required="{{ $isRequired ? '1' : '0' }}">
                                                            @endif

                                                            @error($fieldName)
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm rounded-xl px-4 py-3">
                                <i class="ti tabler-alert-triangle mr-1"></i>
                                {{ __('No payment gateways available. Please contact administrator.') }}
                            </div>
                        @endif
                    </div>

                    <!-- Note (Optional) -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5">
                        <p class="text-sm font-semibold text-gray-800 mb-3">{{ __('Note (Optional)') }}</p>
                        <textarea id="noteArea" name="note" maxlength="1000" rows="5"
                                  class="w-full border border-borderCS rounded-xl px-4 py-3 text-sm text-gray-600 placeholder-gray-400 outline-none resize-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition"
                                  placeholder="{{ __('Add any additional information...') }}"
                                  oninput="document.getElementById('charCount').textContent = this.value.length + '/1000 characters'">{{ old('note') }}</textarea>
                        <p class="text-xs text-gray-400 text-right mt-1" id="charCount">0/1000 characters</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-3 pb-4">
                        <a href="{{ route('landlord.user.wallet.withdraw') }}"
                           class="flex-1 py-3 rounded-xl border border-gray-200 text-base font-medium text-secondary bg-white hover:bg-gray-50 transition-colors text-center">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" id="submitWithdrawBtn"
                                class="flex-1 py-3 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90 bg-primary">
                            <i class="ti tabler-send mr-1"></i>
                            {{ __('Submit Request') }}
                        </button>
                    </div>

                </div>
            </form>
        </main>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>
    <script>
        function selectGateway(gatewayId) {
            // Update hidden input
            document.getElementById('selected_payment_gateway').value = gatewayId;

            // Update card styles
            document.querySelectorAll('.method-card').forEach(card => {
                card.classList.remove('border-[#0C4D54]', 'bg-[#f0f7f8]');
                card.classList.add('border-gray-200');
                const badge = card.querySelector('.check-badge');
                if (badge) {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            });

            const selectedCard = document.querySelector(`.method-card[data-gateway-id="${gatewayId}"]`);
            if (selectedCard) {
                selectedCard.classList.remove('border-gray-200');
                selectedCard.classList.add('border-[#0C4D54]', 'bg-[#f0f7f8]');
                const badge = selectedCard.querySelector('.check-badge');
                if (badge) {
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                }
            }

            // Show/hide gateway-specific fields
            document.querySelectorAll('.gateway-specific-fields').forEach(el => {
                el.classList.add('hidden');
            });
            const gatewayFields = document.querySelector(`.gateway-specific-fields[data-gateway="${gatewayId}"]`);
            if (gatewayFields) {
                gatewayFields.classList.remove('hidden');
            }
        }

        // Initialize on page load if old value exists
        document.addEventListener('DOMContentLoaded', function() {
            const existingGateway = document.getElementById('selected_payment_gateway').value;
            if (existingGateway) {
                selectGateway(existingGateway);
            }
        });
    </script>
@endsection
