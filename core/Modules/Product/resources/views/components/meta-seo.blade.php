@php
    if(!isset($metaData)){
        $metaData = null;
    }
@endphp

<div class="meta-seo-wrapper space-y-5">
    <h4 class="product-section-title">
        <i class="mdi mdi-search-web mr-1"></i>{{ __("Meta SEO") }}
    </h4>

    <p class="text-xs text-muted -mt-2">{{ __('Optimize your product for search engines and social media sharing.') }}</p>

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-0.5 bg-secondary rounded-xl p-1">
        <button type="button" class="meta-seo-tab flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition bg-surface shadow-sm text-dark" data-meta-tab="panel-general-meta">
            <i class="mdi mdi-earth text-sm"></i> {{ __("General") }}
        </button>
        <button type="button" class="meta-seo-tab flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition text-muted hover:text-dark" data-meta-tab="panel-facebook-meta">
            <i class="mdi mdi-facebook text-sm"></i> {{ __("Facebook") }}
        </button>
        <button type="button" class="meta-seo-tab flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition text-muted hover:text-dark" data-meta-tab="panel-twitter-meta">
            <i class="mdi mdi-twitter text-sm"></i> {{ __("Twitter") }}
        </button>
    </div>

    {{-- Tab Panels --}}
    <div class="meta-seo-panels">

        {{-- General Meta Info --}}
        <div class="meta-seo-panel space-y-4" id="panel-general-meta">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="mdi mdi-earth text-primary text-sm"></i>
                </div>
                <h5 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __('General Meta Info') }}</h5>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Meta Title") }}</label>
                <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <i class="mdi mdi-format-title text-primary text-base"></i>
                    <input type="text" id="general-title" value="{{ $metaData?->title }}" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" name="general_title" placeholder="{{ __("Enter meta title for search engines") }}">
                </div>
                <p class="text-[10px] text-muted mt-1">{{ __('Recommended: 50-60 characters') }}</p>
                <span class="product-error-msg general_title-error"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Meta Description") }}</label>
                <div class="bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <textarea id="general-description" name="general_description" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none" rows="4" placeholder="{{ __("Write a compelling meta description for search results") }}">{{ $metaData?->description }}</textarea>
                </div>
                <p class="text-[10px] text-muted mt-1">{{ __('Recommended: 150-160 characters') }}</p>
                <span class="product-error-msg general_description-error"></span>
            </div>
        </div>

        {{-- Facebook Meta --}}
        <div class="meta-seo-panel space-y-4 hidden" id="panel-facebook-meta">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-[#1877F2]/10 flex items-center justify-center">
                    <i class="mdi mdi-facebook text-[#1877F2] text-sm"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __("Facebook Open Graph") }}</h5>
                    <p class="text-[10px] text-muted">{{ __('Controls how this product appears when shared on Facebook') }}</p>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("OG Title") }}</label>
                <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <i class="mdi mdi-format-title text-[#1877F2] text-base"></i>
                    <input type="text" id="facebook-title" name="facebook_title" value="{{ $metaData?->fb_title }}" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{ __("Facebook share title") }}">
                </div>
                <span class="product-error-msg facebook_title-error"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("OG Description") }}</label>
                <div class="bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <textarea id="facebook-description" name="facebook_description" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none" rows="4" placeholder="{{ __("Description shown when shared on Facebook") }}">{{ $metaData?->fb_description }}</textarea>
                </div>
                <span class="product-error-msg facebook_description-error"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("OG Image") }}</label>
                <x-fields.tw-media-upload :id="$metaData?->fb_image" :title="__('Facebook Share Image')" :name="'facebook_image'" :dimentions="'1200x630'"/>
            </div>
        </div>

        {{-- Twitter Meta --}}
        <div class="meta-seo-panel space-y-4 hidden" id="panel-twitter-meta">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-[#1DA1F2]/10 flex items-center justify-center">
                    <i class="mdi mdi-twitter text-[#1DA1F2] text-sm"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __("Twitter Card") }}</h5>
                    <p class="text-[10px] text-muted">{{ __('Controls how this product appears when shared on Twitter') }}</p>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Card Title") }}</label>
                <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <i class="mdi mdi-format-title text-[#1DA1F2] text-base"></i>
                    <input type="text" id="twitter-title" value="{{ $metaData?->tw_title }}" name="twitter_title" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{ __("Twitter share title") }}">
                </div>
                <span class="product-error-msg twitter_title-error"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Card Description") }}</label>
                <div class="bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                    <textarea id="twitter-description" name="twitter_description" class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none" rows="4" placeholder="{{ __("Description shown when shared on Twitter") }}">{{ $metaData?->tw_description }}</textarea>
                </div>
                <span class="product-error-msg twitter_description-error"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Card Image") }}</label>
                <x-fields.tw-media-upload :id="$metaData?->tw_image" :title="__('Twitter Card Image')" :name="'twitter_image'" :dimentions="'1200x600'"/>
            </div>
        </div>
    </div>
</div>

<div class="product-nav-buttons">
    <button type="button" class="prev-step inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
        <i class="mdi mdi-arrow-left"></i> {{ __('Previous') }}
    </button>
    <button type="button" class="next-step inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
        {{ __('Next') }} <i class="mdi mdi-arrow-right"></i>
    </button>
    <button type="submit" class="submit-form inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-success hover:opacity-90 transition" style="display: none;">
        <i class="mdi mdi-check"></i> {{ isset($product) ? __('Update Product') : __('Create Product') }}
    </button>
</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function () {
        var tabBtns = document.querySelectorAll('.meta-seo-tab');
        var panels  = document.querySelectorAll('.meta-seo-panel');

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = this.getAttribute('data-meta-tab');

                // Reset all tabs
                tabBtns.forEach(function (b) {
                    b.classList.remove('bg-surface', 'shadow-sm', 'text-dark');
                    b.classList.add('text-muted', 'hover:text-dark');
                });
                // Activate clicked tab
                this.classList.add('bg-surface', 'shadow-sm', 'text-dark');
                this.classList.remove('text-muted', 'hover:text-dark');

                // Show/hide panels
                panels.forEach(function (panel) {
                    if (panel.id === target) {
                        panel.classList.remove('hidden');
                    } else {
                        panel.classList.add('hidden');
                    }
                });
            });
        });
    });
})();
</script>
