<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-4">

        {{-- How it works --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cart-remove text-primary text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('How It Works')}}</h4>
            </div>
            <div class="space-y-2 text-sm text-secondary">
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">1</span>
                    <span>When a <strong>logged-in customer</strong> adds an item to their cart, the plugin records the cart contents and total in the database.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">2</span>
                    <span>If the customer leaves without placing an order, the cart is flagged as <strong>abandoned</strong> after the configured delay.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">3</span>
                    <span>The daily scheduler sends a <strong>recovery email</strong> to the customer up to the configured maximum number of reminders.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">4</span>
                    <span>When the customer completes an order, the cart is automatically marked as <strong>converted</strong>.</span>
                </div>
            </div>
        </div>

        {{-- Email / SMTP setup --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-email-outline text-blue-500 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Email / SMTP Setup')}}</h4>
            </div>
            <p class="text-sm text-secondary mb-3">Recovery emails are sent through your store's default mail driver. Make sure SMTP is configured before activating this plugin.</p>
            <ol class="space-y-2 text-sm text-secondary ps-4 list-decimal marker:text-muted">
                <li>Go to <strong>Admin → Settings → Email Settings</strong> and configure your SMTP credentials.</li>
                <li>Send a test email to verify delivery.</li>
                <li>Return here and set your <strong>Sender Name</strong> and <strong>Email Subject</strong> in the Settings tab.</li>
                <li>Set <strong>Send reminder after (hours)</strong> — 1–2 hours is typically optimal for recovery rates.</li>
                <li>Set <strong>Max reminders</strong> — 2 is recommended to avoid appearing as spam.</li>
            </ol>
            <div class="mt-3 flex items-start gap-2 bg-amber-50 rounded-lg px-3 py-2 text-xs text-amber-700">
                <i class="mdi mdi-alert-outline mt-0.5 flex-shrink-0"></i>
                <span>Recovery emails are only sent to <strong>logged-in customers</strong>. Guest carts are tracked but no email is sent without an account email address.</span>
            </div>
        </div>

        {{-- Scheduler requirement --}}
        <div class="bg-surface rounded-xl border border-main p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-clock-outline text-green-500 text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-dark">{{__('Cron Job Requirement')}}</h4>
            </div>
            <p class="text-sm text-secondary mb-3">The reminder emails are dispatched by Laravel's task scheduler. You must have a cron job running on your server.</p>
            <p class="text-xs font-semibold text-muted mb-1">{{__('Add this cron entry to your server (cPanel → Cron Jobs):')}}
</p>
            <div class="bg-gray-900 rounded-lg px-3 py-2 font-mono text-xs text-green-400 select-all">
                * * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1
            </div>
            <div class="mt-3 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                <span>The scheduler runs every minute but emails are only sent once per day per the configured delay, so it won't flood your customers.</span>
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
                    <div class="font-semibold text-dark mb-0.5">Send reminder after (hours)</div>
                    <div class="text-muted">How long after cart abandonment to send the first email. Default: 2 hours.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Max reminders per cart</div>
                    <div class="text-muted">Maximum emails sent per abandoned cart. Recommended: 2.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Sender name</div>
                    <div class="text-muted">The "From" name displayed in the customer's inbox. Use your store name.</div>
                </div>
                <div class="px-4 py-3">
                    <div class="font-semibold text-dark mb-0.5">Email subject</div>
                    <div class="text-muted">Subject line of the recovery email. Keep it short and action-oriented.</div>
                </div>
            </div>
        </div>

        {{-- Cart statuses --}}
        <div class="bg-surface rounded-xl border border-main overflow-hidden">
            <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                <i class="mdi mdi-information-outline text-primary"></i>
                <h4 class="text-sm font-bold text-dark">{{__('Cart Statuses')}}</h4>
            </div>
            <div class="divide-y divide-main text-xs">
                <div class="px-4 py-2.5 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">abandoned</span>
                    <span class="text-muted">Cart tracked, no order placed yet</span>
                </div>
                <div class="px-4 py-2.5 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">reminded</span>
                    <span class="text-muted">Max reminders sent, still no order</span>
                </div>
                <div class="px-4 py-2.5 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">converted</span>
                    <span class="text-muted">Order placed — recovery successful</span>
                </div>
                <div class="px-4 py-2.5 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">expired</span>
                    <span class="text-muted">Cart was manually cleared</span>
                </div>
            </div>
        </div>

    </div>
</div>
