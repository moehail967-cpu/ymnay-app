@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="ar-dash-section-title mb-0">
        <i class="mdi mdi-ticket-outline" style="color:var(--ar-red);"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="ar-btn ar-btn-outline ar-btn-sm">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="ar-dash-box ar-dash-box-padded mb-3">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="ar-ticket-meta-label">{{ __('Subject') }}</div>
            <div class="ar-ticket-meta-value">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ar-ticket-meta-label">{{ __('Priority') }}</div>
            @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'var(--ar-red)', 'medium'=>'#D4A017', default=>'#4CAF50' }; @endphp
            <span class="ar-status-badge ar-priority-badge-wrap" style="color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div class="ar-ticket-meta-label">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status??'open')==='open' ? '#4CAF50' : 'var(--ar-muted)'; @endphp
            <span class="ar-status-badge ar-status-badge-wrap" style="color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="d-flex flex-column gap-3 mb-4">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div class="ar-msg-item {{ !$is_admin ? 'ar-msg-reverse' : '' }}">
        <div class="ar-msg-avatar {{ $is_admin ? 'ar-msg-avatar-admin' : 'ar-msg-avatar-user' }}">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}"></i>
        </div>
        <div class="ar-msg-body-wrap">
            <div class="ar-msg-bubble {{ $is_admin ? 'ar-msg-bubble-admin' : 'ar-msg-bubble-user' }}">
                <div class="ar-msg-time">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div class="ar-msg-text">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div>
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank" class="ar-msg-attachment">
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
<div class="ar-dash-box ar-dash-box-padded-lg">
    <div class="ar-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="mb-3">
            <textarea name="message" class="summernote ar-textarea" rows="5" placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <label class="ar-auth-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" class="ar-file-input">
            </div>
            <button type="submit" class="ar-btn ar-btn-red mt-3">
                <i class="mdi mdi-send-outline"></i> {{ __('Send Reply') }}
            </button>
        </div>
    </form>
</div>
@endif

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
@endsection
