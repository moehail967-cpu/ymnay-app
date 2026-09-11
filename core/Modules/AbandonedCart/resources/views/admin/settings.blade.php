@extends('tenant.admin.admin-master')

@section('title') {{ __('Abandoned Cart — Settings') }} @endsection
@section('page-title') {{ __('Recovery Settings') }} @endsection

@section('content')
<div class="col-12">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-5 text-sm">
        <i class="mdi mdi-check-circle-outline text-base flex-shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Recovery Email Settings') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Configure when and how recovery reminder emails are sent to customers.') }}</p>
        </div>
        <a href="{{ route('tenant.admin.abandoned-cart.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{ __('Back to Carts') }}
        </a>
    </div>

    <form method="POST" action="{{ route('tenant.admin.abandoned-cart.settings.save') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Main settings --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Timing --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-clock-outline text-blue-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('Timing') }}</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Send reminder after (hours)') }}</label>
                            <input type="number"
                                   name="delay_hours"
                                   value="{{ old('delay_hours', $settings['delay_hours']) }}"
                                   min="1"
                                   max="720"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                            <p class="text-[11px] text-gray-400 mt-1">{{ __('How long after cart abandonment to send the first reminder.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Max reminders per cart') }}</label>
                            <input type="number"
                                   name="max_reminders"
                                   value="{{ old('max_reminders', $settings['max_reminders']) }}"
                                   min="1"
                                   max="10"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                            <p class="text-[11px] text-gray-400 mt-1">{{ __('Maximum number of reminder emails per customer. Recommended: 2.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Email content --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-email-edit-outline text-indigo-500 text-base"></i>
                        </div>
                        <h3 class="text-sm font-bold text-dark">{{ __('Email Content') }}</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Sender Name') }}</label>
                            <input type="text"
                                   name="sender_name"
                                   value="{{ old('sender_name', $settings['sender_name']) }}"
                                   placeholder="{{ __('Your Store Name') }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Email Subject') }}</label>
                            <input type="text"
                                   name="email_subject"
                                   value="{{ old('email_subject', $settings['email_subject']) }}"
                                   placeholder="{{ __('You left something behind!') }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">{{ __('Email Body') }}</label>
                            <textarea name="email_body"
                                      rows="10"
                                      class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-dark focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition font-mono resize-y">{{ old('email_body', $settings['email_body']) }}</textarea>
                            {{-- Token reference --}}
                            <div class="mt-2 flex items-start gap-2 bg-blue-50 rounded-lg px-3 py-2 text-xs text-blue-700">
                                <i class="mdi mdi-information-outline mt-0.5 flex-shrink-0"></i>
                                <span>
                                    {{ __('Available tokens:') }}
                                    <code class="font-mono bg-blue-100 rounded px-1">{customer_name}</code>,
                                    <code class="font-mono bg-blue-100 rounded px-1">{cart_total}</code>,
                                    <code class="font-mono bg-blue-100 rounded px-1">{cart_link}</code>,
                                    <code class="font-mono bg-blue-100 rounded px-1">{store_name}</code>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>

            </div>

            {{-- Sidebar help --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-2">
                        <i class="mdi mdi-help-circle-outline text-primary text-base"></i>
                        <h4 class="text-sm font-bold text-dark">{{ __('Token Reference') }}</h4>
                    </div>
                    <div class="divide-y divide-gray-100 text-xs">
                        <div class="px-4 py-3">
                            <code class="font-mono text-indigo-600">{customer_name}</code>
                            <p class="text-gray-500 mt-0.5">{{ __("Customer's name or email if name is unavailable.") }}</p>
                        </div>
                        <div class="px-4 py-3">
                            <code class="font-mono text-indigo-600">{cart_total}</code>
                            <p class="text-gray-500 mt-0.5">{{ __('Formatted cart total with currency symbol.') }}</p>
                        </div>
                        <div class="px-4 py-3">
                            <code class="font-mono text-indigo-600">{cart_link}</code>
                            <p class="text-gray-500 mt-0.5">{{ __('Direct link for the customer to return to their cart.') }}</p>
                        </div>
                        <div class="px-4 py-3">
                            <code class="font-mono text-indigo-600">{store_name}</code>
                            <p class="text-gray-500 mt-0.5">{{ __('Your store name from settings.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl border border-amber-200 p-4 text-xs text-amber-700">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <i class="mdi mdi-alert-outline"></i>
                        {{ __('Cron Job Required') }}
                    </div>
                    <p>{{ __('Reminder emails are sent by the Laravel scheduler. Ensure your cron job is running:') }}</p>
                    <div class="mt-2 bg-gray-900 rounded px-2 py-1.5 font-mono text-green-400 text-[10px] select-all break-all">
                        * * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
