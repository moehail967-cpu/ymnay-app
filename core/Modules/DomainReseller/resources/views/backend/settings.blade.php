@extends('landlord.admin.admin-master')

@section('title')
    {{ __('Domain Reseller Settings') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-cogs text-info text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Domain Reseller Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Manage domain service providers — activate, deactivate and configure')}}</p>
            </div>
        </div>
        <button type="button" onclick="openModal('additional_settings_modal')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
            <i class="las la-sliders-h"></i>
            {{__('Additional Settings')}}
        </button>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="dr-provider-grid">
            @foreach($providers ?? [] as $key => $provider)
                @php
                    $status = get_static_option_central('active_domain_provider');
                    $credentials = [
                        $provider['slug']."_api_key" => get_static_option_central($provider['slug']."_api_key"),
                        $provider['slug']."_api_secret" => get_static_option_central($provider['slug']."_api_secret"),
                        $provider['slug']."_api_app_name" => get_static_option_central($provider['slug']."_api_app_name"),
                        $provider['slug']."_api_environment" => get_static_option_central($provider['slug']."_api_environment"),
                    ];
                    $credentials = json_encode($credentials);
                    $isActive = $status == $provider['slug'];
                @endphp

                <div class="dr-provider-card">
                    <div class="dr-provider-thumb" style="background: url('{{route('landlord.admin.domain-reseller.logo', $provider['logo'])}}') center/cover">
                        <span class="dr-provider-name">{{$provider['name']}}</span>
                    </div>
                    <div class="dr-provider-body">
                        <p class="dr-provider-meta">
                            {{__('Learn more about this provider')}} —
                            <a href="{{$provider['reference']}}" target="_blank">{{__('Documentation')}}</a>
                        </p>
                        <div class="dr-provider-actions">
                            <a href="#"
                               data-option="{{$provider['slug']}}"
                               class="pl_active_deactive inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white transition {{$isActive ? 'bg-success' : 'bg-danger'}}">
                                {{$isActive ? __('Activated') : __('Deactivated')}}
                                <span class="cm-spinner hidden"></span>
                            </a>

                            <a href="#"
                               data-option="{{$provider['slug']}}"
                               data-credentials="{{$credentials}}"
                               data-modal="{{$provider['slug']}}_modal"
                               class="pl_settings inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-sidebar-hover transition">
                                <i class="las la-key"></i> {{__('Settings')}}
                            </a>

                            @if($provider['configuration'] ?? false)
                                <a href="#"
                                   data-modal="{{$provider['slug']}}_configuration_modal"
                                   class="pl_config inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-sidebar-hover transition">
                                    <i class="las la-cog"></i> {{__('Config')}}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- GoDaddy Settings Modal --}}
<div class="cm-modal-backdrop" id="godaddy_modal_backdrop"></div>
<div class="cm-modal" id="godaddy_modal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h4 class="cm-modal-title"><i class="las la-key text-primary"></i> {{__('GoDaddy Credentials')}}</h4>
            <button type="button" class="cm-modal-close" onclick="closeModal('godaddy_modal')"><i class="las la-times"></i></button>
        </div>
        <form action="{{route('landlord.admin.domain-reseller.settings.update')}}" method="POST">
            @csrf
            <div class="cm-modal-body space-y-4">
                <div>
                    <label class="lnd-label">{{__('GoDaddy API Key')}} <span class="text-danger">*</span></label>
                    <input type="text" class="lnd-input" name="godaddy_api_key" placeholder="{{__('GoDaddy API Key')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('GoDaddy API Secret')}} <span class="text-danger">*</span></label>
                    <input type="text" class="lnd-input" name="godaddy_api_secret" placeholder="{{__('GoDaddy API Secret')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('GoDaddy API App Name')}} <span class="text-xs text-muted">({{__('Optional')}})</span></label>
                    <input type="text" class="lnd-input" name="godaddy_api_app_name" placeholder="{{__('GoDaddy API App Name')}}">
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('godaddy_modal')"
                        class="px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark transition">{{__('Cancel')}}</button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                    <span class="cm-spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- GoDaddy Config Modal --}}
