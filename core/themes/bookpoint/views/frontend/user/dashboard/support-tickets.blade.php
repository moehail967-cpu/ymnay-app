@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="bp-dash-section-title mb-0">
        <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="bp-btn bp-btn-green bp-btn-sm">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="bp-dash-box">
    <div style="overflow-x:auto;">
        <table class="bp-dash-table">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_tickets as $ticket)
                @php
                    $priority = $ticket->priority ?? 'low';
                    $status   = $ticket->status   ?? 'open';
                    $p_cls    = in_array($priority, ['high','urgent']) ? 'bp-badge-danger' : ($priority==='medium' ? 'bp-badge-warn' : 'bp-badge-success');
                    $s_cls    = $status==='open' ? 'bp-badge-success' : 'bp-badge-muted';
                @endphp
                <tr>
                    <td class="bp-fw-bold">#{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="bp-status-badge {{ $p_cls }}">{{ __($priority) }}</span></td>
                    <td><span class="bp-status-badge {{ $s_cls }}">{{ __($status) }}</span></td>
                    <td class="bp-muted">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="bp-btn bp-btn-outline bp-btn-sm">
                            <i class="las la-eye"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $all_tickets->links() }}</div>

@else
<div class="bp-dash-box bp-dash-empty">
    <i class="las la-ticket-alt"></i>
    <p>{{ __('No support tickets yet.') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="bp-btn bp-btn-green">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
