@extends('landlord.frontend.frontend-page-master')

@section('title', __('wizard.title'))

@section('content')
@php
    $steps = [
        'plans' => __('wizard.step_plan'),
        'theme' => __('wizard.step_theme'),
        'store' => __('wizard.step_store'),
        'register' => __('wizard.step_register'),
        'payment' => __('wizard.step_payment'),
    ];
    $activeStep = array_search($step, array_keys($steps), true);
    $baseUrl = str_replace(['http://', 'https://'], '', url('/'));
@endphp

<style>
    .theme-wizard-card { border-color: transparent; transition: border-color .15s ease, box-shadow .15s ease; }
    .theme-wizard-card.is-selected { border-color: var(--main-color-one, #0C4D54); box-shadow: 0 0 0 1px var(--main-color-one, #0C4D54); }
</style>

<section class="py-16" style="background-color: var(--section-bg-3, #f4f8fb); min-height: 70vh">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="mb-10">
            <div class="flex flex-wrap gap-3 items-center justify-center text-sm">
                @foreach($steps as $key => $label)
                    @php
                        $index = array_search($key, array_keys($steps), true);
                    @endphp
                    <div class="flex items-center gap-2 {{ $index <= $activeStep ? 'text-primary font-semibold' : 'text-gray-400' }}">
                        <span class="w-7 h-7 rounded-full inline-flex items-center justify-center border {{ $index <= $activeStep ? 'border-primary bg-primary text-white' : 'border-gray-300' }}">{{ $index + 1 }}</span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <x-flash-msg-tw/>
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700">{{ $errors->first() }}</div>
        @endif

        @if($step === 'plans')
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-secondary">{{ __('wizard.choose_plan_heading') }}</h1>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($plans as $item)
                    <article class="border rounded-lg p-6 bg-white flex flex-col">
                        <h2 class="text-xl font-semibold text-secondary">{{ $item->title }}</h2>
                        <p class="text-2xl font-bold text-primary my-4">{{ amount_with_currency_symbol($item->price) }}</p>
                        <p class="text-sm text-gray-600 flex-1">{{ $item->package_description }}</p>
                        <a class="mt-6 w-full text-center py-3 rounded-lg bg-primary text-white" href="{{ route('landlord.frontend.plan.order.theme', $item->id) }}">{{ __('wizard.choose_plan') }}</a>
                    </article>
                @empty
                    <p class="col-span-full text-center text-gray-600">{{ __('wizard.no_plans') }}</p>
                @endforelse
            </div>

        @elseif($step === 'theme')
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-secondary">{{ __('wizard.choose_theme_heading') }}</h1>
                <p class="text-gray-600 mt-2">{{ $plan->title }}</p>
            </div>
            <form method="post" action="{{ route('landlord.frontend.plan.order.theme.store', $plan->id) }}">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(getPricePlanBasedAllThemeData($themeSlugs) as $theme)
                        @php
                            $themeData = getIndividualThemeDetails($theme->slug);
                            $name = get_static_option_central($themeData['slug'].'_theme_name') ?: $themeData['name'];
                            $image = get_static_option_central($themeData['slug'].'_theme_image') ?: loadScreenshot($theme->slug);
                            $checked = ($state['theme_slug'] ?? get_static_option('default_theme')) === $theme->slug;
                        @endphp
                        <label class="theme-wizard-card border-2 rounded-lg overflow-hidden bg-white cursor-pointer {{ $checked ? 'is-selected' : '' }}" data-theme-card>
                            <input type="radio" class="sr-only" name="theme_slug" value="{{ $theme->slug }}" {{ $checked ? 'checked' : '' }}>
                            <img src="{{ $image }}" alt="{{ $name }}" class="w-full aspect-video object-cover">
                            <span class="block p-4 font-semibold text-secondary">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-8 flex justify-between">
                    <a href="{{ route('landlord.frontend.plan.order.start') }}" class="py-3 px-6">{{ __('wizard.back') }}</a>
                    <button class="py-3 px-6 rounded-lg bg-primary text-white" type="submit">{{ __('wizard.continue') }}</button>
                </div>
            </form>

        @elseif($step === 'store')
            <div class="max-w-xl mx-auto bg-white border rounded-lg p-8">
                <h1 class="text-2xl font-bold text-secondary mb-2">{{ __('wizard.store_address_heading') }}</h1>
                <p class="text-gray-600 mb-6">{{ __('wizard.store_address_help') }}</p>
                <form method="post" action="{{ route('landlord.frontend.plan.order.store.save', $plan->id) }}">
                    @csrf
                    <label class="block font-medium mb-2" for="subdomain">{{ __('wizard.subdomain') }}</label>
                    <div class="flex">
                        <input id="subdomain" name="subdomain" value="{{ old('subdomain', $state['subdomain'] ?? '') }}" class="flex-1 p-3 border rounded-s-lg" required autocomplete="off">
                        <span class="p-3 border border-s-0 rounded-e-lg bg-gray-50 text-sm">.{{ $baseUrl }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">{{ __('wizard.subdomain_help') }}</p>
                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('landlord.frontend.plan.order.theme', $plan->id) }}" class="py-3 px-6">{{ __('wizard.back') }}</a>
                        <button class="py-3 px-6 rounded-lg bg-primary text-white" type="submit">{{ __('wizard.continue') }}</button>
                    </div>
                </form>
            </div>

        @elseif($step === 'register')
            <div class="max-w-xl mx-auto bg-white border rounded-lg p-8">
                <h1 class="text-2xl font-bold text-secondary mb-6">{{ __('wizard.create_account') }}</h1>
                <div id="register-message" class="mb-4"></div>
                <form id="wizard-register-form" novalidate>
                    @csrf
                    <input class="w-full p-3 border rounded-lg mb-4" name="name" placeholder="{{ __('wizard.full_name') }}" required>
                    <input class="w-full p-3 border rounded-lg mb-4" name="email" type="email" placeholder="{{ __('wizard.email') }}" required>
                    <input class="w-full p-3 border rounded-lg mb-4" name="phone" placeholder="{{ __('wizard.phone') }}" required>
                    <input class="w-full p-3 border rounded-lg mb-4" name="username" placeholder="{{ __('wizard.username') }}" required>
                    <input class="w-full p-3 border rounded-lg mb-4" name="password" type="password" placeholder="{{ __('wizard.password') }}" required>
                    <input class="w-full p-3 border rounded-lg mb-4" name="password_confirmation" type="password" placeholder="{{ __('wizard.confirm_password') }}" required>
                    <label class="flex gap-2 text-sm mb-6"><input name="terms_condition" type="checkbox" value="1" required> {{ __('wizard.terms') }}</label>
                    <button id="send-otp" class="w-full py-3 rounded-lg bg-primary text-white" type="submit">{{ __('wizard.send_otp') }}</button>
                </form>
                <div id="otp-panel" class="hidden mt-6">
                    <input id="otp" class="w-full p-3 border rounded-lg mb-4" inputmode="numeric" maxlength="6" placeholder="{{ __('wizard.otp_placeholder') }}">
                    <button id="verify-otp" class="w-full py-3 rounded-lg bg-primary text-white">{{ __('wizard.verify_continue') }}</button>
                </div>
                <p class="text-center text-sm mt-6"><a href="{{ route('landlord.user.login') }}">{{ __('wizard.already_account') }}</a></p>
            </div>

        @elseif($step === 'payment')
            <div class="max-w-3xl mx-auto bg-white border rounded-lg p-8">
                <h1 class="text-2xl font-bold text-secondary mb-2">{{ __('wizard.payment') }}</h1>
                <p class="text-gray-600 mb-6">{{ $plan->title }} - {{ amount_with_currency_symbol($plan->price) }}</p>
                <form method="post" enctype="multipart/form-data" action="{{ route('landlord.frontend.order.payment.form') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $plan->id }}">
                    <input type="hidden" name="theme_slug" value="{{ $state['theme_slug'] }}">
                    <input type="hidden" name="subdomain" value="custom_domain__dd">
                    <input type="hidden" name="custom_subdomain" value="{{ $state['subdomain'] }}">
                    <input type="hidden" name="name" value="{{ auth('web')->user()->name }}">
                    <input type="hidden" name="email" value="{{ auth('web')->user()->email }}">
                    <input type="hidden" name="payment_gateway" id="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                    {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    <div id="manual-payment" class="hidden mt-4">
                        <input class="w-full p-3 border rounded-lg mb-3" name="trasaction_id" placeholder="{{ __('wizard.transaction_id') }}">
                        <input class="w-full p-3 border rounded-lg" name="trasaction_attachment" type="file" accept="image/*">
                    </div>
                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('landlord.frontend.plan.order.store', $plan->id) }}" class="py-3 px-6">{{ __('wizard.back') }}</a>
                        <button class="py-3 px-6 rounded-lg bg-primary text-white" type="submit">{{ __('wizard.pay_create') }}</button>
                    </div>
                </form>
            </div>

        @elseif($step === 'create-store')
            <div class="max-w-xl mx-auto bg-white border rounded-lg p-8 text-center">
                <h1 class="text-2xl font-bold text-secondary mb-3">{{ __('wizard.creating_store') }}</h1>
                <p class="text-gray-600">{{ $state['subdomain'] }}.{{ $baseUrl }}</p>
                <p id="create-store-message" class="mt-6 text-gray-600">{{ __('wizard.preparing_store') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection

@section('scripts')
@if($step === 'theme')
<script>
document.querySelectorAll('[data-theme-card]').forEach(function (card) {
    card.addEventListener('click', function () {
        const input = card.querySelector('input[name="theme_slug"]');
        input.checked = true;
        document.querySelectorAll('[data-theme-card]').forEach(function (item) {
            item.classList.toggle('is-selected', item === card);
        });
    });
});
</script>
@endif
@if($step === 'register')
<script>
document.getElementById('wizard-register-form').addEventListener('submit', async function (event) {
    event.preventDefault();
    const form = this;
    const message = document.getElementById('register-message');
    const button = document.getElementById('send-otp');
    const originalText = button.textContent;
    if (!form.checkValidity()) {
        form.reportValidity();
        message.textContent = '{{ __('wizard.complete_required') }}';
        return;
    }

    if (form.elements.password.value !== form.elements.password_confirmation.value) {
        message.textContent = '{{ __('wizard.password_mismatch') }}';
        return;
    }

    button.disabled = true;
    button.textContent = '{{ __('wizard.sending') }}';
    message.textContent = '';

    const requestController = new AbortController();
    const requestTimeout = window.setTimeout(function () {
        requestController.abort();
    }, 25000);

    try {
        const response = await fetch('{{ route('landlord.user.register.otp.store') }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value, 'Accept': 'application/json'},
            body: new FormData(form),
            credentials: 'same-origin',
            signal: requestController.signal,
        });
        const data = await response.json().catch(function () { return {}; });

        if (response.ok && data.status === 'otp_sent') {
            document.getElementById('otp-panel').classList.remove('hidden');
            message.textContent = data.msg;
            return;
        }

        message.textContent = data.msg || data.message || Object.values(data.errors || {}).flat().join(' ') || '{{ __('wizard.otp_send_failed') }}';
    } catch (error) {
        message.textContent = '{{ __('wizard.otp_send_failed') }}';
    } finally {
        window.clearTimeout(requestTimeout);
        button.disabled = false;
        button.textContent = originalText;
    }
});
document.getElementById('verify-otp').addEventListener('click', async function () {
    const response = await fetch('{{ route('landlord.user.register.otp.verify') }}', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'Accept': 'application/json'}, body: JSON.stringify({otp: document.getElementById('otp').value})});
    const data = await response.json();
    if (data.status === 'valid') window.location.href = '{{ (float) $plan->price === 0.0 ? route('landlord.frontend.plan.order.create-store', $plan->id) : route('landlord.frontend.plan.order.payment', $plan->id) }}';
    else document.getElementById('register-message').textContent = data.msg;
});
</script>
@endif
@if($step === 'payment')
<script>
document.querySelectorAll('.payment-gateway-wrapper > ul > li').forEach(function (item) {
    item.addEventListener('click', function () {
        const gateway = item.dataset.gateway;
        document.getElementById('payment_gateway').value = gateway;
        document.getElementById('order_from_user_wallet').value = gateway;
        document.getElementById('manual-payment').classList.toggle('hidden', gateway !== 'manual_payment');
    });
});
</script>
@endif
@if($step === 'create-store')
<script>
fetch('{{ route('landlord.frontend.plan.order.create-store.provision', $plan->id) }}', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}, body: JSON.stringify({})})
    .then(response => response.json())
    .then(data => { if (data.type === 'success') window.location.href = data.redirect; else document.getElementById('create-store-message').textContent = data.msg || '{{ __('wizard.unable_create') }}'; })
    .catch(() => document.getElementById('create-store-message').textContent = '{{ __('wizard.unable_create') }}');
</script>
@endif
@endsection
