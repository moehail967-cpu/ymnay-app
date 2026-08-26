<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- How it works --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-rss text-primary text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('How It Works')}}</h4>
            </div>
            <div class="space-y-2 text-sm text-secondary">
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">1</span>
                    <span>The plugin generates <strong>XML product feeds</strong> for Google Merchant Center and Facebook/Meta Product Catalog from your live product catalogue.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">2</span>
                    <span>Each feed has a unique <strong>token-protected public URL</strong> — only people with the URL can access it. The token is generated automatically on activation.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">3</span>
                    <span>You submit the feed URL to Google or Facebook and they <strong>crawl it periodically</strong> to keep their product listings in sync with your store.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">4</span>
                    <span>The feed is <strong>regenerated daily</strong> by the scheduler and invalidated immediately whenever a product is saved.</span>
                </div>
            </div>
        </div>

        {{-- Google Merchant setup --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-google text-blue-600 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Google Merchant Center Setup')}}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Enable <strong>Google Merchant Feed</strong> in the Settings tab and configure shipping country, price, brand, and condition.</li>
                <li>Go to the <strong>Feed URLs</strong> tab and copy the <strong>Google Merchant feed URL</strong>.</li>
                <li>Open <a href="https://merchants.google.com" target="_blank" rel="noopener" class="text-primary hover:underline">merchants.google.com</a> and log in to your Merchant Center account.</li>
                <li>Go to <strong>Products → Feeds → Add Primary Feed</strong>.</li>
                <li>Choose <strong>Scheduled Fetch</strong> as the input method and paste your feed URL.</li>
                <li>Set the fetch frequency to <strong>Daily</strong> and save.</li>
                <li>Google will crawl the feed and surface any disapprovals in the Diagnostics tab.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-amber-50 rounded-lg px-3 py-2 text-xs text-amber-700">
                <i class="mdi mdi-alert-outline mt-0.5 flex-shrink-0"></i>
                <span>Google requires a valid <strong>GTIN, MPN, or Brand</strong> on each product. Set the Brand Source in Settings to ensure all products have this field populated.</span>
            </div>
        </div>

        {{-- Facebook Catalog setup --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e7f0fd">
                    <i class="mdi mdi-facebook text-lg" style="color:#1877f2"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Facebook / Meta Product Catalog Setup')}}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Enable <strong>Facebook Catalog Feed</strong> in the Settings tab and configure availability and sale price options.</li>
                <li>Go to the <strong>Feed URLs</strong> tab and copy the <strong>Facebook Catalog feed URL</strong>.</li>
                <li>Open <a href="https://business.facebook.com/commerce" target="_blank" rel="noopener" class="text-primary hover:underline">Facebook Commerce Manager</a> and select or create a Catalog.</li>
                <li>Go to <strong>Data Sources → Add Items → Use a Data Feed → Scheduled Feed</strong>.</li>
                <li>Paste your feed URL and set the schedule to <strong>Daily</strong>.</li>
                <li>Facebook will validate the feed — check the Data Sources tab for any errors.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>Once your catalog is connected, link it to your Meta Pixel in Events Manager to enable Dynamic Product Ads.</span>
            </div>
        </div>

    </div>

    <div class="space-y-4">

        {{-- Settings reference --}}
        <div class="bg-surface rounded-xl border border-main overflow-hidden">
            <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                <i class="mdi mdi-cog-outline text-primary"></i>
                <h4 class="text-sm font-bold text-dark">{{__('Settings Reference')}}</h4>
            </div>
            <div class="divide-y divide-main text-xs">
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Shipping Country</div>
                    <div class="text-muted">ISO country code for the shipping origin (e.g. US, GB, DE). Used in the Google feed's shipping attribute.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Shipping Price</div>
                    <div class="text-muted">Flat shipping cost included in the feed. Use 0 for free shipping.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Brand Source</div>
                    <div class="text-muted">Where to read the brand: product brand field, store name, or a custom value you type.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Product Condition</div>
                    <div class="text-muted">New, Used, or Refurbished. Applied globally to all products in the feed.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Include Sale Price</div>
                    <div class="text-muted">When on, both the regular and sale price are included in the Facebook feed.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Default Availability</div>
                    <div class="text-muted">Stock status sent to Facebook: In Stock, Preorder, or Available for Order.</div>
                </div>
            </div>
        </div>

        {{-- Cron requirement --}}
        <div class="bg-surface rounded-xl border border-main p-4 space-y-3">
            <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{__('Cron Requirement')}}</h4>
            <p class="text-xs text-secondary">Feeds regenerate daily via the Laravel scheduler. Make sure your server cron is configured:</p>
            <div class="bg-gray-900 rounded-lg px-3 py-2 font-mono text-xs text-green-400 select-all break-all">
                * * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1
            </div>
        </div>

        {{-- Tips --}}
        <div class="bg-surface rounded-xl border border-main p-4 space-y-3">
            <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{__('Tips')}}</h4>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Use the <strong>Regenerate</strong> button in Feed URLs after changing settings to immediately update the cached feed.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>The feed token in the URL keeps it private. If you suspect the URL was shared unintentionally, deactivate and reactivate the plugin to generate a new token.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-shield-check text-green-500 mt-0.5 flex-shrink-0"></i>
                <span>Products saved in the admin automatically invalidate the feed cache so Google and Facebook always get fresh data on their next fetch.</span>
            </div>
        </div>

    </div>
</div>
