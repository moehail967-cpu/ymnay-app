@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('page-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')
<div class="fp-dash-card">
    <div class="fp-dash-card-header">
        {{ __('Support Tickets') }}
        <a href="{{ theme_user_new_ticket_url() }}" class="fp-btn fp-btn-primary fp-btn-sm">
            <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
        </a>
    </div>
    <div class="fp-dash-card-body" style="padding:0;">
        @if(isset($all_tickets) && $all_tickets->count())
        <div class="table-responsive">
            <table class="fp-dash-table">
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
                        $pStyle = match($ticket->priority ?? '') { 'high' => 'color:#ff4d4d;border:1px solid #ff4d4d;', 'medium' => 'color:#ffc107;border:1px solid #ffc107;', default => 'color:var(--fp-green);border:1px solid var(--fp-green);' };
                        $sStyle = match($ticket->status ?? '') { 'open' => 'color:var(--fp-green);border:1px solid var(--fp-green);', 'closed' => 'color:var(--fp-muted);border:1px solid var(--fp-border);', default => 'color:#ffc107;border:1px solid #ffc107;' };
                    @endphp
                    <tr>
                        <td style="font-family:var(--fp-font-head);color:var(--fp-text);">{{ $ticket->subject }}</td>
                        <td><span class="fp-dash-badge" style="{{ $pStyle }}background:rgba(255,255,255,.03);">{{ ucfirst($ticket->priority ?? 'low') }}</span></td>
                        <td><span class="fp-dash-badge" style="{{ $sStyle }}background:rgba(255,255,255,.03);">{{ ucfirst($ticket->status ?? 'open') }}</span></td>
                        <td style="color:var(--fp-muted);font-size:12px;">{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ theme_user_ticket_url($ticket->id) }}"
                               class="fp-btn fp-btn-dark fp-btn-sm">{{ __('View') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div style="padding:60px;text-align:center;color:var(--fp-muted);">
                <i class="mdi mdi-headset-off" style="font-size:64px;color:var(--fp-border);display:block;margin-bottom:16px;"></i>
                <p style="font-family:var(--fp-font-head);font-size:18px;text-transform:uppercase;letter-spacing:1px;">{{ __('No tickets yet') }}</p>
                <a href="{{ theme_user_new_ticket_url() }}" class="fp-btn fp-btn-primary" style="margin-top:12px;">{{ __('Open a Ticket') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
