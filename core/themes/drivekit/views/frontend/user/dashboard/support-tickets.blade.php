@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:800;color:var(--dk-white);font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--dk-red);"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="dk-btn dk-btn-red dk-btn-sm">
        <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--dk-panel);border-bottom:1px solid var(--dk-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($all_tickets as $ticket)
                @php
                    $priority_color = match($ticket->priority ?? 'low') {
                        'high','urgent' => 'var(--dk-red)',
                        'medium' => '#FFC107',
                        default => '#4CAF50',
                    };
                    $status_color = ($ticket->status ?? 'open') === 'open' ? '#4CAF50' : 'var(--dk-muted)';
                @endphp
                <tr style="border-bottom:1px solid var(--dk-border);">
                    <td style="padding:14px 16px;color:var(--dk-white);font-weight:700;">#{{ $ticket->id }}</td>
                    <td style="padding:14px 16px;color:var(--dk-white);">{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:rgba(255,255,255,.06);color:{{ $priority_color }};">
                            {{ __($ticket->priority ?? 'low') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:rgba(255,255,255,.06);color:{{ $status_color }};">
                            {{ __($ticket->status ?? 'open') }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;color:var(--dk-silver);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="dk-btn dk-btn-ghost dk-btn-sm">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="dk-pagination mt-4">
    {{ $all_tickets->links() }}
</div>

@else
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:48px;text-align:center;">
    <i class="mdi mdi-ticket-confirmation-outline" style="font-size:48px;color:var(--dk-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--dk-silver);">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="dk-btn dk-btn-red">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
