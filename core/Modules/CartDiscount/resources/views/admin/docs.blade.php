<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- How it works --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-percent text-primary text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('How It Works')}}</h4>
            </div>
            <div class="space-y-2 text-sm text-secondary">
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">1</span>
                    <span>You define <strong>discount tiers</strong> — each tier has a cart total threshold, a discount type, and a value.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">2</span>
                    <span>A <strong>progress widget</strong> is injected into the storefront footer, showing customers how close they are to the next reward.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">3</span>
                    <span>When a customer's cart total reaches a threshold, the discount is <strong>applied automatically</strong> at checkout via the cart total filter.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">4</span>
                    <span>With <strong>Stack discounts</strong> on, all unlocked percentage tiers are added together instead of using only the highest.</span>
                </div>
            </div>
        </div>

        {{-- Discount types --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-tag-multiple text-green-600 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Discount Types')}}</h4>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-truck-fast text-blue-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Free Shipping</div>
                        <div class="text-xs text-muted mt-0.5">Removes the shipping cost from the cart total when the threshold is reached. Set Value to 0.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-percent text-primary text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Percentage</div>
                        <div class="text-xs text-muted mt-0.5">Reduces the cart total by a percentage. E.g. Value = 10 gives a 10% discount.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-secondary rounded-lg">
                    <i class="mdi mdi-currency-usd text-amber-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-sm font-semibold text-dark">Flat Amount</div>
                        <div class="text-xs text-muted mt-0.5">Deducts a fixed amount from the cart total. E.g. Value = 15 removes $15 from the total.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Setup steps --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-rocket-launch text-amber-500 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Quick Setup')}}</h4>
            </div>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Go to the <strong>Settings tab</strong> and toggle <strong>Enable Cart Discounts</strong> on.</li>
                <li>In <strong>Discount Tiers</strong>, add your thresholds — e.g. $50 → Free Shipping, $100 → 10% off, $200 → 20% off.</li>
                <li>Set a <strong>Widget Heading</strong> (e.g. "Unlock Rewards") and pick your <strong>Progress Bar Color</strong>.</li>
                <li>Enable <strong>Show Locked Tiers</strong> to let customers see upcoming rewards in a dimmed state — this encourages them to spend more.</li>
                <li>Save and visit your storefront cart page to confirm the widget appears.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>The widget is injected into the storefront footer automatically. No theme file edits are required.</span>
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
                    <div class="font-semibold text-dark mb-0.5">Discount Tiers</div>
                    <div class="text-muted">Repeatable rows. Each row: threshold (cart total), type, value, and display label.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Widget Heading</div>
                    <div class="text-muted">Title shown above the progress bar in the cart widget.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Progress Bar Color</div>
                    <div class="text-muted">Accent color for the progress bar and unlocked tier badges.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Show Locked Tiers</div>
                    <div class="text-muted">Displays upcoming tiers in a dimmed state to motivate customers to spend more.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Exclude Shipping from Threshold</div>
                    <div class="text-muted">When on, shipping cost is not counted toward the discount threshold.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Stack All Unlocked Discounts</div>
                    <div class="text-muted">Adds all unlocked percentage tiers instead of applying only the highest one.</div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="bg-surface rounded-xl border border-main p-4 space-y-3">
            <h4 class="text-xs font-bold text-muted uppercase tracking-wide">{{__('Tips')}}</h4>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Start with a free shipping tier at a low threshold — it's the single most effective incentive to increase average order value.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Keep tier labels short and action-oriented: "Free Shipping", "10% Off", "20% Off".</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-secondary">
                <i class="mdi mdi-lightbulb text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>The cart total filter runs at priority 20 — it applies after any coupon discounts so tiers are calculated on the post-coupon total.</span>
            </div>
        </div>

    </div>
</div>
