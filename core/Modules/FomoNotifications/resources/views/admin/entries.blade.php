@extends('tenant.admin.admin-master')

@section('title') {{ __('FOMO — Synthetic Entries') }} @endsection

@section('content')

{{-- Page header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-format-list-bulleted text-primary text-lg"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-dark font-urbanist">{{ __('Synthetic Entries') }}</h3>
            <p class="text-xs text-muted">{{ __('Custom purchase notifications shown when real order data is not used') }}</p>
        </div>
        <a href="{{ route('tenant.admin.fomo-notifications.settings') }}"
           class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
            <i class="mdi mdi-arrow-left text-sm"></i> {{ __('Back to Settings') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-check-circle-outline"></i> {{ session('success') }}
    </div>
@endif

{{-- Add entry form --}}
<div class="bg-surface rounded-xl border border-main shadow-main mb-6">
    <div class="px-5 py-4 border-b border-main">
        <p class="text-sm font-bold text-dark">{{ __('Add New Entry') }}</p>
    </div>
    <div class="px-5 py-5">
        <form method="POST" action="{{ route('tenant.admin.fomo-notifications.entries.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Product ID') }}</label>
                    <input type="number" name="product_id" required placeholder="{{ __('e.g. 42') }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">
                        {{ __('Variant Label') }} <span class="text-subtle font-normal">({{ __('optional') }})</span>
                    </label>
                    <input type="text" name="variant_label" placeholder="{{ __('e.g. Red · XL') }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Display Name') }}</label>
                    <input type="text" name="display_name" required placeholder="{{ __('Sarah') }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Location') }}</label>
                    <input type="text" name="location" required placeholder="{{ __('New York, US') }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors">
                        <i class="mdi mdi-plus text-base"></i> {{ __('Add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Entries table --}}
<div class="bg-surface rounded-xl border border-main shadow-main overflow-hidden">
    <div class="px-5 py-4 border-b border-main flex items-center justify-between">
        <p class="text-sm font-bold text-dark">{{ __('Entries') }}</p>
        <span class="text-xs text-muted">{{ $entries->count() }} {{ __('total') }}</span>
    </div>

    @if($entries->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <i class="mdi mdi-bell-off-outline text-5xl text-subtle mb-3"></i>
            <p class="text-sm font-semibold text-dark mb-1">{{ __('No entries yet') }}</p>
            <p class="text-xs text-muted">{{ __('Add a synthetic entry above to get started.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-secondary border-b border-main text-xs font-semibold text-muted uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">{{ __('Product ID') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Variant') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Display Name') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Location') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-main">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-secondary/50 transition-colors" id="fomo-row-{{ $entry->id }}">
                        <td class="px-5 py-3 font-mono text-xs text-muted">{{ $entry->product_id }}</td>
                        <td class="px-5 py-3 text-dark">{{ $entry->variant_label ?: '—' }}</td>
                        <td class="px-5 py-3 font-medium text-dark">{{ $entry->display_name }}</td>
                        <td class="px-5 py-3 text-muted">{{ $entry->location }}</td>
                        <td class="px-5 py-3">
                            <button class="fomo-toggle-btn inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold transition-colors
                                           {{ $entry->active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                    data-id="{{ $entry->id }}"
                                    data-url="{{ route('tenant.admin.fomo-notifications.entries.toggle', $entry->id) }}"
                                    data-active="{{ $entry->active ? '1' : '0' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $entry->active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $entry->active ? __('Active') : __('Inactive') }}
                            </button>
                        </td>
                        <td class="px-5 py-3">
                            <button class="fomo-delete-btn w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors"
                                    data-id="{{ $entry->id }}"
                                    data-url="{{ route('tenant.admin.fomo-notifications.entries.destroy', $entry->id) }}"
                                    title="{{ __('Delete') }}">
                                <i class="mdi mdi-delete text-base"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.fomo-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id  = btn.dataset.id;
            var url = btn.dataset.url;
            btn.disabled = true;

            fetch(url, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var active = data.active;
                btn.dataset.active = active ? '1' : '0';
                btn.className = btn.className.replace(/bg-\S+\s+text-\S+\s+hover:bg-\S+/, '');
                if (active) {
                    btn.classList.add('bg-green-50', 'text-green-700', 'hover:bg-green-100');
                    btn.querySelector('span').className = 'w-1.5 h-1.5 rounded-full bg-green-500';
                } else {
                    btn.classList.add('bg-gray-100', 'text-gray-500', 'hover:bg-gray-200');
                    btn.querySelector('span').className = 'w-1.5 h-1.5 rounded-full bg-gray-400';
                }
                btn.childNodes[btn.childNodes.length - 1].textContent = active ? ' {{ __("Active") }}' : ' {{ __("Inactive") }}';
                btn.disabled = false;
                toastr.success(data.message || '{{ __("Updated") }}');
            })
            .catch(function () { btn.disabled = false; toastr.error('{{ __("Something went wrong.") }}'); });
        });
    });

    document.querySelectorAll('.fomo-delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('{{ __("Delete this entry?") }}')) return;
            var url = btn.dataset.url;
            btn.disabled = true;

            fetch(url, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            })
            .then(function () {
                var row = document.getElementById('fomo-row-' + btn.dataset.id);
                if (row) row.remove();
                toastr.success('{{ __("Entry deleted.") }}');
            })
            .catch(function () { btn.disabled = false; toastr.error('{{ __("Something went wrong.") }}'); });
        });
    });
})();
</script>
@endsection
