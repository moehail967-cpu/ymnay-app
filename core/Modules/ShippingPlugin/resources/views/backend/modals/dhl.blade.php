{{-- DHL Settings Modal --}}
<div id="dhl_modal" class="fixed inset-0 z-[800] hidden">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('dhl_modal')"></div>

    {{-- Dialog --}}
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="ti tabler-truck-delivery text-primary text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('DHL Settings') }}</h3>
                </div>
                <button type="button" onclick="closeModal('dhl_modal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="ti tabler-x text-base"></i>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('tenant.admin.shipping.plugin.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="shipping_gateway_name" value="dhl">

                <div class="px-6 py-5 space-y-4">

                    <p class="text-[10px] font-bold tracking-widest text-muted uppercase pb-2 border-b border-main">
                        {{ __('API Credentials') }}
                    </p>

                    {{-- API Key --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{ __('DHL API Key') }} <span class="text-danger normal-case">*</span>
                        </label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="ti tabler-key text-lg text-primary"></i>
                            <input type="text"
                                   name="dhl_api_key"
                                   placeholder="{{ __('Enter your DHL API Key') }}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- API Secret --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{ __('DHL API Secret') }} <span class="text-danger normal-case">*</span>
                        </label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="ti tabler-lock text-lg text-primary"></i>
                            <input type="text"
                                   name="dhl_api_secret"
                                   placeholder="{{ __('Enter your DHL API Secret') }}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('dhl_modal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" data-modal-submit
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="ti tabler-device-floppy text-base"></i>
                        {{ __('Save Changes') }}
                        <x-btn.button-loader class="hidden"/>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
