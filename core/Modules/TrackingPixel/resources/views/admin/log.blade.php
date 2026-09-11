@extends('tenant.admin.admin-master')

@section('title') {{ __('Tracking Pixels — Event Log') }} @endsection

@section('content')
<div class="space-y-5">

    {{-- Test buttons --}}
    <div class="bg-surface rounded-xl shadow-main border border-main">
        <div class="px-5 py-4 border-b border-main flex items-center gap-3">
            <span class="text-sm font-bold text-dark">{{ __('Send Test Event') }}</span>
            <span class="text-xs text-muted">{{ __('Fires a synthetic Purchase event to verify credentials.') }}</span>
        </div>
        <div class="px-5 py-4 flex flex-wrap gap-3 items-center">
            @foreach(['ga4' => 'Google Analytics 4', 'meta' => 'Meta (Facebook)', 'tiktok' => 'TikTok'] as $ch => $label)
            <button class="tp-test-btn inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-lg border border-main text-muted bg-surface hover:text-dark hover:border-primary/40 transition-colors"
                    data-channel="{{ $ch }}">
                <i class="mdi mdi-send text-sm"></i>
                {{ $label }}
            </button>
            @endforeach
            <span id="tp-test-result" class="text-xs"></span>
        </div>
    </div>

    {{-- Event log table --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <span class="text-sm font-bold text-dark">{{ __('Last 50 Events') }}</span>
            <span class="text-xs text-muted">{{ __('Auto-pruned at 100 entries per tenant.') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-secondary border-b border-main text-xs font-semibold text-muted uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">{{ __('Time') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Channel') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Event') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Order') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Response') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-main">
                    @forelse($events as $event)
                    <tr class="hover:bg-secondary/50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap text-muted text-xs">
                            {{ $event->fired_at?->format('M d, H:i:s') }}
                        </td>
                        <td class="px-5 py-3">
                            @php $icons = ['ga4'=>'mdi-google-analytics','meta'=>'mdi-facebook','tiktok'=>'mdi-music-note']; @endphp
                            <span class="inline-flex items-center gap-1.5 text-dark text-xs font-medium">
                                <i class="mdi {{ $icons[$event->channel] ?? 'mdi-broadcast' }} text-base text-primary"></i>
                                {{ strtoupper($event->channel) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-dark text-xs">{{ $event->event_type }}</td>
                        <td class="px-5 py-3 text-muted text-xs">{{ $event->order_id ?: '—' }}</td>
                        <td class="px-5 py-3">
                            @php
                                $s = $event->http_status;
                                $badge = $s >= 200 && $s < 300
                                    ? 'bg-green-50 text-green-700'
                                    : ($s ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500');
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                {{ $s ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <code class="text-xs text-muted font-mono" title="{{ $event->response_snippet }}">
                                {{ \Illuminate\Support\Str::limit($event->response_snippet ?? '', 60) }}
                            </code>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-muted text-sm">
                            {{ __('No events logged yet.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.tp-test-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var channel = this.dataset.channel;
        var result  = document.getElementById('tp-test-result');
        result.textContent = '{{ __("Sending…") }}';
        result.className = 'text-xs text-muted';

        fetch('{{ route('tenant.admin.tracking-pixel.test') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ channel: channel })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.error) {
                result.textContent = '{{ __("Error") }}: ' + data.error;
                result.className = 'text-xs text-red-600 font-semibold';
            } else {
                result.textContent = channel.toUpperCase() + ' → HTTP ' + data.status;
                result.className = 'text-xs font-semibold ' + (data.status >= 200 && data.status < 300
                    ? 'text-green-600' : 'text-red-600');
            }
        })
        .catch(function () {
            result.textContent = '{{ __("Request failed") }}';
            result.className = 'text-xs text-red-600 font-semibold';
        });
    });
});
</script>
@endsection
