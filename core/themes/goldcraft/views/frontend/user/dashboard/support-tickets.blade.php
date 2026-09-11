@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);display:flex;align-items:center;gap:8px;">
        <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="gc-btn gc-btn-primary" style="font-size:12px;">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Georgia,serif;">
            <thead>
                <tr style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Subject') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Priority') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_tickets as $ticket)
                @php
                    $priority_color = match($ticket->priority ?? 'low') {
                        'high','urgent' => '#c53030',
                        'medium'        => '#d97706',
                        default         => '#38a169',
                    };
                    $priority_bg = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'rgba(229,62,62,.1)',
                        'medium'        => 'rgba(245,158,11,.1)',
                        default         => 'rgba(72,187,120,.1)',
                    };
                    $status_color = ($ticket->status ?? 'open') === 'open' ? '#38a169' : 'var(--gc-muted)';
                    $status_bg    = ($ticket->status ?? 'open') === 'open' ? 'rgba(72,187,120,.1)' : 'rgba(0,0,0,.04)';
                @endphp
                <tr style="border-bottom:1px solid var(--gc-border);">
                    <td style="padding:14px 16px;color:var(--gc-rose);font-style:italic;">#{{ $ticket->id }}</td>
                    <td style="padding:14px 16px;color:var(--gc-dark);font-style:italic;">{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;text-transform:uppercase;background:{{ $priority_bg }};color:{{ $priority_color }};">
                            {{ __($ticket->priority ?? 'low') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;text-transform:uppercase;background:{{ $status_bg }};color:{{ $status_color }};">
                            {{ __($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;color:var(--gc-muted);font-style:italic;">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="gc-btn gc-btn-ghost" style="font-size:11px;padding:6px 12px;">
                            <i class="las la-eye"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top:16px;">{{ $all_tickets->links() }}</div>

@else
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:48px;text-align:center;box-shadow:var(--gc-shadow);">
    <div style="font-size:44px;margin-bottom:12px;"><i class="las la-ticket-alt"></i></div>
    <p style="color:var(--gc-muted);font-size:14px;margin-bottom:20px;font-style:italic;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="gc-btn gc-btn-primary">
        {{ __('Create Ticket') }}
    </a>
</div>
@endif

@endsection
