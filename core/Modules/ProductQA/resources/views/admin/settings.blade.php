@extends('tenant.admin.admin-master')

@section('title') {{ __('Q&A Settings') }} @endsection
@section('page-title') {{ __('Q&A Settings') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('tenant.admin.product-qa.index') }}"
           class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Q&A Settings') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Configure how customer questions work in your store.') }}</p>
        </div>
    </div>

    <form action="{{ route('tenant.admin.product-qa.settings.save') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2 space-y-4">

                @php
                    $toggles = [
                        ['key' => 'enabled',       'label' => __('Enable Product Q&A'),         'desc'  => __('Show the Q&A section on product detail pages.'),                       'color' => 'amber'],
                        ['key' => 'require_login', 'label' => __('Require Login to Ask'),        'desc'  => __('Guests must log in before submitting a question.'),                    'color' => 'amber'],
                        ['key' => 'notify_admin',  'label' => __('Notify Admin of New Questions'),'desc' => __('Send an email notification when a new question is submitted.'),        'color' => 'amber'],
                    ];
                @endphp

                @foreach($toggles as $toggle)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ $toggle['label'] }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $toggle['desc'] }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="{{ $toggle['key'] }}" value="0">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ $settings[$toggle['key']] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-amber-400 rounded-full peer peer-checked:bg-amber-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </div>

            <div>
                <div class="bg-amber-50 rounded-xl border border-amber-100 p-5">
                    <h4 class="text-sm font-bold text-amber-800 mb-3">
                        <i class="mdi mdi-lightbulb-outline mr-1"></i>{{ __('How It Works') }}
                    </h4>
                    <ol class="text-xs text-amber-700 space-y-2 list-decimal list-inside">
                        <li>{{ __('Visitor submits a question on a product page.') }}</li>
                        <li>{{ __('Question appears in this dashboard as "pending".') }}</li>
                        <li>{{ __('Admin answers — the answer is published publicly.') }}</li>
                        <li>{{ __('Other visitors can mark answers as helpful.') }}</li>
                    </ol>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
