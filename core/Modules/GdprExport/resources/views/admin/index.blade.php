@extends('tenant.admin.admin-master')

@section('title') {{ __('GDPR Data Export Requests') }} @endsection

@section('content')
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-shield-account-outline text-primary text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{ __('GDPR Data Export Requests') }}</h3>
                <p class="text-xs text-muted">{{ __('Customer data download requests submitted from the storefront') }}</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary border border-main text-xs font-semibold text-muted">
            {{ $requests->total() }} {{ __('total') }}
        </span>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-check-circle-outline"></i>
        {{ session('success') }}
    </div>
@endif

<div class="bg-surface rounded-xl border border-main shadow-main overflow-hidden">
    @if($requests->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <i class="mdi mdi-shield-account-outline text-5xl text-subtle mb-3"></i>
            <p class="text-sm font-semibold text-dark mb-1">{{ __('No requests yet') }}</p>
            <p class="text-xs text-muted">{{ __('Customer data export requests will appear here.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-secondary border-b border-main text-xs font-semibold text-muted uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">{{ __('User') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Requested') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Ready At') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Expires') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-main">
                    @foreach($requests as $req)
                    @php
                        $badgeMap = [
                            'pending'    => 'bg-yellow-50 text-yellow-700',
                            'processing' => 'bg-blue-50 text-blue-700',
                            'ready'      => 'bg-green-50 text-green-700',
                            'downloaded' => 'bg-gray-100 text-gray-500',
                            'expired'    => 'bg-red-50 text-red-600',
                        ];
                        $badge = $badgeMap[$req->status] ?? 'bg-gray-100 text-gray-500';
                    @endphp
                    <tr class="hover:bg-secondary/50 transition-colors" id="row-{{ $req->id }}">
                        <td class="px-5 py-3 text-muted font-mono text-xs">{{ $req->id }}</td>
                        <td class="px-5 py-3 font-medium text-dark">{{ $req->user_id }}</td>
                        <td class="px-5 py-3">
                            <span class="status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-muted text-xs">
                            {{ \Carbon\Carbon::parse($req->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-3 text-muted text-xs ready-at-cell">
                            {{ $req->ready_at ? \Carbon\Carbon::parse($req->ready_at)->format('d M Y, H:i') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs">
                            @if($req->expires_at)
                                @php $isExpired = now()->isAfter($req->expires_at); @endphp
                                <span class="{{ $isExpired ? 'text-red-500 font-medium' : 'text-muted' }}">
                                    {{ \Carbon\Carbon::parse($req->expires_at)->format('d M Y, H:i') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <select class="gdpr-status-select text-xs border border-main rounded-lg px-2 py-1.5 bg-surface text-dark focus:outline-none focus:border-primary transition-colors"
                                        data-id="{{ $req->id }}"
                                        data-url="{{ route('tenant.admin.gdpr-export.updateStatus', $req->id) }}">
                                    @foreach(['pending','processing','ready','downloaded','expired'] as $s)
                                        <option value="{{ $s }}" {{ $req->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="gdpr-save-btn hidden text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors"
                                        data-id="{{ $req->id }}">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="px-5 py-4 border-t border-main">
            {{ $requests->links() }}
        </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    var badgeMap = {
        pending:    'bg-yellow-50 text-yellow-700',
        processing: 'bg-blue-50 text-blue-700',
        ready:      'bg-green-50 text-green-700',
        downloaded: 'bg-gray-100 text-gray-500',
        expired:    'bg-red-50 text-red-600',
    };

    document.querySelectorAll('.gdpr-status-select').forEach(function (sel) {
        var original = sel.value;

        sel.addEventListener('change', function () {
            var btn = document.querySelector('.gdpr-save-btn[data-id="' + sel.dataset.id + '"]');
            if (sel.value !== original) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        });
    });

    document.querySelectorAll('.gdpr-save-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id  = btn.dataset.id;
            var sel = document.querySelector('.gdpr-status-select[data-id="' + id + '"]');
            var url = sel.dataset.url;
            var newStatus = sel.value;

            btn.disabled = true;
            btn.textContent = '{{ __("Saving…") }}';

            fetch(url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ status: newStatus }),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    var row    = document.getElementById('row-' + id);
                    var badge  = row.querySelector('.status-badge');

                    // Update badge classes
                    badge.className = 'status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold ' + (badgeMap[newStatus] || 'bg-gray-100 text-gray-500');
                    badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

                    // If marked ready, update ready_at cell
                    if (newStatus === 'ready') {
                        var readyCell = row.querySelector('.ready-at-cell');
                        if (readyCell) {
                            var now = new Date();
                            readyCell.textContent = now.toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }).replace(',', '');
                        }
                    }

                    btn.classList.add('hidden');
                    btn.disabled = false;
                    btn.textContent = '{{ __("Save") }}';
                    toastr.success(data.message);
                } else {
                    btn.disabled = false;
                    btn.textContent = '{{ __("Save") }}';
                    toastr.error(data.message);
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = '{{ __("Save") }}';
                toastr.error('{{ __("Something went wrong.") }}');
            });
        });
    });
})();
</script>
@endsection
