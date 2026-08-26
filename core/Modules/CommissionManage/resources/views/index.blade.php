@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Commission Settings')}}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-cog text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Commission Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure your commission type and payment gateway preferences')}}</p>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="px-4 sm:px-6 py-5">
        <form id="commissionSettingsForm" onsubmit="event.preventDefault(); submitCommissionForm();">
            @csrf

            {{-- Commission Type Selection --}}
            <div class="mb-5">
                <label for="commission_type" class="lnd-label flex items-center gap-1.5">
                    <i class="las la-list-ul text-primary"></i>
                    {{__('Commission Type')}} <span class="text-danger">*</span>
                </label>
                <select name="commission_type" id="commission_type" class="lnd-input">
                    <option value="">-- {{__('Select Commission Type')}} --</option>
                    @foreach($commissionTypes as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('commission_type', $setting?->commission_type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                {{-- Dynamic Help Alert --}}
                <div id="commissionTypeHelp" class="mt-3 px-4 py-3 rounded-xl bg-info-soft border border-main text-sm text-dark" style="display:none;">
                    <i class="las la-info-circle text-info mr-1"></i>
                    <small id="commissionTypeDescription"></small>
                </div>
            </div>

            {{-- Information Cards --}}
            <div class="mb-5">
                <div class="bg-secondary rounded-xl border border-main overflow-hidden">
                    <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                        <i class="las la-lightbulb text-primary"></i>
                        <h4 class="text-sm font-bold text-dark">{{__('Understanding Commission Types')}}</h4>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Type 1: Subscription Only --}}
                            <div class="cm-info-card type-purple">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-7 h-7 rounded-full bg-[#6c5ce7] text-white text-xs font-bold inline-flex items-center justify-center flex-shrink-0">1</span>
                                    <h5 class="text-sm font-bold text-[#6c5ce7]">{{__('Subscription Only')}}</h5>
                                </div>
                                <p class="text-xs text-muted mb-3">
                                    {{__('Standard subscription model: Tenants pay their subscription fees normally. No commission tracking on orders.')}}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    <span class="tw-pill tw-pill-success text-[10px]">
                                        <i class="las la-check-circle mr-0.5"></i>{{__('Subscription Payment')}}
                                    </span>
                                    <span class="tw-pill tw-pill-gray text-[10px]">
                                        <i class="las la-times-circle mr-0.5"></i>{{__('No Commission')}}
                                    </span>
                                </div>
                            </div>

                            {{-- Type 2: Subscription + Commission --}}
                            <div class="cm-info-card type-teal">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-7 h-7 rounded-full bg-info text-white text-xs font-bold inline-flex items-center justify-center flex-shrink-0">2</span>
                                    <h5 class="text-sm font-bold text-info">{{__('Subscription + Commission')}}</h5>
                                </div>
                                <p class="text-xs text-muted mb-3">
                                    {{__('Tenants pay subscription fees plus you earn commission on each order. Commissions are tracked and invoiced separately.')}}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    <span class="tw-pill tw-pill-success text-[10px]">
                                        <i class="las la-check-circle mr-0.5"></i>{{__('Subscription Payment')}}
                                    </span>
                                    <span class="tw-pill tw-pill-info text-[10px]">
                                        <i class="las la-percentage mr-0.5"></i>{{__('Order Commission')}}
                                    </span>
                                </div>
                            </div>

                            {{-- Type 3: Commission Only --}}
                            <div class="cm-info-card type-amber">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-7 h-7 rounded-full bg-warning text-white text-xs font-bold inline-flex items-center justify-center flex-shrink-0">3</span>
                                    <h5 class="text-sm font-bold text-warning">{{__('Commission Only')}}</h5>
                                </div>
                                <p class="text-xs text-muted mb-3">
                                    <strong>{{__('Free subscriptions!')}}</strong> {{__('All price plans are $0. Tenants subscribe/renew automatically. You earn only through order commissions.')}}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    <span class="tw-pill tw-pill-success text-[10px]">
                                        <i class="las la-gift mr-0.5"></i>{{__('Free Subscription')}}
                                    </span>
                                    <span class="tw-pill tw-pill-info text-[10px]">
                                        <i class="las la-sync mr-0.5"></i>{{__('Auto Renewal')}}
                                    </span>
                                    <span class="tw-pill tw-pill-warning text-[10px]">
                                        <i class="las la-percentage mr-0.5"></i>{{__('Commission Only')}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Commission Rate Field --}}
            <div class="mb-5" id="commissionRateWrapper" style="display:none;">
                <label for="commission_rate" class="lnd-label flex items-center gap-1.5">
                    <i class="las la-percentage text-primary"></i>
                    {{__('Commission Rate (%)')}} <span class="text-danger" id="commissionRateRequired">*</span>
                </label>
                <div class="flex">
                    <input type="number" name="commission_rate" id="commission_rate"
                           class="lnd-input"
                           style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
                           value="{{ old('commission_rate', $setting?->commission_rate ?? 0) }}"
                           step="1" min="1" max="100" disabled
                           placeholder="{{__('Enter commission rate')}}">
                    <span class="inline-flex items-center px-3 text-sm font-semibold text-muted bg-secondary border border-l-0 border-main rounded-r-lg">%</span>
                </div>
                <p class="mt-1.5 text-xs text-muted">
                    <i class="las la-info-circle mr-0.5"></i>
                    {{__('Commission percentage will be calculated from each tenant\'s order total amount.')}}
                </p>
            </div>

            {{-- Payment Gateway Source --}}
            <div class="mb-5" id="gatewaySourceWrapper" style="display:none;">
                <label for="payment_gateway_source" class="lnd-label flex items-center gap-1.5">
                    <i class="las la-credit-card text-primary"></i>
                    {{__('Payment Gateway Source')}} <span class="text-danger">*</span>
                </label>
                <select name="payment_gateway_source" id="payment_gateway_source" class="lnd-input">
                    <option value="">-- {{__('Select Gateway Source')}} --</option>
                    @foreach($gatewaySources as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('payment_gateway_source', $setting?->payment_gateway_source) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-muted">
                    <i class="las la-info-circle mr-0.5"></i>
                    {{__('Choose whether to use landlord\'s payment gateways or allow tenants to use their own.')}}
                </p>
            </div>

            {{-- Landlord Gateway Selection --}}
            <div class="mb-5" id="gatewaySelectionWrapper" style="display:none;">
                <div class="bg-secondary rounded-xl border border-main overflow-hidden">
                    <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                        <i class="las la-money-check-alt text-primary"></i>
                        <h4 class="text-sm font-bold text-dark">{{__('Select Payment Gateways')}}</h4>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <x-fields.switcher class="select-all-theme" name="" label="" value=""/>
                        </div>

                        <div class="feature-section">
                            @php
                                $gateway_markup = render_payment_gateway_for_price_plan(is_price_plan: true);
                                $gateway_markup = preg_replace('/<input[^>]*name="selected_payment_gateway"[^>]*>/i', '', $gateway_markup);
                            @endphp

                            {!! $gateway_markup !!}

                            <input type="hidden" name="payment_gateways" id="selected_gateways_input"
                                   value="{{ implode(',', $landlord_payment_gateways ?? []) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4 border-t border-main">
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition" id="submitBtn">
                    <span class="cm-spinner hidden" id="submitSpinner" style="width:1rem;height:1rem;border-width:2px;"></span>
                    <i class="las la-save" id="submitIcon"></i>
                    <span id="btnText">{{__('Save Settings')}}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        /* ==== SESSION MESSAGES ==== */
        @if(session('success')) toastr.success("{{ session('success') }}");
        @endif
        @if(session('error')) toastr.error("{{ session('error') }}"); @endif

        /* ==== ERROR HELPERS ==== */
        function clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }

        function showError(field, msg) {
            const el = document.querySelector(`[name="${field}"]`);
            if (!el) return;
            el.classList.add('is-invalid');
            el.style.borderColor = 'var(--color-danger)';
            if (!el.parentNode.querySelector('.invalid-feedback')) {
                el.insertAdjacentHTML('afterend', `<div class="invalid-feedback" style="display:block;color:var(--color-danger);font-size:0.75rem;margin-top:0.25rem;">${msg}</div>`);
            }
        }

        /* ==== AJAX SUBMIT ==== */
        window.submitCommissionForm = function () {
            clearErrors();

            const form = document.getElementById('commissionSettingsForm');
            const formData = new FormData(form);
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            const icon = document.getElementById('submitIcon');
            const btnText = document.getElementById('btnText');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            icon.classList.add('hidden');
            btnText.textContent = 'Saving...';

            const hasId = '{{ $setting?->id ?? null }}';
            const url = hasId
                ? '{{ route("landlord.admin.commission.settings.store", ":id") }}'.replace(':id', hasId)
                : '{{ route("landlord.admin.commission.settings.store") }}';

            axios.post(url, formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => {
                    toastr.success(response.data.message || 'Settings saved successfully!');
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(error => {
                    if (error.response?.status === 422) {
                        const errors = error.response.data.errors;
                        Object.keys(errors).forEach(field => showError(field, errors[field][0]));
                    } else {
                        toastr.error(error.response?.data?.message || 'Something went wrong');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    icon.classList.remove('hidden');
                    btnText.textContent = 'Save Settings';
                });
        };

        /* ==== DYNAMIC HELP TEXT DESCRIPTIONS ==== */
        const commissionDescriptions = {
            'subscription_only': '<strong>Standard subscription model:</strong> Tenants pay for their plans. No commission tracking on orders.',
            'subscription_and_commission': '<strong>Dual revenue model:</strong> Tenants pay subscription + you earn commission on every order they receive.',
            'commission_only': '<strong>Free subscription model:</strong> No subscription fees! All plans are free. You earn only from order commissions.'
        };

        /* ==== DYNAMIC FIELD VISIBILITY ==== */
        const typeEl = document.getElementById('commission_type');
        const rateWrapper = document.getElementById('commissionRateWrapper');
        const gatewaySourceWrapper = document.getElementById('gatewaySourceWrapper');
        const gatewaySelectionWrapper = document.getElementById('gatewaySelectionWrapper');
        const gatewaySourceEl = document.getElementById('payment_gateway_source');
        const commissionTypeHelp = document.getElementById('commissionTypeHelp');
        const commissionTypeDescription = document.getElementById('commissionTypeDescription');

        function updateFieldVisibility() {
            const type = typeEl.value;
            const rateInput = document.getElementById('commission_rate');

            if (type && commissionDescriptions[type]) {
                commissionTypeDescription.innerHTML = commissionDescriptions[type];
                commissionTypeHelp.style.display = 'block';
            } else {
                commissionTypeHelp.style.display = 'none';
            }

            if (type === 'subscription_and_commission' || type === 'commission_only') {
                rateWrapper.style.display = 'block';
                rateInput.disabled = false;
                gatewaySourceWrapper.style.display = 'block';
                toggleGatewaySelection();
            } else {
                rateWrapper.style.display = 'none';
                rateInput.disabled = true;
                gatewaySourceWrapper.style.display = 'none';
                gatewaySelectionWrapper.style.display = 'none';
            }
        }

        function toggleGatewaySelection() {
            gatewaySelectionWrapper.style.display =
                (gatewaySourceEl.value === 'landlord_gateway') ? 'block' : 'none';
        }

        typeEl?.addEventListener('change', updateFieldVisibility);
        gatewaySourceEl?.addEventListener('change', toggleGatewaySelection);

        document.addEventListener('DOMContentLoaded', () => {
            updateFieldVisibility();
        });

        /* ==== REAL-TIME COMMISSION RATE VALIDATION ==== */
        function validateRate() {
            const type = typeEl.value;
            const rate = parseFloat(document.getElementById('commission_rate')?.value) || 0;
            const required = ['commission_only', 'subscription_and_commission'].includes(type);
            const input = document.getElementById('commission_rate');
            const fb = input?.parentNode.querySelector('.invalid-feedback');

            if (required && rate <= 0) {
                input.style.borderColor = 'var(--color-danger)';
                if (!fb) input.insertAdjacentHTML('afterend', '<div class="invalid-feedback" style="display:block;color:var(--color-danger);font-size:0.75rem;margin-top:0.25rem;">Rate must be greater than 0%</div>');
            } else {
                input.style.borderColor = '';
                fb?.remove();
            }
        }

        typeEl?.addEventListener('change', validateRate);
        document.getElementById('commission_rate')?.addEventListener('input', validateRate);

        /* ==== GATEWAY SELECTION (preselect + click + select all) ==== */
        window.preselectedGateways = @json($landlord_payment_gateways ?? []);

        $(function () {
            const $items = $('.payment-gateway-wrapper ul li');
            const $hidden = $('input[name="payment_gateways"]');

            $items.removeClass('selected');
            window.preselectedGateways.forEach(gw => {
                $items.filter(`[data-gateway="${gw}"]`).addClass('selected');
            });
            $hidden.val(window.preselectedGateways.join(','));

            $items.on('click', function () {
                $(this).toggleClass('selected');
                const selected = $items.filter('.selected').map((_, el) => $(el).data('gateway')).get();
                $hidden.val(selected.join(','));
            });

            $('.select-all-theme input[type="checkbox"]').on('change', function () {
                const checked = this.checked;
                $items.toggleClass('selected', checked);
                const selected = checked ? $items.map((_, el) => $(el).data('gateway')).get() : [];
                $hidden.val(selected.join(','));
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.payment-gateway-wrapper input[type="checkbox"]');
            const hiddenInput = document.getElementById('selected_gateways_input');

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const selected = Array.from(checkboxes)
                        .filter(c => c.checked)
                        .map(c => c.value);
                    hiddenInput.value = selected.join(',');
                });
            });
        });
    </script>

    <script>
        function updateWithdrawGatewayVisibility() {
            const type = document.getElementById('commission_type')?.value;
            const gatewaySource = document.getElementById('payment_gateway_source')?.value;
            const withdrawWrapper = document.getElementById('withdrawGatewayWrapper');

            if (withdrawWrapper) {
                if ((type === 'subscription_and_commission' || type === 'commission_only') &&
                    gatewaySource === 'landlord_gateway') {
                    withdrawWrapper.style.display = 'block';
                } else {
                    withdrawWrapper.style.display = 'none';
                }
            }
        }

        const originalUpdateFieldVisibility = typeof updateFieldVisibility !== 'undefined' ? updateFieldVisibility : function(){};
        updateFieldVisibility = function() {
            originalUpdateFieldVisibility();
            updateWithdrawGatewayVisibility();
        };

        document.addEventListener('DOMContentLoaded', function() {
            const typeEl = document.getElementById('commission_type');
            const gatewaySourceEl = document.getElementById('payment_gateway_source');

            if (typeEl) {
                typeEl.addEventListener('change', updateWithdrawGatewayVisibility);
            }

            if (gatewaySourceEl) {
                gatewaySourceEl.addEventListener('change', updateWithdrawGatewayVisibility);
            }

            updateWithdrawGatewayVisibility();
        });
    </script>
@endsection
