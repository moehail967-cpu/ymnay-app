@extends($layout)

@section('title') {{ __('AI Integration') }} @endsection

@section('content')

{{-- Page header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-robot-outline text-primary text-xl"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-dark font-urbanist">{{ __('AI Integration') }}</h3>
            <p class="text-xs text-muted">{{ __('Configure AI providers to generate product, blog, and page descriptions.') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-check-circle-outline"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-alert-circle-outline"></i> {{ session('error') }}
    </div>
@endif

{{-- How it works collapsible --}}
<div x-data="{ open: false }" class="bg-surface rounded-xl border border-primary/30 mb-6 overflow-hidden">
    <button type="button" @click="open = !open"
            class="w-full flex items-center gap-3 px-5 py-4 text-left bg-primary/5 hover:bg-primary/10 transition-colors">
        <i class="mdi mdi-book-open-variant text-primary"></i>
        <span class="text-sm font-semibold text-primary">{{ __('How AI Integration Works') }}</span>
        <span class="text-xs text-muted ml-2">{{ __('Click to expand') }}</span>
        <i class="mdi text-primary ml-auto text-base" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
    </button>
    <div x-show="open" x-transition class="px-5 py-4 grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-main">
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-pencil-outline text-primary"></i> {{ __('Open any editor') }}</p>
            <p class="text-xs text-muted">{{ __('Navigate to Product, Blog, or Page edit screens — a "Generate with AI" button appears in the rich-text toolbar.') }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-robot-outline text-violet-500"></i> {{ __('Describe your content') }}</p>
            <p class="text-xs text-muted">{{ __('Enter a short hint (e.g. "noise-cancelling wireless headphones"). Choose content type and tone, then click Generate.') }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-check-circle-outline text-green-600"></i> {{ __('Insert into editor') }}</p>
            <p class="text-xs text-muted">{{ __('Review the generated content in the preview panel. Click "Insert" to place it directly into the editor.') }}</p>
        </div>
    </div>
</div>

<form action="{{ $saveRoute }}" method="POST" id="aiSettingsForm">
@csrf

{{-- Provider settings --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main">
        <p class="text-sm font-bold text-dark">{{ __('Provider Settings') }}</p>
        <p class="text-xs text-muted mt-0.5">{{ __('Choose which AI provider is used by default and configure a fallback.') }}</p>
    </div>
    <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Default Provider') }}</label>
            <select name="provider" id="providerSelect"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary">
                <option value="openai"   {{ $provider === 'openai'   ? 'selected' : '' }}>OpenAI</option>
                <option value="deepseek" {{ $provider === 'deepseek' ? 'selected' : '' }}>DeepSeek</option>
                <option value="gemini"   {{ $provider === 'gemini'   ? 'selected' : '' }}>Gemini</option>
            </select>
            <p class="text-[11px] text-muted mt-1">{{ __('Used when clicking "Generate with AI" in the editor.') }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Fallback Provider') }}</label>
            <select name="fallback_provider" id="fallbackSelect"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary">
                <option value=""         {{ $fallback_provider === ''        ? 'selected' : '' }}>{{ __('None') }}</option>
                <option value="openai"   {{ $fallback_provider === 'openai'  ? 'selected' : '' }}>OpenAI</option>
                <option value="deepseek" {{ $fallback_provider === 'deepseek'? 'selected' : '' }}>DeepSeek</option>
                <option value="gemini"   {{ $fallback_provider === 'gemini'  ? 'selected' : '' }}>Gemini</option>
            </select>
            <p class="text-[11px] text-muted mt-1">{{ __('Automatically tried if the default provider fails.') }}</p>
        </div>
    </div>
</div>

{{-- OpenAI --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-brain text-green-600 text-base"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-dark">{{ __('OpenAI') }}</p>
            <p class="text-xs text-muted mt-0.5">{{ __('GPT-5, GPT-4o, and more. Get your key at platform.openai.com.') }}</p>
        </div>
    </div>
    <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('API Key') }}</label>
            <input type="password" name="openai_key" value="{{ $openai_key }}"
                   placeholder="sk-..."
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Model') }}</label>
            @php
                $openaiModels  = ['gpt-5', 'gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-3.5-turbo'];
                $openaiCustom  = !in_array($openai_model, $openaiModels) && $openai_model !== '';
            @endphp
            <select name="openai_model" id="openaiModelSelect"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary">
                @foreach($openaiModels as $m)
                    <option value="{{ $m }}" {{ $openai_model === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
                <option value="__custom__" {{ $openaiCustom ? 'selected' : '' }}>{{ __('Custom…') }}</option>
            </select>
            <input type="text" id="openaiModelCustom"
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono mt-1.5 {{ $openaiCustom ? '' : 'hidden' }}"
                   placeholder="{{ __('Enter model name…') }}"
                   value="{{ $openaiCustom ? $openai_model : '' }}">
        </div>
    </div>
</div>

{{-- DeepSeek --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-layers-outline text-blue-600 text-base"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-dark">{{ __('DeepSeek') }}</p>
            <p class="text-xs text-muted mt-0.5">{{ __('Cost-effective with strong writing ability. Get your key at platform.deepseek.com.') }}</p>
        </div>
    </div>
    <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('API Key') }}</label>
            <input type="password" name="deepseek_key" value="{{ $deepseek_key }}"
                   placeholder="sk-..."
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Model') }}</label>
            @php
                $deepseekModels = ['deepseek-v4', 'deepseek-chat', 'deepseek-reasoner'];
                $deepseekCustom = !in_array($deepseek_model, $deepseekModels) && $deepseek_model !== '';
            @endphp
            <select name="deepseek_model" id="deepseekModelSelect"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary">
                @foreach($deepseekModels as $m)
                    <option value="{{ $m }}" {{ $deepseek_model === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
                <option value="__custom__" {{ $deepseekCustom ? 'selected' : '' }}>{{ __('Custom…') }}</option>
            </select>
            <input type="text" id="deepseekModelCustom"
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono mt-1.5 {{ $deepseekCustom ? '' : 'hidden' }}"
                   placeholder="{{ __('Enter model name…') }}"
                   value="{{ $deepseekCustom ? $deepseek_model : '' }}">
        </div>
    </div>
</div>

{{-- Gemini --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-google text-amber-500 text-base"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-dark">{{ __('Gemini') }}</p>
            <p class="text-xs text-muted mt-0.5">{{ __("Google's Gemini models. Free tier available at aistudio.google.com.") }}</p>
        </div>
    </div>
    <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('API Key') }}</label>
            <input type="password" name="gemini_key" value="{{ $gemini_key }}"
                   placeholder="{{ __('AIza...') }}"
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Model') }}</label>
            @php
                $geminiModels  = ['gemini-3.0-pro', 'gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-1.5-flash'];
                $geminiCustom  = !in_array($gemini_model, $geminiModels) && $gemini_model !== '';
            @endphp
            <select name="gemini_model" id="geminiModelSelect"
                    class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary">
                @foreach($geminiModels as $m)
                    <option value="{{ $m }}" {{ $gemini_model === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
                <option value="__custom__" {{ $geminiCustom ? 'selected' : '' }}>{{ __('Custom…') }}</option>
            </select>
            <input type="text" id="geminiModelCustom"
                   class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary text-dark focus:outline-none focus:border-primary font-mono mt-1.5 {{ $geminiCustom ? '' : 'hidden' }}"
                   placeholder="{{ __('Enter model name…') }}"
                   value="{{ $geminiCustom ? $gemini_model : '' }}">
        </div>
    </div>
</div>

{{-- Temperature --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-thermometer text-orange-500 text-base"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-dark">{{ __('Temperature') }}</p>
            <p class="text-xs text-muted mt-0.5">{{ __('Controls creativity. Lower = more focused, higher = more creative (0.0 – 1.0).') }}</p>
        </div>
    </div>
    <div class="px-5 py-5">
        <div class="flex items-center gap-4">
            <input type="range" name="temperature" id="tempRange"
                   min="0" max="1" step="0.1" value="{{ $temperature }}"
                   class="w-48 accent-primary">
            <span id="tempDisplay" class="text-sm font-mono font-bold text-dark w-8">{{ $temperature }}</span>
            <span class="text-xs text-muted">{{ __('Applied to all providers.') }}</span>
        </div>
    </div>
</div>

<div class="flex justify-end mb-6">
    <button type="submit"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
        <i class="mdi mdi-content-save text-base"></i>
        {{ __('Save Settings') }}
    </button>
</div>

</form>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Temperature display
    var range   = document.getElementById('tempRange');
    var display = document.getElementById('tempDisplay');
    if (range) {
        range.addEventListener('input', function () { display.textContent = this.value; });
    }

    // Fallback provider — disable option that matches primary
    var providerSel = document.getElementById('providerSelect');
    var fallbackSel = document.getElementById('fallbackSelect');

    function syncFallback() {
        var primary = providerSel.value;
        Array.from(fallbackSel.options).forEach(function (opt) {
            opt.disabled = opt.value !== '' && opt.value === primary;
            if (opt.disabled && fallbackSel.value === opt.value) fallbackSel.value = '';
        });
    }

    if (providerSel && fallbackSel) {
        syncFallback();
        providerSel.addEventListener('change', syncFallback);
    }

    // Custom model toggle
    [
        ['openaiModelSelect',   'openaiModelCustom',   'openai_model'],
        ['deepseekModelSelect', 'deepseekModelCustom', 'deepseek_model'],
        ['geminiModelSelect',   'geminiModelCustom',   'gemini_model'],
    ].forEach(function (pair) {
        var sel    = document.getElementById(pair[0]);
        var custom = document.getElementById(pair[1]);
        if (!sel || !custom) return;

        sel.addEventListener('change', function () {
            if (this.value === '__custom__') {
                custom.classList.remove('hidden');
                custom.focus();
                custom.name = pair[2];
                sel.removeAttribute('name');
            } else {
                custom.classList.add('hidden');
                custom.removeAttribute('name');
                sel.name = pair[2];
            }
        });

        if (!custom.classList.contains('hidden')) {
            custom.name = pair[2];
            sel.removeAttribute('name');
        }
    });
});
</script>
@endsection
