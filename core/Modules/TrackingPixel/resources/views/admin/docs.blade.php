<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: per-platform setup cards --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- GA4 --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-google-analytics text-primary text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{ __('Google Analytics 4') }}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Open <strong>analytics.google.com</strong> and select your property.</li>
                <li>Go to <strong>Admin → Data Streams</strong> and click your web stream.</li>
                <li>Copy the <strong>Measurement ID</strong> — starts with <code class="bg-secondary px-1 rounded text-xs">G-</code>.</li>
                <li>For server-side: scroll to <strong>Measurement Protocol API secrets → Create</strong> and copy the secret value.</li>
                <li>Paste both into Settings and enable the toggles.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>If you load GA4 through Google Tag Manager, leave the Measurement ID blank here and use GTM instead.</span>
            </div>
        </div>

        {{-- GTM --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-tag-outline text-amber-500 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{ __('Google Tag Manager') }}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Open <strong>tagmanager.google.com</strong> and select your account.</li>
                <li>Find the <strong>Container ID</strong> — starts with <code class="bg-secondary px-1 rounded text-xs">GTM-</code>.</li>
                <li>Paste it into Settings and enable the GTM toggle.</li>
                <li>The plugin injects the GTM script into <code class="bg-secondary px-1 rounded text-xs">&lt;head&gt;</code> and the noscript fallback after <code class="bg-secondary px-1 rounded text-xs">&lt;body&gt;</code> automatically.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-amber-50 rounded-lg px-3 py-2 text-xs text-amber-700">
                <i class="mdi mdi-alert-outline mt-0.5 flex-shrink-0"></i>
                <span>If you fire GA4 through GTM, disable the GA4 client-side toggle to avoid loading the gtag snippet twice.</span>
            </div>
        </div>

        {{-- Meta --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e7f0fd">
                    <i class="mdi mdi-facebook text-lg" style="color:#1877f2"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{ __('Meta (Facebook) Pixel') }}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Go to <strong>business.facebook.com → Events Manager</strong>.</li>
                <li>Select your pixel and copy the <strong>Pixel ID</strong> shown at the top.</li>
                <li>For Conversions API: click <strong>Settings → Generate Access Token</strong> and copy the token.</li>
                <li>Paste both into Settings and enable the toggles.</li>
                <li>Use the <strong>Test Event Code</strong> (from Events Manager → Test Events tab) only while testing — remove it before going live.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>Enable both client-side pixel and Conversions API together for the highest match rate and iOS 14+ resilience.</span>
            </div>
        </div>

        {{-- TikTok --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-music-note text-dark text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{ __('TikTok Pixel') }}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Open <strong>ads.tiktok.com → Assets → Events → Web Events</strong>.</li>
                <li>Create or select a pixel and copy the <strong>Pixel ID</strong>.</li>
                <li>For Events API: click <strong>Settings → Generate Access Token</strong> and copy it.</li>
                <li>Paste both into Settings and enable the toggles.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>Enable both client-side pixel and Events API for better ad attribution on TikTok campaigns.</span>
            </div>
        </div>

    </div>

    {{-- Right column: events table + sidebar info --}}
    <div class="space-y-4">

        {{-- Events table --}}
        <div class="bg-surface rounded-xl border border-main overflow-hidden">
            <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                <i class="mdi mdi-lightning-bolt text-primary"></i>
                <h4 class="text-sm font-bold text-dark">{{__('Events Tracked')}}</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-secondary border-b border-main text-left">
                            <th class="px-3 py-2 font-semibold text-muted">{{__('Event')}}</th>
                            <th class="px-3 py-2 font-semibold text-muted text-center">GA4</th>
                            <th class="px-3 py-2 font-semibold text-muted text-center">Meta</th>
                            <th class="px-3 py-2 font-semibold text-muted text-center">TikTok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-main">
                        <tr>
                            <td class="px-3 py-2.5 text-dark">PageView</td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 text-dark">ViewContent</td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 text-dark">AddToCart</td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2.5 text-dark">Purchase <span class="text-muted">(server)</span></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                            <td class="px-3 py-2.5 text-center"><i class="mdi mdi-check-circle text-green-500"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick tips --}}
        <div class="bg-surface rounded-xl border border-main p-4 space-y-3">
            <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{__('Tips')}}</h4>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-shield-check text-green-500 mt-0.5 flex-shrink-0"></i>
                <span>Server-side events (Conversions API / Events API) bypass ad blockers for more accurate data.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-test-tube text-amber-500 mt-0.5 flex-shrink-0"></i>
                <span>Use the Event Log tab to confirm events are being received after setup.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-eye-off text-red-400 mt-0.5 flex-shrink-0"></i>
                <span>Remove the Meta Test Event Code before going live in production.</span>
            </div>
        </div>

    </div>
</div>
