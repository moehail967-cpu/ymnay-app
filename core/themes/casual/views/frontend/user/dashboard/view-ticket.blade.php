@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-ticket-alt"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="cs-dash-action-btn">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="cs-dash-box cs-dash-status-bar mb-3">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="cs-dash-meta-label">{{ __('Subject') }}</div>
            <div class="cs-dash-meta-value">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Priority') }}</div>
            @php $pc = match($ticket_details->priority ?? 'low') { 'high', 'urgent' => 'danger', 'medium' => 'warning', default => 'success' }; @endphp
            <span class="cs-dash-badge cs-dash-badge-{{ $pc }}">{{ __($ticket_details->priority ?? 'low') }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status ?? 'open') === 'open' ? 'success' : 'muted'; @endphp
            <span class="cs-dash-badge cs-dash-badge-{{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="cs-ticket-thread mb-4">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div class="cs-ticket-msg {{ $is_admin ? 'cs-ticket-msg-admin' : 'cs-ticket-msg-user' }}">
        <div class="cs-ticket-avatar cs-ticket-avatar-{{ $is_admin ? 'admin' : 'user' }}">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div class="cs-ticket-bubble cs-ticket-bubble-{{ $is_admin ? 'admin' : 'user' }}">
            <div class="cs-ticket-meta">
                {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
            </div>
            <div class="cs-ticket-text">{!! $msg->message !!}</div>
            @if($msg->attachment)
            <div class="cs-ticket-attachment">
                <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank">
                    <i class="las la-paperclip"></i> {{ __('Attachment') }}
                </a>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Reply Form --}}
@if(($ticket_details->status ?? '') !== 'close')
<div class="cs-dash-box cs-dash-reply-box">
    <div class="cs-dash-box-head">
        <i class="las la-reply"></i> {{ __('Reply') }}
    </div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="mb-3">
            <textarea name="message" class="summernote cs-dash-textarea" rows="5" placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div class="cs-ticket-reply-footer">
            <div>
                <label class="cs-dash-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" class="cs-dash-file-input">
            </div>
            <button type="submit" class="cs-dash-submit-btn">
                <i class="las la-paper-plane"></i> {{ __('Send Reply') }}
            </button>
        </div>
    </form>
</div>
@endif

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
@endsection
