@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Shipping Plugin Settings') }}
@endsection

@section('style')
<style>
.sp-gateway-card{transition:transform .22s,box-shadow .22s;}
.sp-gateway-card:hover{transform:translateY(-3px);}
.sp-btn-action{transition:opacity .18s,transform .18s;}
.sp-btn-action:hover{opacity:.82;transform:translateY(-1px);}
.sp-btn-action:disabled{opacity:.55;cursor:not-allowed;transform:none;}
</style>
@endsection

@section('content')
<div class="dashboard-recent-order">
    <div class="row">
        <x-flash-msg/>
        <x-error-msg/>
        <div class="col-md-12">

            {{-- Page Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-xl font-bold mb-1" style="color:var(--color-text-dark)">{{ __('Shipping Plugin Settings') }}</h4>
                    <p class="text-sm" style="color:var(--color-text-muted)">{{ __('Activate, deactivate and configure your shipping gateway integrations.') }}</p>
                </div>
            </div>

            {{-- Gateway Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

                @foreach($gateways ?? [] as $gateway)
                    @php
                        $status      = get_static_option('active_shipping_gateway');
                        $isActive    = $status === $gateway['slug'];
                        $gatewayData = null; // reset per-card error state

                        if ($gateway['slug'] === 'shiprocket') {
                            $credentials = [
                                $gateway['slug'].'_api_user_email'           => get_static_option($gateway['slug'].'_api_user_email'),
                                $gateway['slug'].'_api_user_password'        => get_static_option($gateway['slug'].'_api_user_password'),
                                $gateway['slug'].'_api_authorization_token'  => get_static_option($gateway['slug'].'_api_authorization_token'),
                                $gateway['slug'].'_auto_create_order_option' => get_static_option($gateway['slug'].'_auto_create_order_option'),
                                $gateway['slug'].'_order_tracking_option'    => get_static_option($gateway['slug'].'_order_tracking_option'),
                            ];
                            // Default: assume success with empty list
                            $data        = ['status' => true, 'list' => []];
                            $gatewayData = &$data;
                            try {
                                $list = (new \Modules\ShippingPlugin\Http\Services\Gateways\ShipRocket())->getPickupLocations() ?? [];
                                $data = ['status' => true, 'list' => $list];
                            } catch (Exception $exception) {
                                if ($exception->getCode() === 401) {
                                    $data = ['status' => false, 'message' => __('Token has expired'), 'list' => []];
                                }
                                // Any other exception: keep default (status true, empty list)
                            }
                        } else {
                            $credentials = [
                                $gateway['slug'].'_api_key'    => get_static_option($gateway['slug'].'_api_key'),
                                $gateway['slug'].'_api_secret' => get_static_option($gateway['slug'].'_api_secret'),
                            ];
                        }

                        $credentials = json_encode($credentials);
                    @endphp

                    {{-- Card --}}
                    <div class="sp-gateway-card flex flex-col rounded-2xl overflow-hidden border"
                         style="background:var(--color-bg-surface);border-color:var(--color-border-main);box-shadow:var(--shadow-main);">

                        {{-- Logo Area --}}
                        <div class="relative flex items-center justify-center"
                             style="height:130px;background:linear-gradient(135deg,#e8f5f2 0%,#d0ece8 100%);">

                            <img src="{{ route('tenant.admin.shipping.plugin.logo', $gateway['logo']) }}"
                                 alt="{{ $gateway['name'] }}"
                                 style="max-height:64px;max-width:65%;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(26,92,78,.14));">

                            {{-- Status pill --}}
                            <span class="absolute top-3 right-3 flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="{{ $isActive
                                        ? 'background:var(--color-success-bg);color:var(--color-success);border:1px solid #a7f3d0;'
                                        : 'background:var(--color-danger-bg);color:var(--color-danger);border:1px solid #fecaca;' }}">
                                <span class="inline-block w-1.5 h-1.5 rounded-full"
                                      style="{{ $isActive ? 'background:var(--color-success)' : 'background:var(--color-danger)' }}"></span>
                                {{ $isActive ? __('Active') : __('Inactive') }}
                            </span>

                            {{-- Token error strip (ShipRocket only) --}}
                            @if($gatewayData && !$gatewayData['status'])
                                <div class="absolute bottom-0 left-0 right-0 flex items-center justify-center gap-1 py-1.5 text-xs font-medium text-white"
                                     style="background:var(--color-danger);">
                                    <i class="ti tabler-alert-circle"></i>
                                    {{ $gatewayData['message'] }}
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="flex flex-col flex-1 p-5" style="gap:.85rem;">

                            {{-- Name --}}
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-sm" style="color:var(--color-text-dark)">{{ $gateway['name'] }}</span>
                                @if($gateway['info'])
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                          style="background:var(--color-info-bg);color:var(--color-info);">Info</span>
                                @endif
                            </div>

                            {{-- Description --}}
                            <p class="text-xs leading-relaxed flex-1" style="color:var(--color-text-muted)">
                                {{ __('Configure your') }} {{ $gateway['name'] }} {{ __('shipping integration and manage API credentials.') }}
                                <a href="{{ $gateway['reference'] }}" target="_blank"
                                   class="font-medium underline-offset-2 hover:underline"
                                   style="color:var(--color-primary)">{{ __('Learn more') }}</a>
                            </p>

                            {{-- Action buttons --}}
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- Toggle Active / Inactive --}}
                                <button type="button"
                                        data-option="{{ $gateway['slug'] }}"
                                        class="sp-btn-action pl_active_deactive inline-flex items-center justify-center gap-1.5 flex-1 px-3 py-2 rounded-lg text-xs font-semibold border"
                                        style="{{ $isActive
                                              ? 'background:var(--color-success-bg);color:var(--color-success);border-color:#a7f3d0;'
                                              : 'background:var(--color-danger-bg);color:var(--color-danger);border-color:#fecaca;' }}">
                                    <i class="ti {{ $isActive ? 'tabler-check' : 'tabler-x' }} text-sm"></i>
                                    {{ $isActive ? __('Activated') : __('Deactivated') }}
                                    <x-btn.button-loader class="hidden"/>
                                </button>

                                {{-- Settings --}}
                                <button type="button"
                                        data-option="{{ $gateway['slug'] }}"
                                        data-credentials="{{ $credentials }}"
                                        class="sp-btn-action pl_settings inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold border"
                                        style="background:var(--color-primary-soft);color:var(--color-primary);border-color:var(--color-border-main);">
                                    <i class="ti tabler-settings text-sm"></i>
                                    {{ __('Settings') }}
                                </button>

                                {{-- Config (ShipRocket only) --}}
                                @if($gateway['configuration'])
                                    <button type="button"
                                            onclick="openModal('{{ $gateway['slug'] }}_configuration_modal')"
                                            class="sp-btn-action inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold border"
                                            style="background:var(--color-warning-bg);color:var(--color-warning);border-color:#fde68a;">
                                        <i class="ti tabler-adjustments-horizontal text-sm"></i>
                                        {{ __('Config') }}
                                    </button>
                                @endif

                            </div>
                        </div>

                    </div>{{-- /card --}}
                @endforeach

            </div>{{-- /grid --}}

        </div>
    </div>
