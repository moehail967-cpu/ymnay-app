<section class="pt-14 pb-14 md:pt-24 md:pb-24 xl:pt-30 xl:pb-30 2xl:pt-35 2xl:pb-35 bg-bg-light">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="contact-two-wrap rounded-3xl p-6 sm:p-8 border border-gray-100 bg-[#F5F7F9]">
            <form class="contact-two-form"
                  action="{{ route('landlord.frontend.contact.direct') }}"
                  method="POST"
                  data-success="{{ $successMessage }}">
                @csrf
                <input type="hidden" name="recipient_email" value="{{ $recipientEmail }}">

                <div class="ct-error"></div>

                <div>
                    <label>{{ $fullNameLabel }}</label>
                    <input type="text" name="full_name" placeholder="{{ $fullNameLabel }}" required>
                </div>

                <div>
                    <label>{{ $emailLabel }}</label>
                    <input type="email" name="email" placeholder="{{ $emailLabel }}" required>
                </div>

                <div>
                    <label>{{ $phoneLabel }}</label>
                    <input type="tel" name="phone" placeholder="{{ $phoneLabel }}">
                </div>

                <div>
                    <label>{{ $subjectLabel }}</label>
                    <input type="text" name="subject" placeholder="{{ $subjectLabel }}">
                </div>

                <div class="ct-field-full">
                    <label>{{ $messageLabel }}</label>
                    <textarea name="message" rows="6" placeholder="{{ $messageLabel }}" required></textarea>
                </div>

                @if(!empty(config('services.recaptcha.site_key')))
                <div class="ct-recaptcha">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
                @endif

                <div class="ct-btn-wrap">
                    <button type="submit" class="ct-submit-btn">{{ $submitText }}</button>
                </div>
            </form>
        </div>
    </div>
</section>

@if(!empty(config('services.recaptcha.site_key')))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if(!empty($cards))
<section class="pb-14 md:pb-24 xl:pb-30 2xl:pb-35 bg-bg-light">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($cards as $card)
            <article class="address-card bg-[#F5F7F9] rounded-3xl p-8 sm:p-10 lg:p-12 max-w-sm w-full text-center transition-all duration-300 ease-out hover:shadow-lg hover:bg-white hover:-translate-y-1"
                     role="region">
                @if(!empty($card['icon']))
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-sectionC rounded-2xl flex items-center justify-center">
                        <img src="{{ $card['icon'] }}" alt="{{ $card['title'] }}">
                    </div>
                </div>
                @endif
                <h2 class="text-secondary text-2xl sm:text-3xl font-normal mb-4">{{ $card['title'] }}</h2>
                <address class="not-italic text-quate text-base sm:text-lg leading-relaxed flex flex-col">
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
