@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="sz-dash-section-title" style="margin-bottom:0;padding-bottom:0;">
        <i class="mdi mdi-ticket-outline" style="margin-right:8px;color:var(--sz-red);"></i>
        {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="sz-dash-btn sz-dash-btn-red" style="padding:8px 18px;font-size:11px;">
        <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if(isset($all_tickets) && $all_tickets->isNotEmpty())
<div class="sz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="sz-dash-table">
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
                    $p_class  = match($priority){ 'high','urgent'=>'sz-dash-badge-danger', 'medium'=>'sz-dash-badge-warning', default=>'sz-dash-badge-muted' };
                    $status   = $ticket->status ?? 'open';
                    $s_class  = $status === 'open' ? 'sz-dash-badge-success' : 'sz-dash-badge-muted';
                @endphp
                <tr>
                    <td><strong style="color:var(--sz-dark);">#{{ $ticket->id }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="sz-dash-badge {{ $p_class }}">{{ __($priority) }}</span></td>
                    <td><span class="sz-dash-badge {{ $s_class }}">{{ __($status) }}</span></td>
                    <td style="color:var(--sz-muted);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}"
                           class="sz-dash-btn sz-dash-btn-navy"
                           style="padding:6px 14px;font-size:11px;">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:20px;">
    {{ $all_tickets->links() }}
</div>

@else
<div class="sz-dash-card" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-ticket-confirmation-outline" style="font-size:56px;color:var(--sz-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--sz-muted);font-size:14px;font-family:var(--sz-font-body);margin-bottom:20px;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="sz-dash-btn sz-dash-btn-red">
        <i class="mdi mdi-plus"></i> {{ __('Create Ticket') }}
    </a>
</div>
@endif

@endsection
