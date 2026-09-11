@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:18px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--tz-blue);"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;padding:9px 18px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;text-decoration:none;font-family:var(--tz-font);transition:background .2s;"
       onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
        <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if(isset($all_tickets) && $all_tickets->isNotEmpty())
<div class="tz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="tz-dash-table">
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
                    $p_class  = match($priority){ 'high','urgent'=>'tz-dash-badge-danger', 'medium'=>'tz-dash-badge-warning', default=>'tz-dash-badge-muted' };
                    $status   = $ticket->status ?? 'open';
                    $s_class  = $status === 'open' ? 'tz-dash-badge-success' : 'tz-dash-badge-muted';
                @endphp
                <tr>
                    <td><strong style="color:#fff;">#{{ $ticket->id }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="tz-dash-badge {{ $p_class }}">{{ __($priority) }}</span></td>
                    <td><span class="tz-dash-badge {{ $s_class }}">{{ __($status) }}</span></td>
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;background:var(--tz-blue);color:#fff;padding:5px 12px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;text-decoration:none;font-family:var(--tz-font);">
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
<div class="tz-dash-card tz-dash-card-body" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-ticket-confirmation-outline" style="font-size:52px;color:var(--tz-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--tz-muted);font-size:14px;margin-bottom:20px;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;padding:10px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;text-decoration:none;font-family:var(--tz-font);">
        <i class="mdi mdi-plus"></i> {{ __('Create Ticket') }}
    </a>
</div>
@endif

@endsection
