@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="fn-dash-section-title mb-0">
        <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="fn-btn fn-btn-gold fn-btn-sm">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="fn-dash-box">
    <div class="table-responsive">
        <table class="fn-dash-table">
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
                    $p_cls    = in_array($priority, ['high','urgent']) ? 'fn-badge-danger' : ($priority==='medium' ? 'fn-badge-warn' : 'fn-badge-success');
                    $s_cls    = $status==='open' ? 'fn-badge-success' : 'fn-badge-muted';
                @endphp
                <tr>
                    <td class="fn-fw-bold">#{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="fn-status-badge {{ $p_cls }}">{{ __($priority) }}</span></td>
                    <td><span class="fn-status-badge {{ $s_cls }}">{{ __($status) }}</span></td>
                    <td class="fn-muted">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="fn-btn fn-btn-outline fn-btn-sm">
                            <i class="las la-eye"></i> {{ __('View') }}
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
<div class="fn-dash-box fn-dash-empty">
    <i class="las la-ticket-alt"></i>
    <p>{{ __('No support tickets yet.') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="fn-btn fn-btn-gold">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
