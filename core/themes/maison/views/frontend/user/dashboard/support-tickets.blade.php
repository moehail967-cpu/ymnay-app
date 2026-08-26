@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="ms-dash-section-title" style="margin:0;">
        <i class="mdi mdi-ticket-outline" style="margin-right:6px;color:var(--ms-olive);"></i>
        {{ __('Support Tickets') }}
    </div>
    <a href="{{ theme_user_new_ticket_url() }}" class="ms-btn-dark" style="font-size:12px;padding:8px 16px;">
        <i class="mdi mdi-plus" style="margin-right:4px;"></i> {{ __('New Ticket') }}
    </a>
</div>

@if($all_tickets->isNotEmpty())
<div class="ms-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="ms-table" style="width:100%;">
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
                    $p_class  = match($priority){ 'high','urgent'=>'ms-badge-warning', 'medium'=>'ms-badge-info', default=>'ms-badge-muted' };
                    $status   = $ticket->status ?? 'open';
                    $s_class  = $status === 'open' ? 'ms-badge-success' : 'ms-badge-muted';
                @endphp
                <tr>
                    <td><strong>#{{ $ticket->id }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::words($ticket->title ?? $ticket->subject ?? '-', 6) }}</td>
                    <td><span class="ms-badge {{ $p_class }}">{{ __($priority) }}</span></td>
                    <td><span class="ms-badge {{ $s_class }}">{{ __($status) }}</span></td>
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ theme_user_ticket_url($ticket->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:var(--ms-linen-d);text-decoration:none;padding:5px 12px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);transition:all .2s;"
                           onmouseover="this.style.borderColor='var(--ms-linen)'"
                           onmouseout="this.style.borderColor='var(--ms-border)'">
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
<div class="ms-dash-card" style="text-align:center;padding:56px 20px;">
    <i class="mdi mdi-ticket-confirmation-outline" style="font-size:48px;color:var(--ms-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ms-muted);font-size:14px;margin-bottom:20px;">{{ __('No support tickets yet') }}</p>
    <a href="{{ theme_user_new_ticket_url() }}" class="ms-btn-dark">{{ __('Create Ticket') }}</a>
</div>
@endif

@endsection
