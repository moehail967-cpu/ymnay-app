@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('dash-style')
<x-summernote.css/>
@endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);display:flex;align-items:center;gap:8px;">
        <i class="las la-ticket-alt"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="gc-btn gc-btn-ghost" style="font-size:12px;">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:20px;box-shadow:var(--gc-shadow);">
    <div class="row g-2" style="font-size:13px;font-family:Georgia,serif;">
        <div class="col-md-6">
            <span style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Subject') }}</span>
            <div style="color:var(--gc-dark);font-style:italic;margin-top:4px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Priority') }}</span>
            <div style="margin-top:4px;">
                @php
                    $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'#c53030', 'medium'=>'#d97706', default=>'#38a169' };
                    $pb = match($ticket_details->priority??'low'){ 'high','urgent'=>'rgba(229,62,62,.1)', 'medium'=>'rgba(245,158,11,.1)', default=>'rgba(72,187,120,.1)' };
                @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;background:{{ $pb }};color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Status') }}</span>
            <div style="margin-top:4px;">
                @php $sc = ($ticket_details->status??'open')==='open' ? '#38a169' : 'var(--gc-muted)'; @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;background:{{ ($ticket_details->status??'open')==='open'?'rgba(72,187,120,.1)':'rgba(0,0,0,.04)' }};color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--gc-rose)' : 'var(--gc-dark)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;color:#fff;">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? 'var(--gc-ivory)' : 'var(--gc-warm)' }};border:1px solid {{ $is_admin ? 'var(--gc-border)' : 'rgba(201,128,106,.25)' }};border-radius:var(--gc-radius);padding:14px;">
                <div style="font-size:12px;color:var(--gc-muted);margin-bottom:8px;font-style:italic;font-family:Georgia,serif;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--gc-dark);font-size:14px;line-height:1.7;font-family:Georgia,serif;font-style:italic;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--gc-rose);text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-style:italic;">
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
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;box-shadow:var(--gc-shadow);">
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div style="margin-bottom:14px;">
            <textarea name="message" class="summernote" rows="5"
                      style="width:100%;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);padding:10px 14px;font-size:14px;font-family:Georgia,serif;outline:none;resize:vertical;"
                      placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div>
                <label style="font-size:10px;color:var(--gc-muted);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px;">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--gc-dark);">
            </div>
            <button type="submit" class="gc-btn gc-btn-primary" style="margin-top:18px;">
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
