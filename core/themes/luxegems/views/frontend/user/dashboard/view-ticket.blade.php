@extends(theme_view('frontend.user.user-master'))
@section('dash-title') {{ __('Ticket') }} #{{ $ticket->id ?? '' }} @endsection

@section('dash-content')
<div class="lg-dash-card mb-4">
    <div class="lg-dash-card-title">{{ $ticket->subject ?? '' }}</div>
    <div style="font-size:12px;color:var(--lx-muted);margin-bottom:16px;">
        <span class="lg-dash-badge lg-dash-badge-{{ strtolower($ticket->status ?? 'open') }}">{{ ucfirst($ticket->status ?? 'open') }}</span>
        <span style="margin-left:12px;">{{ $ticket->created_at?->format('d M Y') }}</span>
    </div>

    @foreach($ticket->messages ?? [] as $msg)
    <div style="padding:16px;border:1px solid var(--lx-border);margin-bottom:12px;background:{{ $msg->user_type === 'customer' ? 'var(--lx-surface)' : 'rgba(201,168,76,.05)' }};">
        <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
            <span style="font-size:11px;font-weight:600;color:{{ $msg->user_type === 'customer' ? 'var(--lx-white)' : 'var(--lx-gold)' }};letter-spacing:1px;text-transform:uppercase;">
                {{ $msg->user_type === 'customer' ? __('You') : __('Support') }}
            </span>
            <span style="font-size:10px;color:var(--lx-muted);">{{ $msg->created_at?->format('d M Y, H:i') }}</span>
        </div>
        <div style="font-size:13px;color:var(--lx-muted);line-height:1.7;">{{ $msg->message }}</div>
    </div>
    @endforeach
</div>

<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('Reply') }}</div>
    <form action="{{ route('tenant.user.dashboard.support.ticket.view', $ticket->id ?? 0) }}" method="POST">
        @csrf
        <div class="mb-3">
            <textarea name="message" class="lg-dash-input" rows="4"
                      placeholder="{{ __('Type your reply…') }}" style="resize:vertical;"></textarea>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="lg-dash-btn lg-dash-btn-gold">
                <i class="las la-paper-plane"></i> {{ __('Send Reply') }}
            </button>
            <a href="{{ route('tenant.user.home.support.tickets') }}" class="lg-dash-btn lg-dash-btn-outline">{{ __('Back') }}</a>
        </div>
    </form>
</div>
@endsection
