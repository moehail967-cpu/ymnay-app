@extends('tenant.admin.admin-master')

@php
    $plugin      = \App\PluginSystem\PluginManager::get('multi-lingual');
    $aiSource    = $plugin ? $plugin->get_option('ai_source',      'ai-integration') : 'ai-integration';
    $aiProvider  = $plugin ? $plugin->get_option('ai_provider',    'openai')         : 'openai';
    $aiKey       = $plugin ? $plugin->get_option('ai_key',         '')               : '';
    $aiModel     = $plugin ? $plugin->get_option('ai_model',       '')               : '';
    $aiTemp      = $plugin ? $plugin->get_option('ai_temperature', '0.3')            : '0.3';
    $aiIntActive = \App\PluginSystem\PluginManager::get('ai-integration') !== null;

    $modelOptions = [
        'openai'   => ['gpt-5','gpt-4.5','gpt-4o','gpt-4o-mini','gpt-4-turbo','gpt-4','gpt-3.5-turbo','o3','o3-mini','o1','o1-mini'],
        'deepseek' => ['deepseek-v4','deepseek-chat','deepseek-reasoner'],
        'gemini'   => ['gemini-2.5-pro','gemini-2.0-flash','gemini-1.5-pro','gemini-1.5-flash'],
    ];

    $activeTab = request('tab', 'settings');
@endphp

@section('title') {{ __('Multi-Lingual') }} @endsection
@section('page-title') {{ __('Multi-Lingual') }} @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">{{ __('Multi-Lingual') }}</a></li>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Tab switching
    $('.ml-tab-btn').on('click', function () {
        var tab = $(this).data('tab');
        $('.ml-tab-btn').removeClass('active border-indigo-600 text-indigo-600').addClass('border-transparent text-gray-500');
        $(this).addClass('active border-indigo-600 text-indigo-600').removeClass('border-transparent text-gray-500');
        $('.ml-tab-pane').addClass('hidden');
        $('#ml_tab_' + tab).removeClass('hidden');
    });

    // Source toggle
    $('#ml_ai_source').on('change', function () {
        if ($(this).val() === 'custom') {
            $('#ml_custom_key_fields').removeClass('hidden');
        } else {
            $('#ml_custom_key_fields').addClass('hidden');
        }
    });

    // Provider → model list sync
    $('[name="ai_provider"]').on('change', function () {
        var prov = $(this).val();
        $('.ml-model-select').addClass('hidden').prop('disabled', true);
        $('[data-model-wrap]').addClass('hidden');
        $('#ml_model_' + prov).removeClass('hidden').prop('disabled', false);
        $('[data-model-wrap="' + prov + '"]').removeClass('hidden');
    });

    // Disable hidden model selects on load
    $('.ml-model-select.hidden').prop('disabled', true);
});
</script>
@endsection

