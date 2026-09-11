@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('page-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')
<div class="fm-dash-card">
    <div class="fm-dash-card-header">
        {{ __('Support Tickets') }}
        <a href="{{ theme_user_new_ticket_url() }}"
           class="fm-btn fm-btn-green fm-btn-sm">
            <i class="las la-plus"></i> {{ __('New Ticket') }}
        </a>
    </div>
    <div class="fm-dash-card-body" style="padding:0;">
        @if(isset($all_tickets) && $all_tickets->count())
        <div class="table-responsive">
            <table class="fm-dash-table">
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
                        $pStyle = match($ticket->priority ?? '') { 'high' => 'color:#e74c3c;border:1px solid #e74c3c;', 'medium' => 'color:#f39c12;border:1px solid #f39c12;', default => 'color:var(--fm-green);border:1px solid var(--fm-green);' };
                        $sStyle = match($ticket->status ?? '') { 'open' => 'color:var(--fm-green);border:1px solid var(--fm-green);', 'closed' => 'color:var(--fm-muted);border:1px solid var(--fm-border);', default => 'color:#f39c12;border:1px solid #f39c12;' };
                    @endphp
                    <tr>
                        <td style="font-weight:600;color:var(--fm-dark);">{{ $ticket->subject }}</td>
                        <td><span class="fm-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.03);">{{ ucfirst($ticket->priority ?? 'low') }}</span></td>
                        <td><span class="fm-dash-badge" style="{{ $sStyle }}background:rgba(0,0,0,.03);">{{ ucfirst($ticket->status ?? 'open') }}</span></td>
                        <td style="color:var(--fm-muted);font-size:12px;">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ theme_user_ticket_url($ticket->id) }}"
                               class="fm-btn fm-btn-outline fm-btn-sm">{{ __('View') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:60px;text-align:center;color:var(--fm-muted);">
            <i class="las la-headset" style="font-size:56px;display:block;margin-bottom:16px;"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No tickets yet') }}</p>
            <a href="{{ theme_user_new_ticket_url() }}"
               class="fm-btn fm-btn-green">{{ __('Open a Ticket') }}</a>
        </div>
        @endif
    </div>
</div>
@endsection
