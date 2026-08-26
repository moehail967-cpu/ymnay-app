@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);display:flex;align-items:center;gap:8px;">
        <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="kv-btn kv-btn-red kv-btn-sm">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;box-shadow:var(--kv-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--kv-light);border-bottom:2px solid var(--kv-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_tickets as $ticket)
                @php
                    $priority_color = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'var(--kv-red)',
                        'medium'        => 'var(--kv-orange)',
                        default         => 'var(--kv-green)',
                    };
                    $priority_bg = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'rgba(244,67,54,.1)',
                        'medium'        => 'rgba(251,140,0,.1)',
                        default         => 'rgba(67,160,71,.1)',
                    };
                    $status_color = ($ticket->status ?? 'open') === 'open' ? 'var(--kv-green)' : 'var(--kv-muted)';
                    $status_bg    = ($ticket->status ?? 'open') === 'open' ? 'rgba(67,160,71,.1)' : 'rgba(0,0,0,.04)';
                @endphp
                <tr style="border-bottom:1px solid var(--kv-border);">
                    <td style="padding:14px 16px;color:var(--kv-red);font-weight:800;">#{{ $ticket->id }}</td>
                    <td style="padding:14px 16px;color:var(--kv-dark);font-weight:600;">{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $priority_bg }};color:{{ $priority_color }};">
                            {{ __($ticket->priority ?? 'low') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $status_bg }};color:{{ $status_color }};">
                            {{ __($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;color:var(--kv-muted);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="kv-btn kv-btn-outline kv-btn-sm">
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
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:48px;text-align:center;box-shadow:var(--kv-shadow);">
    <i class="las la-ticket-alt" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--kv-muted);font-size:14px;margin-bottom:20px;font-weight:600;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="kv-btn kv-btn-red">
        {{ __('Create Ticket') }}
    </a>
</div>
@endif

@endsection
