@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="bk-dash-section-title mb-0">
        <i class="mdi mdi-ticket-outline" style="color:var(--bk-rose);"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="bk-btn bk-btn-outline bk-btn-sm">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="bk-dash-box mb-3" style="padding:20px;">
    <div class="row g-3" style="font-size:13px;">
        <div class="col-md-6">
            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Subject') }}</div>
            <div style="font-weight:700;color:var(--bk-dark);">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Priority') }}</div>
            @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'var(--bk-rose)', 'medium'=>'#D4A017', default=>'#4CAF50' }; @endphp
            <span class="bk-status-badge" style="background:rgba(212,133,106,.08);color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--bk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status??'open')==='open' ? '#4CAF50' : 'var(--bk-muted)'; @endphp
            <span class="bk-status-badge" style="background:rgba(76,175,80,.08);color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="d-flex flex-column gap-3 mb-4">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div class="d-flex gap-3 {{ !$is_admin ? 'flex-row-reverse' : '' }}">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--bk-muted)' : 'var(--bk-rose)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;color:#fff;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? '#fff' : 'var(--bk-rose-light)' }};border:1.5px solid {{ $is_admin ? 'var(--bk-border)' : 'var(--bk-rose)' }};border-radius:var(--bk-radius);padding:14px 18px;">
                <div style="font-size:12px;color:var(--bk-muted);margin-bottom:8px;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--bk-dark);font-size:14px;line-height:1.6;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:8px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank" style="font-size:12px;color:var(--bk-rose);">
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
<div class="bk-dash-box" style="padding:24px;">
    <div class="bk-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="mb-3">
            <textarea name="message" class="summernote bk-textarea" rows="5" placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <label class="bk-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--bk-muted);">
            </div>
            <button type="submit" class="bk-btn bk-btn-rose" style="margin-top:20px;">
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
