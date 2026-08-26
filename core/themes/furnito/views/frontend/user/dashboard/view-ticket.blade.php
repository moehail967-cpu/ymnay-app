@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="fn-dash-section-title mb-0">
        <i class="las la-ticket-alt"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="fn-btn fn-btn-outline fn-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="fn-dash-box mb-3 fn-dash-box-pad">
    <div class="row g-3 fn-ticket-info-row">
        <div class="col-md-6">
            <div class="fn-meta-label">{{ __('Subject') }}</div>
            <div class="fn-fw-bold fn-dark-text">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="fn-meta-label">{{ __('Priority') }}</div>
            @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'fn-badge-danger', 'medium'=>'fn-badge-warn', default=>'fn-badge-success' }; @endphp
            <span class="fn-status-badge {{ $pc }}">{{ __($ticket_details->priority ?? 'low') }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div class="fn-meta-label">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status??'open')==='open' ? 'fn-badge-success' : 'fn-badge-muted'; @endphp
            <span class="fn-status-badge {{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="d-flex flex-column gap-3 mb-4">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div class="d-flex gap-3 {{ !$is_admin ? 'flex-row-reverse' : '' }}">
        <div class="fn-ticket-avatar {{ $is_admin ? 'fn-ticket-avatar-admin' : 'fn-ticket-avatar-user' }}">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div class="fn-ticket-bubble-wrap">
            <div class="fn-ticket-bubble {{ $is_admin ? 'fn-bubble-admin' : 'fn-bubble-user' }}">
                <div class="fn-ticket-meta">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div class="fn-ticket-body">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div class="fn-ticket-attachment">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank">
                        <i class="las la-paperclip"></i> {{ __('Attachment') }}
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
<div class="fn-dash-box fn-dash-box-pad">
    <div class="fn-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="mb-3">
            <textarea name="message" class="summernote fn-textarea" rows="5" placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <label class="fn-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" class="fn-file-input">
            </div>
            <button type="submit" class="fn-btn fn-btn-gold mt-3">
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
