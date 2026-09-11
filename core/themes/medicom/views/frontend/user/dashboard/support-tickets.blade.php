@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Support Tickets') }} @endsection
@section('dash-title') {{ __('Support Tickets') }} @endsection

@section('dashboard-content')

{{-- New Ticket Form --}}
<div class="mc-dash-card mb-4">
    <div class="mc-dash-card-title"><i class="las la-plus-circle"></i> {{ __('New Support Ticket') }}</div>
    <form action="{{ route('tenant.frontend.support.ticket.store') }}" method="post">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="mc-form-label">{{ __('Subject') }} <span class="mc-form-required">*</span></label>
                <input type="text" name="subject" class="mc-form-input" placeholder="{{ __('Describe your issue briefly') }}">
            </div>
            <div class="col-12">
                <label class="mc-form-label">{{ __('Message') }} <span class="mc-form-required">*</span></label>
                <textarea name="message" class="mc-form-input mc-form-textarea" rows="4" placeholder="{{ __('Provide details about your issue…') }}"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="mc-btn mc-btn-primary">
                    <i class="las la-paper-plane"></i> {{ __('Submit Ticket') }}
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Ticket List --}}
<div class="mc-dash-card">
    <div class="mc-dash-card-title"><i class="las la-headset"></i> {{ __('My Tickets') }}</div>
    @if(($support_tickets ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="mc-dash-table">
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
                <td>
                    <span class="mc-badge mc-badge-{{ $ticket->status === 'open' ? 'success' : 'muted' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </td>
                <td>{{ $ticket->created_at?->format('d M Y') }}</td>
                <td>
                    <a href="{{ theme_user_ticket_url($ticket->id) }}" class="mc-btn mc-btn-ghost mc-btn-sm">
                        <i class="las la-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;">
        <i class="las la-headset" style="font-size:48px;color:#e0e0e0;display:block;margin-bottom:12px;"></i>
        <p style="color:#888;font-size:14px;">{{ __('No support tickets yet.') }}</p>
    </div>
    @endif
</div>
@endsection
