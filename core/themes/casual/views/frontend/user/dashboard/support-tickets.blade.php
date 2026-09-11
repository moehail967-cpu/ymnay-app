@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Tickets') }} @endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-ticket-alt"></i> {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="cs-dash-action-btn cs-dash-action-primary">
        <i class="las la-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="cs-dash-box">
    <div class="cs-dash-table-wrap">
        <table class="cs-dash-table">
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
                    $p_class  = in_array($priority, ['high','urgent']) ? 'danger' : ($priority === 'medium' ? 'warning' : 'success');
                    $s_class  = $status === 'open' ? 'success' : 'muted';
                @endphp
                <tr>
                    <td class="cs-dash-td-bold">#{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="cs-dash-badge cs-dash-badge-{{ $p_class }}">{{ __($priority) }}</span></td>
                    <td><span class="cs-dash-badge cs-dash-badge-{{ $s_class }}">{{ __($status) }}</span></td>
                    <td class="cs-dash-td-muted">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}" class="cs-dash-action-btn">
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
<div class="cs-dash-empty">
    <i class="las la-headset cs-dash-empty-icon"></i>
    <p class="cs-dash-empty-text">{{ __('No support tickets yet.') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="cs-dash-empty-btn">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
