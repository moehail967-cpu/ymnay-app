{{-- ShipRocket Configuration Modal --}}
<div id="shiprocket_configuration_modal" class="fixed inset-0 z-[800] hidden">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('shiprocket_configuration_modal')"></div>

    {{-- Dialog --}}
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:var(--color-warning-bg);">
                        <i class="ti tabler-adjustments-horizontal text-sm" style="color:var(--color-warning);"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('ShipRocket Configuration') }}</h3>
                </div>
                <button type="button" onclick="closeModal('shiprocket_configuration_modal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="ti tabler-x text-base"></i>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('tenant.admin.shipping.plugin.configuration.update') }}" method="POST">
                @csrf
                <input type="hidden" name="shipping_gateway_name" value="shiprocket">

                <div class="px-6 py-5 space-y-5">

                    {{-- Pickup Location --}}
                    <div>
                        <p class="text-[10px] font-bold tracking-widest text-muted uppercase pb-2 mb-4 border-b border-main">
                            {{ __('Shipment Setup') }}
                        </p>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{ __('Pickup Location') }}
                        </label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="ti tabler-map-pin text-lg text-primary"></i>
                            <select name="shiprocket_pickup_location"
                                    id="shiprocket_pickup_location"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach($data['list'] ?? [] as $item)
                                    <option value="{{ $item->pickup_location }}"
                                            {{ get_static_option('shiprocket_pickup_location') == $item->pickup_location ? 'selected' : '' }}>
                                        {{ $item->pickup_location }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ti tabler-chevron-down text-sm text-muted pointer-events-none"></i>
                        </div>
                        <p class="flex items-center gap-1 mt-2 text-xs" style="color:var(--color-text-muted)">
                            <i class="ti tabler-info-circle text-sm flex-shrink-0"></i>
                            {{ __('Select a pickup location created in your ShipRocket account.') }}
                        </p>
                    </div>

                    {{-- Toggle Options --}}
                    <div>
                        <p class="text-[10px] font-bold tracking-widest text-muted uppercase pb-2 mb-3 border-b border-main">
                            {{ __('Options') }}
                        </p>

                        {{-- Auto Create Order --}}
                        <div class="flex items-start justify-between gap-4 py-3 border-b border-main">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-dark mb-0.5">{{ __('Auto Create Order') }}</p>
                                <p class="text-xs" style="color:var(--color-text-muted)">{{ __('Automatically creates a ShipRocket order once a customer completes payment.') }}</p>
                            </div>
                            <div class="flex-shrink-0 pt-0.5">
                                <x-fields.switcher label="" name="shiprocket_auto_create_order_option"
                                                   set-value="on"
                                                   value="{{ get_static_option('shiprocket_auto_create_order_option') }}"/>
                            </div>
                        </div>

                        {{-- Disable Order Tracking --}}
                        <div class="flex items-start justify-between gap-4 py-3">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-dark mb-0.5">{{ __('Disable Order Tracking') }}</p>
                                <p class="text-xs" style="color:var(--color-text-muted)">{{ __('When enabled, hides the order tracking feature for customers on the frontend.') }}</p>
                            </div>
                            <div class="flex-shrink-0 pt-0.5">
                                <x-fields.switcher label="" name="shiprocket_order_tracking_option"
                                                   set-value="on"
                                                   value="{{ get_static_option('shiprocket_order_tracking_option') }}"/>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('shiprocket_configuration_modal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" data-modal-submit
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="ti tabler-device-floppy text-base"></i>
                        {{ __('Save Configuration') }}
                        <x-btn.button-loader class="hidden"/>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
