@extends('tenant.admin.admin-master')

@section('title') {{ __('Campaign Suggestions') }} @endsection
@section('page-title') {{ __('Campaign Suggestions') }} @endsection

@section('section')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Campaign Suggestions') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Slow-moving products with high stock — consider running a campaign to clear inventory.') }}</p>
        </div>
        <a href="{{ route('tenant.admin.campaign.all') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{ __('Back to Campaigns') }}
        </a>
    </div>

    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-5">
        <div class="flex items-start gap-3">
            <i class="mdi mdi-lightbulb-outline text-amber-500 text-xl flex-shrink-0 mt-0.5"></i>
            <div class="text-sm text-amber-800">
                {{ __('These products have had no sales in the last 60 days and have significant stock. Creating a limited-time campaign can help clear inventory and drive engagement.') }}
                <span class="block mt-1 text-xs text-amber-600">{{ __('Run') }} <code class="bg-amber-100 px-1 rounded">php artisan campaigns:suggest</code> {{ __('to refresh this list.') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-tag-plus-outline text-orange-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('Suggested Products') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Product') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Stock') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('No Sales (days)') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($suggestions as $suggestion)
                    @php
                        $badge = match($suggestion->status) {
                            'acted'     => 'text-green-700 bg-green-100',
                            'dismissed' => 'text-gray-500 bg-gray-100',
                            default     => 'text-amber-700 bg-amber-100',
                        };
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" id="sug-{{ $suggestion->id }}">
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{ $suggestion->product_name }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-bold">
                                <i class="mdi mdi-package-variant text-sm"></i>
                                {{ number_format($suggestion->stock_count) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-gray-600">{{ $suggestion->days_window }}+ {{ __('days') }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($suggestion->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tenant.admin.campaign.new') }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-700 text-xs font-semibold hover:bg-orange-100 transition sug-act-btn"
                                   data-id="{{ $suggestion->id }}">
                                    <i class="mdi mdi-rocket-launch-outline text-sm"></i>
                                    {{ __('Create Campaign') }}
                                </a>
                                @if($suggestion->status === 'pending')
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition sug-dismiss-btn"
                                        data-id="{{ $suggestion->id }}">
                                    <i class="mdi mdi-close text-sm"></i>
                                    {{ __('Dismiss') }}
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                            <i class="mdi mdi-check-circle-outline text-3xl block mb-2 text-gray-300"></i>
                            {{ __('No suggestions yet. Run') }} <code class="bg-gray-100 px-1.5 rounded">php artisan campaigns:suggest</code> {{ __('to generate them.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($suggestions instanceof \Illuminate\Pagination\LengthAwarePaginator && $suggestions->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $suggestions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
var BASE_URL = '{{ url(route_prefix("admin") . "/campaigns/suggestions/") }}';
var CSRF     = '{{ csrf_token() }}';

document.querySelectorAll('.sug-dismiss-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var id = this.dataset.id;
        fetch(BASE_URL + '/' + id + '/dismiss', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'},
            body: JSON.stringify({})
        }).then(function () {
            var row = document.getElementById('sug-' + id);
            if (row) row.style.opacity = '0.4';
        });
    });
});

document.querySelectorAll('.sug-act-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var id = this.dataset.id;
        fetch(BASE_URL + '/' + id + '/act', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json'},
            body: JSON.stringify({})
        });
    });
});
</script>
@endsection