</div>

{{-- Modals --}}
@include('shippingplugin::backend.modals.dhl')
@include('shippingplugin::backend.modals.shiprocket')
@include('shippingplugin::backend.modals.shiprocket-config')
@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    /* ── Modal helpers ───────────────────────────────────────── */
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    window.openModal  = openModal;
    window.closeModal = closeModal;

    /* ── Settings button: populate fields then open modal ────── */
    $(document).on('click', '.pl_settings', function () {
        var el          = $(this);
        var option      = el.data('option');
        var credentials = el.data('credentials');
        if (typeof credentials === 'string') {
            credentials = JSON.parse(credentials);
        }
        var modal = $('#' + option + '_modal');
        $.each(credentials, function (key, val) {
            modal.find('input[name="' + key + '"]').val(val || '');
        });
        openModal(option + '_modal');
    });

    /* ── Toggle active / inactive ────────────────────────────── */
    $(document).on('click', '.pl_active_deactive', function () {
        var el     = $(this);
        var option = el.data('option');
        el.find('span').toggleClass('hidden');
        el.prop('disabled', true);

        axios.get('{{ route("tenant.admin.shipping.plugin.status.change") }}?option=' + option.toLowerCase())
            .then(function (response) {
                if (response.data.type === 'success') location.reload();
            });
    });

    /* ── Submit loader ───────────────────────────────────────── */
    $(document).on('click', '[data-modal-submit]', function () {
        $(this).find('span').toggleClass('hidden');
    });

})(jQuery);
</script>
@endsection
