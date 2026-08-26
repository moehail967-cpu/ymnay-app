<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- How it works --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-bell-ring text-primary text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('How It Works')}}</h4>
            </div>
            <div class="space-y-2 text-sm text-secondary">
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">1</span>
                    <span>A small popup widget is injected into your storefront footer. It appears after the configured <strong>Initial Delay</strong> and cycles through purchase notifications.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">2</span>
                    <span>Notifications can come from <strong>Real</strong> orders, <strong>Synthetic</strong> entries you create manually, or a <strong>Mixed</strong> combination of both.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">3</span>
                    <span>In Mixed mode, the plugin falls back to synthetic entries when real orders are below the configured minimum — useful for new stores with little history.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">4</span>
                    <span>Customer names can be <strong>anonymised</strong> ("Sarah F." instead of full name) to satisfy privacy regulations.</span>
                </div>
            </div>
        </div>

        {{-- Data sources --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-database text-green-600 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Data Sources')}}</h4>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-check-circle text-green-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Real Orders</div>
                        <div class="text-xs text-muted mt-0.5">Pulls the most recent completed orders from your store. Set <strong>Recent Orders to Show</strong> to control how many. Best once you have 10+ orders.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-table-edit text-blue-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Synthetic</div>
                        <div class="text-xs text-muted mt-0.5">Uses entries you create manually in the <strong>Synthetic Entries</strong> tab. Good for new stores that don't yet have real order history.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-shuffle text-amber-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Mixed (recommended)</div>
                        <div class="text-xs text-muted mt-0.5">Uses real orders when available, falls back to synthetic when real orders are below <strong>Min Real Orders</strong>. Best for most stores.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick setup --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-rocket-launch text-amber-500 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Quick Setup')}}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Enable the plugin via the <strong>Enable FOMO Notifications</strong> toggle in Settings.</li>
                <li>Choose a <strong>Data Source</strong>. For new stores, select <em>Synthetic</em> or <em>Mixed</em>.</li>
                <li>If using Synthetic or Mixed, go to the <strong>Synthetic Entries</strong> tab and add a few realistic purchase entries.</li>
                <li>Adjust <strong>Initial Delay</strong> (how long before the first popup) and <strong>Interval Between Popups</strong>.</li>
                <li>Use the <strong>Preview</strong> tab to see the widget live before enabling it for customers.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>The widget is injected automatically — no theme changes needed. It appears on the pages you select in <strong>Show On Pages</strong>.</span>
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
                    <div class="font-semibold text-dark mb-0.5">Recent Orders to Show</div>
                    <div class="text-muted">How many completed orders to pull for Real mode. Default: 10.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Min Real Orders (Mixed)</div>
                    <div class="text-muted">Falls back to synthetic if real order count is below this. Default: 3.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Initial Delay (seconds)</div>
                    <div class="text-muted">Time after page load before the first popup appears. Default: 5s.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Display Duration (seconds)</div>
                    <div class="text-muted">How long each popup stays visible. Default: 6s.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Interval Between Popups</div>
                    <div class="text-muted">Pause between each notification. Default: 15s.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Anonymise Customer Names</div>
                    <div class="text-muted">Shows "Sarah F." instead of the full name for privacy.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Show On Pages</div>
                    <div class="text-muted">Restrict notifications to specific pages (Home, Shop, Product, Cart, Checkout).</div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="bg-surface rounded-xl border border-main p-4 space-y-3">
            <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{__('Tips')}}</h4>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Show notifications on the <strong>Product</strong> and <strong>Checkout</strong> pages for the highest conversion impact.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Keep Initial Delay at 5–8 seconds so the popup doesn't appear before the customer has read the page.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-eye text-primary mt-0.5 flex-shrink-0"></i>
                <span>Use the <strong>Preview</strong> tab to verify the popup design and timing before going live.</span>
            </div>
        </div>

    </div>
</div>
