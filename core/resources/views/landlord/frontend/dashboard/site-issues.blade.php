@extends('landlord.frontend.dashboard.master')
@section('page-title')
    {{__('Site Issues')}}
@endsection

@section('section')
<div class="col-span-full lg:col-span-9 px-4 lg:px-0">

    {{-- Header --}}
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-y rounded-tr-3xl">
        <div class="flex items-center flex-wrap justify-between lg:pr-8 pt-3 pb-4">
            <div class="flex items-center">
                <button id="menuBtn" class="block ml-5 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                    <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                </button>
                <div class="ml-4 lg:ml-16">
                    <h1 class="text-lg font-medium text-secondary">{{__('Site Issues')}}</h1>
                    <p class="text-xs lg:text-sm text-gray-500 mt-1">{{__('Track and monitor your failed or broken site setups')}}</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mt-8 lg:pl-16 lg:pr-9 pb-8 space-y-8">

        {{-- ── Failed Sites ── --}}
        <div class="border border-gray-200 rounded-2xl overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-secondary">{{__('Failed Sites')}}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{__('Sites missing database configuration')}}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold">
                    {{ $failed_tenants->total() }} {{__('total')}}
                </span>
            </div>

            @if($failed_tenants->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3 text-left font-medium">{{__('Site')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Package')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Payment')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Created')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Status')}}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($failed_tenants as $tenant)
                                @php $log = $tenant->payment_log; @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-secondary">{{ $tenant->id }}</span>
                                        <span class="text-gray-400">.{{ env('CENTRAL_DOMAIN') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $log?->package_name ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($log?->payment_status)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                {{ $log->payment_status === 'complete' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                                {{ ucfirst($log->payment_status) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $tenant->created_at?->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-medium">
                                            {{__('DB not configured')}}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    {!! $failed_tenants->links('vendor.pagination.tailwind') !!}
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">{{__('No failed sites.')}}</p>
                </div>
            @endif
        </div>

        {{-- ── Site Errors ── --}}
        <div class="border border-gray-200 rounded-2xl overflow-hidden" style="background-color: var(--section-bg-1, #ffffff)">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-secondary">{{__('Site Errors')}}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{__('Setup errors reported during site creation')}}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-semibold">
                    {{ $exceptions->total() }} {{__('total')}}
                </span>
            </div>

            @if($exceptions->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3 text-left font-medium">{{__('Site')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Issue Type')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Reported')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Status')}}</th>
                                <th class="px-6 py-3 text-left font-medium">{{__('Reason')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exceptions as $ex)
                                {{-- Main row --}}
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer error-row"
                                    onclick="toggleReason('reason-{{ $ex->id }}', this)">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-secondary">{{ $ex->tenant_id }}</span>
                                        <span class="text-gray-400">.{{ env('CENTRAL_DOMAIN') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-medium capitalize">
                                            {{ ucfirst(str_replace('_', ' ', $ex->issue_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $ex->created_at?->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-medium">
                                            {{__('Pending fix')}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs font-medium hover:border-teal-400 hover:text-teal-600 transition-all">
                                            {{__('View reason')}}
                                            <i class="ti tabler-chevron-down text-xs chevron-icon transition-transform duration-200"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Expandable reason row --}}
                                <tr id="reason-{{ $ex->id }}" class="hidden">
                                    <td colspan="5" class="px-6 pb-5 pt-1">
                                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                            <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-2">
                                                {{__('Error Details')}}
                                            </p>
                                            <p class="text-sm text-gray-600 leading-relaxed break-words whitespace-pre-wrap">{{ $ex->description }}</p>
                                            <p class="mt-3 text-xs text-gray-400 flex items-center gap-1.5">
                                                <i class="ti tabler-info-circle text-sm"></i>
                                                {{__('Our team has been notified and will resolve this shortly.')}}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    {!! $exceptions->links('vendor.pagination.tailwind') !!}
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-400">{{__('No site errors.')}}</p>
                </div>
            @endif
        </div>

        {{-- All clear --}}
        @if($failed_tenants->total() === 0 && $exceptions->total() === 0)
            <div class="border border-gray-200 rounded-2xl px-6 py-12 text-center" style="background-color: var(--section-bg-1, #ffffff)">
                <i class="ti tabler-rocket text-teal-500 text-4xl mb-3 block"></i>
                <h3 class="text-sm font-semibold text-secondary mb-1">{{__('All sites are running smoothly!')}}</h3>
                <p class="text-xs text-gray-400">{{__('No issues detected on any of your sites.')}}</p>
            </div>
        @endif

    </main>
</div>

<script>
function toggleReason(id, row) {
    var reasonRow = document.getElementById(id);
    var chevron   = row.querySelector('.chevron-icon');
    var isHidden  = reasonRow.classList.contains('hidden');

    reasonRow.classList.toggle('hidden', !isHidden);
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}
</script>
@endsection
