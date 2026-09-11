@extends(include_theme_path('user.user-master'))

@section('title') {{ __('View Ticket') }} @endsection
@section('dash-title') {{ __('View Ticket') }} @endsection

@section('dashboard-content')
@php $ticket = $ticket_details ?? null; @endphp

<div class="ph-dash-card">
    <div class="ph-dash-card-title"><i class="las la-headset"></i> {{ __('Ticket') }} #{{ $ticket?->id }} — {{ $ticket?->subject }}</div>

    {{-- Messages --}}
    <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:28px;">
        @foreach($ticket_messages ?? [] as $msg)
        <div style="display:flex;gap:12px;{{ $msg->user_type === 'admin' ? 'flex-direction:row-reverse;' : '' }}">
            <div style="width:38px;height:38px;border-radius:50%;background:{{ $msg->user_type === 'admin' ? 'var(--ph-terra)' : 'var(--ph-sage)' }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;">
                {{ $msg->user_type === 'admin' ? 'A' : strtoupper(substr(auth('web')->user()?->name ?? 'U', 0, 1)) }}
            </div>
            <div style="max-width:75%;background:{{ $msg->user_type === 'admin' ? 'var(--ph-terra-light)' : 'var(--ph-sage-light)' }};border-radius:var(--ph-radius);padding:12px 16px;border:2px solid var(--ph-border);">
                <div style="font-size:11px;color:var(--ph-muted);margin-bottom:6px;font-weight:700;">
                    {{ $msg->user_type === 'admin' ? __('Support Team') : (auth('web')->user()?->name ?? __('You')) }}
                    · {{ $msg->created_at?->format('d M Y H:i') }}
                </div>
                <p style="font-size:14px;color:var(--ph-dark);line-height:1.7;margin:0;">{{ $msg->message }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Reply --}}
    <form action="{{ theme_user_ticket_reply_url($ticket?->id) }}" method="post">
        @csrf
        <label class="ph-label" style="margin-bottom:8px;display:block;">{{ __('Your Reply') }}</label>
        <textarea name="message" class="ph-input" rows="4" style="height:auto;resize:vertical;margin-bottom:12px;" placeholder="{{ __('Type your message…') }}"></textarea>
        <button type="submit" class="ph-btn ph-btn-terra">
            <i class="las la-paper-plane"></i> {{ __('Send Reply') }}
        </button>
    </form>
</div>

<a href="{{ theme_user_support_url() }}" class="ph-btn ph-btn-ghost">
    <i class="las la-arrow-left"></i> {{ __('Back to Tickets') }}
</a>
@endsection
