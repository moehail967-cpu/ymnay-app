@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:700;color:var(--gl-dark);font-size:15px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--gl-gold);"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
        <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
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
                    $status_color = ($ticket->status ?? 'open') === 'open' ? '#38a169' : 'var(--gl-muted)';
                    $status_bg    = ($ticket->status ?? 'open') === 'open' ? 'rgba(72,187,120,.1)' : 'rgba(0,0,0,.04)';
                @endphp
                <tr style="border-bottom:1px solid var(--gl-border);">
                    <td style="padding:14px 16px;color:var(--gl-dark);font-weight:700;">#{{ $ticket->id }}</td>
                    <td style="padding:14px 16px;color:var(--gl-dark);">{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $priority_bg }};color:{{ $priority_color }};">
                            {{ __($ticket->priority ?? 'low') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $status_bg }};color:{{ $status_color }};">
                            {{ __($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;color:var(--gl-muted);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <a href="{{ theme_user_ticket_url($ticket->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:11px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:border-color .2s;"
                           onmouseover="this.style.borderColor='var(--gl-gold)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
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
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:48px;text-align:center;box-shadow:var(--gl-shadow);">
    <i class="mdi mdi-ticket-confirmation" style="font-size:48px;color:var(--gl-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--gl-muted);font-size:14px;margin-bottom:20px;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
        {{ __('Create Ticket') }}
    </a>
</div>
@endif

@endsection