<div class="cm-modal-backdrop" id="godaddy_configuration_modal_backdrop"></div>
<div class="cm-modal" id="godaddy_configuration_modal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h4 class="cm-modal-title"><i class="las la-cog text-primary"></i> {{__('GoDaddy Configuration')}}</h4>
            <button type="button" class="cm-modal-close" onclick="closeModal('godaddy_configuration_modal')"><i class="las la-times"></i></button>
        </div>
        <form action="{{route('landlord.admin.domain-reseller.configuration.update')}}" method="POST">
            @csrf
            <input type="hidden" name="shipping_gateway_name" value="goDaddy">
            <div class="cm-modal-body space-y-4">
                <div class="bg-secondary border border-main rounded-xl px-4 py-3 flex items-start gap-2">
                    <i class="las la-info-circle text-info text-lg mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-muted">{{__('Integrate first with the test environment to verify that you are calling the API properly before going live with calls to the Production environment.')}}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="lnd-label mb-0">{{__('Enable Live Mode')}}</label>
                    <label class="dr-toggle">
                        <input type="hidden" name="godaddy_environment" value="">
                        <input type="checkbox" name="godaddy_environment" value="on"
                            {{get_static_option_central('godaddy_environment') == 'on' ? 'checked' : ''}}>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('godaddy_configuration_modal')"
                        class="px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark transition">{{__('Cancel')}}</button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                    <span class="cm-spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Additional Settings Modal --}}
<div class="cm-modal-backdrop" id="additional_settings_modal_backdrop"></div>
<div class="cm-modal" id="additional_settings_modal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h4 class="cm-modal-title"><i class="las la-sliders-h text-primary"></i> {{__('Additional Settings')}}</h4>
            <button type="button" class="cm-modal-close" onclick="closeModal('additional_settings_modal')"><i class="las la-times"></i></button>
        </div>
        <form action="{{route('landlord.admin.domain-reseller.additional.settings.update')}}" method="POST">
            @csrf
            @php
                $fee_title = __(get_static_option_central('domain_reseller_additional_fee_title'));
                $fee_amount = get_static_option_central('domain_reseller_additional_charge');
            @endphp
            <div class="cm-modal-body space-y-4">
                <div>
                    <label class="lnd-label">{{__('Additional Fee Title')}}</label>
                    <input type="text" name="additional_fee_title" class="lnd-input" value="{{$fee_title ?? ''}}">
                    <p class="text-xs text-muted mt-1">{{__('Shown as the additional fee label in checkout')}}</p>
                </div>
                <div>
                    <label class="lnd-label">{{__('Domain Purchase Additional Fee (USD)')}}</label>
                    <input type="number" name="additional_charge" class="lnd-input" value="{{$fee_amount ?? 0}}" min="0" step="0.01">
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('additional_settings_modal')"
                        class="px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark transition">{{__('Cancel')}}</button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                    <span class="cm-spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.getElementById(id + '_backdrop').classList.add('active');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.getElementById(id + '_backdrop').classList.remove('active');
        }

        document.querySelectorAll('.cm-modal-backdrop').forEach(el => {
            el.addEventListener('click', () => {
                el.classList.remove('active');
                let modalId = el.id.replace('_backdrop', '');
                document.getElementById(modalId)?.classList.remove('active');
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.cm-modal.active').forEach(m => {
                    m.classList.remove('active');
                    document.getElementById(m.id + '_backdrop')?.classList.remove('active');
                });
            }
        });

        (function ($) {
            "use strict";

            $(document).on('click', '.pl_settings', function (e) {
                e.preventDefault();
                let el = $(this);
                let option = el.attr('data-option');
                let credentials = JSON.parse(el.attr('data-credentials'));
                let modalId = el.attr('data-modal');

                let modal = $(`#${modalId}`);
                for (let item in credentials) {
                    modal.find(`input[name=${item}][type=text]`).val(credentials[item]);
                }
                openModal(modalId);
            });

            $(document).on('click', '.pl_config', function (e) {
                e.preventDefault();
                openModal($(this).attr('data-modal'));
            });

            $(document).on('click', '.pl_active_deactive', function (e) {
                e.preventDefault();
                let el = $(this);
                let option = el.attr('data-option');
                el.find('.cm-spinner').removeClass('hidden');
                el.css('pointer-events', 'none');

                axios.get(`{{route('landlord.admin.domain-reseller.status.change')}}?option=${option.toLowerCase()}`)
                    .then((response) => {
                        if (response.data.type === 'success') {
                            location.reload();
                        }
                    });
            });

            $(document).on('click', '.cm-modal form button[type=submit]', function () {
                $(this).find('.cm-spinner').removeClass('hidden');
            });
        })(jQuery);
    </script>
@endsection