@section('content')
<div class="col-12">

    {{-- Plugin header --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-5">
        <div class="px-5 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-translate text-indigo-500 text-xl"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-dark">{{ __('Multi-Lingual') }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Per-locale content translations for products, blogs, pages, categories, and brands.') }}</p>
            </div>
        </div>

        {{-- Tab strip --}}
        <div class="border-t border-gray-200 px-5 flex gap-0">
            <button type="button" data-tab="settings"
                class="ml-tab-btn active border-b-2 border-indigo-600 text-indigo-600 text-[13px] font-semibold px-4 py-3 -mb-px transition-colors">
                <i class="mdi mdi-cog-outline mr-1.5"></i>{{ __('Settings') }}
            </button>
            <button type="button" data-tab="docs"
                class="ml-tab-btn border-b-2 border-transparent text-gray-500 text-[13px] font-semibold px-4 py-3 -mb-px hover:text-gray-700 transition-colors">
                <i class="mdi mdi-book-open-outline mr-1.5"></i>{{ __('Documentation') }}
            </button>
        </div>
    </div>

    {{-- ── Settings tab ───────────────────────────────────────────────────── --}}
    <div id="ml_tab_settings" class="ml-tab-pane">

        @if(session('success'))
        <div class="flex items-center gap-2 p-3 mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl">
            <i class="mdi mdi-check-circle text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <form action="{{ route('tenant.admin.multi-lingual.settings.save') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- General --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-toggle-switch-outline text-indigo-500 text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark">{{ __('General') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('Enable or disable multi-lingual content translation.') }}</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-dark">{{ __('Enable Translations') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('When disabled, all content shows in the default language only.') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-4">
                                <input type="checkbox" name="enabled" value="1" class="sr-only peer" {{ $enabled == '1' ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- AI Translation --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-robot-outline text-violet-500 text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark">{{ __('AI Auto-Translation') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('Let AI translate your content automatically when editing products, blogs, and pages.') }}</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">

                        {{-- Source --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-dark">{{ __('Credentials Source') }}</label>
                            <div class="relative">
                                <select name="ai_source" id="ml_ai_source"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 appearance-none bg-white pr-9">
                                    <option value="ai-integration" {{ $aiSource === 'ai-integration' ? 'selected' : '' }}>
                                        {{ __('Use AI Integration Plugin') }}{{ $aiIntActive ? '' : ' (' . __('not active') . ')' }}
                                    </option>
                                    <option value="custom" {{ $aiSource === 'custom' ? 'selected' : '' }}>
                                        {{ __('Custom API Key') }}
                                    </option>
                                </select>
                                <i class="mdi mdi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                            @if($aiIntActive)
                            <p class="text-[11px] text-green-600 flex items-center gap-1">
                                <i class="mdi mdi-check-circle-outline"></i>
                                {{ __('AI Integration plugin is active — its configured key will be used automatically.') }}
                            </p>
                            @endif
                        </div>

                        {{-- Custom fields --}}
                        <div id="ml_custom_key_fields" class="{{ $aiSource === 'custom' ? '' : 'hidden' }} space-y-4 pt-2 border-t border-gray-100">

                            {{-- Provider --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-dark">{{ __('Provider') }}</label>
                                <div class="relative">
                                    <select name="ai_provider"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 appearance-none bg-white pr-9">
                                        <option value="openai"   {{ $aiProvider === 'openai'   ? 'selected' : '' }}>OpenAI (GPT)</option>
                                        <option value="deepseek" {{ $aiProvider === 'deepseek' ? 'selected' : '' }}>DeepSeek</option>
                                        <option value="gemini"   {{ $aiProvider === 'gemini'   ? 'selected' : '' }}>Google Gemini</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- API Key --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-dark">{{ __('API Key') }}</label>
                                <input type="password" name="ai_key" value="{{ $aiKey }}"
                                    placeholder="{{ __('sk-…') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400">
                            </div>

                            {{-- Model + Temperature --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-dark">{{ __('Model') }}</label>
                                    @foreach($modelOptions as $prov => $models)
                                    <div class="relative {{ $aiProvider === $prov ? '' : 'hidden' }}" data-model-wrap="{{ $prov }}">
                                        <select name="ai_model" id="ml_model_{{ $prov }}"
                                            class="ml-model-select w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 appearance-none bg-white pr-9 {{ $aiProvider === $prov ? '' : 'hidden' }}"
                                            data-ml-provider="{{ $prov }}"
                                            {{ $aiProvider !== $prov ? 'disabled' : '' }}>
                                            @foreach($models as $m)
                                            <option value="{{ $m }}" {{ $aiModel === $m ? 'selected' : '' }}>{{ $m }}</option>
                                            @endforeach
                                        </select>
                                        <i class="mdi mdi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-dark">{{ __('Temperature') }}</label>
                                    <input type="number" name="ai_temperature" value="{{ $aiTemp }}"
                                        min="0" max="1" step="0.1"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400">
                                    <p class="text-[11px] text-gray-400">{{ __('0 = precise, 1 = creative') }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right sidebar --}}
            <div class="space-y-5">

                {{-- Save --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        <i class="mdi mdi-content-save-outline"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>

                {{-- Active languages --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-dark">{{ __('Active Languages') }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Manage languages in Settings → Languages.') }}</p>
                    </div>
                    <div class="p-4">
                        @if($langs->isEmpty())
                        <p class="text-xs text-gray-500">{{ __('No active languages found.') }}</p>
                        @else
                        <div class="space-y-1.5">
                            @foreach($langs as $lang)
                            <div class="flex items-center justify-between py-1.5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <span class="text-xs font-medium text-dark">{{ $lang->name }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[11px] text-gray-400 font-mono">{{ $lang->slug }}</span>
                                    @if($lang->slug === $fallback)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 bg-indigo-100 text-indigo-600 rounded-full">DEFAULT</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($langs->count() < 2)
                        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-[11px] text-amber-700">
                            <i class="mdi mdi-alert-outline mr-1"></i>
                            {{ __('Add at least 2 active languages to use multi-lingual content.') }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        </form>
    </div>

    {{-- ── Docs tab ────────────────────────────────────────────────────────── --}}
    <div id="ml_tab_docs" class="ml-tab-pane hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main docs --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Quick start --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-rocket-launch-outline text-green-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('Quick Start') }}</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach([
                            ['mdi-numeric-1-circle','Go to Settings → Languages and activate at least two languages (e.g. English + Arabic).'],
                            ['mdi-numeric-2-circle','Return to this page and enable Translations.'],
                            ['mdi-numeric-3-circle','Open any product, blog post, or page for editing.'],
                            ['mdi-numeric-4-circle','Language tabs appear above each translatable field. Click a tab and enter the translation — or click Auto Translate to fill it with AI.'],
                            ['mdi-numeric-5-circle','Save the form. Translations are stored and served automatically when a visitor switches language.'],
                        ] as [$icon, $text])
                        <div class="flex gap-3">
                            <i class="mdi {{ $icon }} text-indigo-400 text-lg mt-0.5 flex-shrink-0"></i>
                            <p class="text-sm text-gray-600">{{ __($text) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Translatable fields --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-table-of-contents text-blue-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('Translatable Fields') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach([
                            ['Product',       'mdi-package-variant-closed', 'Name, Summary, Description'],
                            ['Category',      'mdi-tag-outline',            'Name, Description'],
                            ['Sub-Category',  'mdi-tag-multiple-outline',   'Name, Description'],
                            ['Child Category','mdi-tag-text-outline',       'Name, Description'],
                            ['Brand',         'mdi-star-circle-outline',    'Name, Description'],
                            ['Blog Post',     'mdi-post-outline',           'Title, Content, Excerpt'],
                            ['Page',          'mdi-file-document-outline',  'Title, Content'],
                        ] as [$label, $icon, $fields])
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i class="mdi {{ $icon }} text-gray-400 text-base"></i>
                                <span class="text-sm font-medium text-dark">{{ __($label) }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $fields }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- AI Translation --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-robot-outline text-violet-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('AI Auto-Translation') }}</h3>
                    </div>
                    <div class="p-5 space-y-4 text-sm text-gray-600">
                        <p>{{ __('Each non-default locale tab shows an Auto Translate button. Clicking it sends the default-locale text to the configured AI provider and fills the translation automatically.') }}</p>
                        <div class="space-y-2">
                            <p class="font-semibold text-dark text-xs">{{ __('Supported providers') }}</p>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach([
                                    ['OpenAI',        'mdi-brain',         'GPT-4o, GPT-5, o3…'],
                                    ['DeepSeek',      'mdi-fish',          'deepseek-chat, v4…'],
                                    ['Google Gemini', 'mdi-google',        '1.5 Flash, 2.5 Pro…'],
                                ] as [$name, $icon, $models])
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-center">
                                    <i class="mdi {{ $icon }} text-xl text-indigo-400 mb-1 block"></i>
                                    <p class="text-xs font-bold text-dark">{{ $name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $models }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-xl text-[12px] text-indigo-700">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            {{ __('If the AI Integration plugin is installed and active, its API key is used automatically — no extra configuration needed here.') }}
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">

                {{-- Performance --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-speedometer text-green-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('Performance') }}</h3>
                    </div>
                    <div class="p-4 space-y-3 text-xs text-gray-600">
                        <div class="flex gap-2">
                            <i class="mdi mdi-check-circle text-green-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <span>{{ __('Default locale visitors: 0 extra queries') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <i class="mdi mdi-check-circle text-green-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <span>{{ __('1 query per model type per request (not per model)') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <i class="mdi mdi-check-circle text-green-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <span>{{ __('10-minute cache layer — repeated requests are free') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <i class="mdi mdi-check-circle text-green-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <span>{{ __('Cache invalidated automatically on content save') }}</span>
                        </div>
                    </div>
                </div>

                {{-- FAQ --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-dark">{{ __('FAQ') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach([
                            ['Does this modify my existing data?', 'No. Translations are stored in a separate table. Your original content is never touched.'],
                            ['What happens if a translation is missing?', 'The default language value is shown as a fallback.'],
                            ['Can I translate HTML/rich-text fields?', 'Yes. The AI translator preserves HTML tags automatically.'],
                            ['Where is the language switcher shown?', 'A floating widget appears on the storefront. Visitors pick their language and the site switches instantly.'],
                        ] as [$q, $a])
                        <div class="px-5 py-3">
                            <p class="text-xs font-semibold text-dark mb-1">{{ __($q) }}</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">{{ __($a) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
