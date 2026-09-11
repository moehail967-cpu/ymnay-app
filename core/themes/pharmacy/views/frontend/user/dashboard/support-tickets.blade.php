@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('dash-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')

{{-- New Ticket Form --}}
<div class="pf-dash-card mb-4">
    <div class="pf-dash-card-title"><i class="las la-plus-circle"></i> {{ __('New Support Ticket') }}</div>
    <form action="{{ route('tenant.frontend.support.ticket.store') }}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="pf-label">{{ __('Subject') }} <span class="pf-required">*</span></label>
                <input type="text" name="subject" class="pf-input" placeholder="{{ __('Describe your issue briefly') }}">
            </div>
            <div class="col-12">
                <label class="pf-label">{{ __('Message') }} <span class="pf-required">*</span></label>
                <textarea name="message" class="pf-input" rows="4" style="height:auto;resize:vertical;" placeholder="{{ __('Provide details about your issue…') }}"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="pf-btn pf-btn-teal">
                    <i class="las la-paper-plane"></i> {{ __('Submit Ticket') }}
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Ticket List --}}
<div class="pf-dash-card">
    <div class="pf-dash-card-title"><i class="las la-headset"></i> {{ __('My Tickets') }}</div>
    @if(($support_tickets ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="pf-dash-table">
            <thead><tr>
                <th>{{ __('#') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($support_tickets as $ticket)
            <tr>
                <td>#{{ $ticket->id }}</td>
                <td>{{ $ticket->subject }}</td>
                <td><span class="pf-badge-pill pf-badge-{{ $ticket->status === 'open' ? 'success' : 'muted' }}">{{ ucfirst($ticket->status) }}</span></td>
                <td>{{ $ticket->created_at?->format('d M Y') }}</td>
                <td><a href="{{ theme_user_view_ticket_url($ticket->id) }}" class="pf-btn pf-btn-ghost pf-btn-sm"><i class="las la-eye"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;">
        <i class="las la-headset" style="font-size:48px;color:var(--pf-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--pf-muted);font-size:14px;">{{ __('No support tickets yet.') }}</p>
    </div>
    @endif
</div>
@endsection
