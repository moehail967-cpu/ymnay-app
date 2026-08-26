@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:800;color:var(--ch-dark);font-size:15px;">
        <i class="las la-ticket-alt" style="color:var(--ch-red);"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="ch-btn ch-btn-primary ch-btn-sm">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="ch-dash-card">
    <div style="overflow-x:auto;">
        <table class="ch-dash-table">
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
                    $priority_color = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'var(--ch-red)',
                        'medium' => 'var(--ch-amber)',
                        default => 'var(--ch-green)',
                    };
                    $priority_bg = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'rgba(192,57,43,.1)',
                        'medium' => 'rgba(243,156,18,.1)',
                        default => 'rgba(39,174,96,.1)',
                    };
                    $status_open = ($ticket->status ?? 'open') === 'open';
                @endphp
                <tr>
                    <td style="font-weight:700;">#{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td>
                        <span class="ch-dash-badge" style="background:{{ $priority_bg }};color:{{ $priority_color }};">
                            {{ __($ticket->priority ?? 'low') }}
                        </span>
                    </td>
                    <td>
                        <span class="ch-dash-badge" style="
                            background:{{ $status_open ? 'rgba(39,174,96,.1)' : 'rgba(125,96,87,.1)' }};
                            color:{{ $status_open ? 'var(--ch-green)' : 'var(--ch-muted)' }};">
                            {{ __($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td style="color:var(--ch-muted);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="ch-btn ch-btn-outline ch-btn-sm">
                            <i class="las la-eye"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="ch-pagination mt-4">
    {{ $all_tickets->links() }}
</div>

@else
<div class="ch-dash-card">
    <div style="padding:48px;text-align:center;">
        <i class="las la-ticket-alt" style="font-size:48px;color:var(--ch-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ch-muted);">{{ __('No support tickets yet') }}</p>
        <a href="{{ theme_user_new_ticket_url() }}" class="ch-btn ch-btn-primary">{{ __('Create Ticket') }}</a>
    </div>
</div>
@endif

@endsection
