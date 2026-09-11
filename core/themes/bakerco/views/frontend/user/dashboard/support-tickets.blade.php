@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="bk-dash-section-title mb-0">
        <i class="mdi mdi-ticket-outline" style="color:var(--bk-rose);"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="bk-btn bk-btn-rose bk-btn-sm">
        <i class="mdi mdi-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="bk-dash-box">
    <div style="overflow-x:auto;">
        <table class="bk-dash-table">
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
                    $status   = $ticket->status   ?? 'open';
                    $p_color  = in_array($priority, ['high','urgent']) ? 'var(--bk-rose)' : ($priority==='medium' ? '#D4A017' : '#4CAF50');
                    $s_color  = $status==='open' ? '#4CAF50' : 'var(--bk-muted)';
                @endphp
                <tr>
                    <td style="font-weight:700;">#{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="bk-status-badge" style="background:rgba(212,133,106,.08);color:{{ $p_color }};">{{ __($priority) }}</span></td>
                    <td><span class="bk-status-badge" style="background:rgba(76,175,80,.08);color:{{ $s_color }};">{{ __($status) }}</span></td>
                    <td style="color:var(--bk-muted);">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="bk-btn bk-btn-outline bk-btn-sm">
                            <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $all_tickets->links() }}</div>

@else
<div class="bk-dash-box" style="padding:56px;text-align:center;">
    <i class="mdi mdi-ticket-confirmation-outline" style="font-size:52px;color:var(--bk-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--bk-muted);margin-bottom:20px;">{{ __('No support tickets yet.') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="bk-btn bk-btn-rose">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
