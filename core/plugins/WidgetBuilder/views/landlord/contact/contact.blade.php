<section class="pt-14 pb-14 md:pt-24 md:pb-24 xl:pt-30 xl:pb-30 2xl:pt-35 2xl:pb-35" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8 max-w-7xl">
        <div class="contact-form-wrap rounded-3xl p-6 sm:p-8 border" style="background-color: var(--section-bg-1, #FFFFFF); border-color: var(--extra-light-color, #e5e7eb)">
            {!! \App\Helpers\FormBuilderCustom::render_form($formId) !!}
        </div>
    </div>
</section>

@if(!empty($cards))
<section class="pb-14 md:pb-24 xl:pb-30 2xl:pb-35" style="background-color: var(--section-bg-3, #F4F8FB)">
    <div class="container mx-auto px-8 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($cards as $card)
            <article class="address-card rounded-3xl p-8 sm:p-10 lg:p-12 max-w-sm w-full text-center transition-all duration-300 ease-out hover:shadow-lg hover:-translate-y-1"
                     style="background-color: var(--section-bg-1, #FFFFFF)"
                     role="region">
                @if(!empty($card['icon']))
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-sectionC rounded-2xl flex items-center justify-center">
                        <img src="{{ $card['icon'] }}" alt="{{ $card['title'] }}" class="text-white">
                    </div>
                </div>
                @endif

                <h2 class="text-secondary text-2xl sm:text-3xl font-normal mb-4">
                    {{ $card['title'] }}
                </h2>

                <address class="not-italic text-base sm:text-lg leading-relaxed flex flex-col" style="color: var(--body-color, #666666)">
                    @foreach($card['info_lines'] as $line)
                    <span>{{ $line }}</span>
                    @endforeach
                </address>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty(config('services.recaptcha.site_key')))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
