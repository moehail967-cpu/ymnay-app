@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="ms-dash-section-title" style="margin:0;">
        <i class="mdi mdi-ticket-outline" style="margin-right:6px;color:var(--ms-olive);"></i>
        {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}"
       class="ms-btn-border"
       style="font-size:12px;padding:7px 14px;">
        <i class="mdi mdi-arrow-left" style="margin-right:4px;"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="ms-dash-card" style="margin-bottom:20px;">
    <div class="row g-2" style="font-size:13px;">
        <div class="col-md-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Subject') }}</div>
            <div style="font-weight:600;color:var(--ms-dark);">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Priority') }}</div>
            @php $p = $ticket_details->priority ?? 'low'; $pc = match($p){ 'high','urgent'=>'ms-badge-warning', 'medium'=>'ms-badge-info', default=>'ms-badge-muted' }; @endphp
            <span class="ms-badge {{ $pc }}">{{ __($p) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status ?? 'open') === 'open' ? 'ms-badge-success' : 'ms-badge-muted'; @endphp
            <span class="ms-badge {{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages Thread --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        {{-- Avatar --}}
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--ms-linen)' : 'var(--ms-dark)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}" style="font-size:16px;color:#fff;"></i>
        </div>
        {{-- Bubble --}}
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? '#fff' : 'var(--ms-warm)' }};border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:14px 18px;">
                <div style="font-size:11px;color:var(--ms-muted);margin-bottom:8px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                    <span>{{ $is_admin ? __('Support Agent') : __('You') }}</span>
                    <span>{{ $msg->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div style="font-size:14px;color:var(--ms-charcoal);line-height:1.7;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--ms-linen-d);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                        <i class="mdi mdi-paperclip"></i> {{ __('Attachment') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Reply Form --}}
@if(($ticket_details->status ?? '') !== 'close')
<div class="ms-dash-card">
    <div class="ms-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="ms-form-group">
            <textarea name="message" class="summernote ms-form-input" rows="5"
                      placeholder="{{ __('Your message…') }}"
                      style="resize:vertical;height:auto;"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <label class="ms-form-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--ms-muted);">
            </div>
            <button type="submit" class="ms-btn-dark">
                <i class="mdi mdi-send-outline" style="margin-right:6px;"></i>
                {{ __('Send Reply') }}
            </button>
        </div>
    </form>
</div>
@endif

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
@endsection
