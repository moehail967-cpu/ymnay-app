@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('page-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')
<div class="vl-dash-card">
    <div class="vl-dash-card-header">
        {{ __('Support Tickets') }}
        <a href="{{ theme_user_new_ticket_url() }}"
           class="vl-btn vl-btn-primary" style="padding:6px 16px;font-size:9px;letter-spacing:2px;">
            <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
        </a>
    </div>
    <div class="vl-dash-card-body" style="padding:0;">
        @if(isset($all_tickets) && $all_tickets->count())
        <div class="table-responsive">
            <table class="vl-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_tickets as $ticket)
                    @php
                        $pStyle = match($ticket->priority ?? '') { 'high' => 'color:#E53E3E;border:1px solid #E53E3E;', 'medium' => 'color:#D69E2E;border:1px solid #D69E2E;', default => 'color:#48BB78;border:1px solid #48BB78;' };
                        $sStyle = match($ticket->status ?? '') { 'open' => 'color:var(--vl-champagne);border:1px solid var(--vl-champagne);', 'closed' => 'color:var(--vl-muted);border:1px solid var(--vl-border);', default => 'color:#D69E2E;border:1px solid #D69E2E;' };
                    @endphp
                    <tr>
                        <td style="color:var(--vl-ivory);">{{ $ticket->subject }}</td>
                        <td><span class="vl-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.1);">{{ ucfirst($ticket->priority ?? 'low') }}</span></td>
                        <td><span class="vl-dash-badge" style="{{ $sStyle }}background:rgba(0,0,0,.1);">{{ ucfirst($ticket->status ?? 'open') }}</span></td>
                        <td style="color:var(--vl-muted);font-size:11px;">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ theme_user_ticket_url($ticket->id) }}"
                               class="vl-btn vl-btn-outline" style="padding:6px 14px;font-size:9px;letter-spacing:1px;">{{ __('View') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:64px;text-align:center;color:var(--vl-muted);">
            <i class="mdi mdi-lifebuoy" style="font-size:56px;display:block;margin-bottom:16px;color:var(--vl-border);"></i>
            <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;margin-bottom:20px;">{{ __('No tickets yet') }}</div>
            <a href="{{ theme_user_new_ticket_url() }}"
               class="vl-btn vl-btn-primary" style="font-size:9px;letter-spacing:3px;">{{ __('Open a Ticket') }}</a>
        </div>
        @endif
    </div>
</div>
@endsection
