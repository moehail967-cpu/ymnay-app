@extends('tenant.admin.admin-master')

@section('title') {{ __('Tracking Pixels') }} @endsection
@section('page-title') {{ __('Tracking Pixels') }} @endsection

@section('content')
<div class="col-12">

    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded',function(){ toastr.success('{{ session('success') }}'); });</script>
    @endif

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Tracking Pixels') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Configure GA4, GTM, Meta, and TikTok tracking for your storefront.') }}</p>
        </div>
        <a href="{{ route('tenant.admin.tracking-pixel.log') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <i class="mdi mdi-history text-base"></i>
            {{ __('Event Log') }}
        </a>
    </div>

    <form method="POST" action="{{ route('tenant.admin.tracking-pixel.settings.save') }}">
        @csrf

        {{-- ── Google Analytics 4 ───────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-4" x-data="{ open: true }">
            <button type="button"
                    class="w-full flex items-center gap-3 px-5 py-4 text-left"
                    @click="open = !open">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-google-analytics text-blue-500 text-lg"></i>
                </div>
                <span class="text-sm font-bold text-dark flex-1">{{ __('Google Analytics 4') }}</span>
                <i class="mdi text-gray-400 text-lg" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </button>
            <div x-show="open" x-transition class="px-5 pb-5 border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Measurement ID') }}</label>
                    <input type="text" name="ga4_measurement_id"
                           value="{{ $options['ga4_measurement_id'] ?? '' }}"
                           placeholder="{{ __('G-XXXXXXXXXX') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    <p class="text-xs text-gray-400 mt-1">{{ __('From GA4 → Admin → Data Streams') }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('API Secret (server-side)') }}</label>
                    <input type="text" name="ga4_server_api_secret"
                           value="{{ $options['ga4_server_api_secret'] ?? '' }}"
                           placeholder="{{ __('From GA4 → Measurement Protocol') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div class="flex items-center justify-between py-1">
                    <div>
                        <div class="text-sm font-medium text-dark">{{ __('Enable GA4 (client-side)') }}</div>
                        <div class="text-xs text-gray-400">{{ __('Inject gtag.js into every page') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="ga4_enabled" value="0">
                        <input type="checkbox" name="ga4_enabled" value="1" class="sr-only peer"
                               {{ ($options['ga4_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between py-1">
                    <div>
                        <div class="text-sm font-medium text-dark">{{ __('Enable Measurement Protocol') }}</div>
                        <div class="text-xs text-gray-400">{{ __('Server-side conversions via API') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="ga4_server_enabled" value="0">
                        <input type="checkbox" name="ga4_server_enabled" value="1" class="sr-only peer"
                               {{ ($options['ga4_server_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Google Tag Manager ───────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-4" x-data="{ open: false }">
            <button type="button"
                    class="w-full flex items-center gap-3 px-5 py-4 text-left"
                    @click="open = !open">
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-tag-outline text-yellow-500 text-lg"></i>
                </div>
                <span class="text-sm font-bold text-dark flex-1">{{ __('Google Tag Manager') }}</span>
                <i class="mdi text-gray-400 text-lg" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </button>
            <div x-show="open" x-transition class="px-5 pb-5 border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Container ID') }}</label>
                    <input type="text" name="gtm_container_id"
                           value="{{ $options['gtm_container_id'] ?? '' }}"
                           placeholder="{{ __('GTM-XXXXXXX') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div class="flex items-center justify-between py-1">
                    <div>
                        <div class="text-sm font-medium text-dark">{{ __('Enable GTM') }}</div>
                        <div class="text-xs text-gray-400">{{ __('Inject container into head + body') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="gtm_enabled" value="0">
                        <input type="checkbox" name="gtm_enabled" value="1" class="sr-only peer"
                               {{ ($options['gtm_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Meta (Facebook) ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-4" x-data="{ open: false }">
            <button type="button"
                    class="w-full flex items-center gap-3 px-5 py-4 text-left"
                    @click="open = !open">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-facebook text-lg" style="color:#1877f2"></i>
                </div>
                <span class="text-sm font-bold text-dark flex-1">{{ __('Meta (Facebook)') }}</span>
                <i class="mdi text-gray-400 text-lg" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </button>
            <div x-show="open" x-transition class="px-5 pb-5 border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Pixel ID') }}</label>
                    <input type="text" name="meta_pixel_id"
                           value="{{ $options['meta_pixel_id'] ?? '' }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Conversions API Access Token') }}</label>
                    <input type="text" name="meta_access_token"
                           value="{{ $options['meta_access_token'] ?? '' }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">
                        {{ __('Test Event Code') }}
                        <span class="text-gray-400 font-normal normal-case ml-1">{{ __('(optional — clear in production)') }}</span>
                    </label>
                    <input type="text" name="meta_test_event_code"
                           value="{{ $options['meta_test_event_code'] ?? '' }}"
                           placeholder="{{ __('TEST12345') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1">
                        <div>
                            <div class="text-sm font-medium text-dark">{{ __('Enable Meta Pixel') }}</div>
                            <div class="text-xs text-gray-400">{{ __('Client-side pixel injection') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="meta_pixel_enabled" value="0">
                            <input type="checkbox" name="meta_pixel_enabled" value="1" class="sr-only peer"
                                   {{ ($options['meta_pixel_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <div>
                            <div class="text-sm font-medium text-dark">{{ __('Enable Conversions API') }}</div>
                            <div class="text-xs text-gray-400">{{ __('Server-side with hashed PII') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="meta_server_enabled" value="0">
                            <input type="checkbox" name="meta_server_enabled" value="1" class="sr-only peer"
                                   {{ ($options['meta_server_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TikTok ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-5" x-data="{ open: false }">
            <button type="button"
                    class="w-full flex items-center gap-3 px-5 py-4 text-left"
                    @click="open = !open">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-music-note text-gray-700 text-lg"></i>
                </div>
                <span class="text-sm font-bold text-dark flex-1">{{ __('TikTok') }}</span>
                <i class="mdi text-gray-400 text-lg" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </button>
            <div x-show="open" x-transition class="px-5 pb-5 border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Pixel ID') }}</label>
                    <input type="text" name="tiktok_pixel_id"
                           value="{{ $options['tiktok_pixel_id'] ?? '' }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">{{ __('Events API Access Token') }}</label>
                    <input type="text" name="tiktok_access_token"
                           value="{{ $options['tiktok_access_token'] ?? '' }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div class="flex items-center justify-between py-1">
                    <div>
                        <div class="text-sm font-medium text-dark">{{ __('Enable TikTok Pixel') }}</div>
                        <div class="text-xs text-gray-400">{{ __('Client-side pixel injection') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="tiktok_pixel_enabled" value="0">
                        <input type="checkbox" name="tiktok_pixel_enabled" value="1" class="sr-only peer"
                               {{ ($options['tiktok_pixel_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between py-1">
                    <div>
                        <div class="text-sm font-medium text-dark">{{ __('Enable Events API') }}</div>
                        <div class="text-xs text-gray-400">{{ __('Server-side with hashed email') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="tiktok_server_enabled" value="0">
                        <input type="checkbox" name="tiktok_server_enabled" value="1" class="sr-only peer"
                               {{ ($options['tiktok_server_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                    </label>
                </div>
            </div>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
            <i class="mdi mdi-content-save-outline text-base"></i>
            {{ __('Save Settings') }}
        </button>
    </form>
</div>
@endsection
