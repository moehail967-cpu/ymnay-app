@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="bp-dash-section-title mb-0">
        <i class="las la-ticket-alt"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="bp-btn bp-btn-outline bp-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="bp-dash-box mb-3 bp-dash-box-pad">
    <div class="row g-3" style="font-size:13px;">
        <div class="col-md-6">
            <div class="bp-meta-label">{{ __('Subject') }}</div>
            <div class="bp-fw-bold bp-dark-text">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="bp-meta-label">{{ __('Priority') }}</div>
            @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'bp-badge-danger', 'medium'=>'bp-badge-warn', default=>'bp-badge-success' }; @endphp
            <span class="bp-status-badge {{ $pc }}">{{ __($ticket_details->priority ?? 'low') }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div class="bp-meta-label">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status??'open')==='open' ? 'bp-badge-success' : 'bp-badge-muted'; @endphp
            <span class="bp-status-badge {{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="d-flex flex-column gap-3 mb-4">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div class="d-flex gap-3 {{ !$is_admin ? 'flex-row-reverse' : '' }}">
        <div class="bp-ticket-avatar {{ $is_admin ? 'bp-ticket-avatar-admin' : 'bp-ticket-avatar-user' }}">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div class="bp-ticket-bubble {{ $is_admin ? 'bp-bubble-admin' : 'bp-bubble-user' }}">
                <div class="bp-ticket-meta">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div class="bp-ticket-body">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div class="bp-ticket-attachment">
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
<div class="bp-dash-box bp-dash-box-pad">
    <div class="bp-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="mb-3">
            <textarea name="message" class="summernote bp-textarea" rows="5" placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <label class="bp-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:#888;">
            </div>
            <button type="submit" class="bp-btn bp-btn-green" style="margin-top:20px;">
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
