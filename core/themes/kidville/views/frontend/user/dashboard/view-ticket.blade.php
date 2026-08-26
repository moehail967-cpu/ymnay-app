@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('dash-style')
<x-summernote.css/>
@endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);display:flex;align-items:center;gap:8px;">
        <i class="las la-ticket-alt"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="kv-btn kv-btn-outline kv-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;margin-bottom:20px;box-shadow:var(--kv-shadow);">
    <div class="row g-2" style="font-size:13px;">
        <div class="col-md-6">
            <span style="font-size:10px;color:var(--kv-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;">{{ __('Subject') }}</span>
            <div style="color:var(--kv-dark);font-weight:700;margin-top:4px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:10px;color:var(--kv-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;">{{ __('Priority') }}</span>
            <div style="margin-top:4px;">
                @php
                    $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'var(--kv-red)', 'medium'=>'var(--kv-orange)', default=>'var(--kv-green)' };
                    $pb = match($ticket_details->priority??'low'){ 'high','urgent'=>'rgba(244,67,54,.1)', 'medium'=>'rgba(251,140,0,.1)', default=>'rgba(67,160,71,.1)' };
                @endphp
                <span style="padding:3px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;background:{{ $pb }};color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:10px;color:var(--kv-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;">{{ __('Status') }}</span>
            <div style="margin-top:4px;">
                @php $sc = ($ticket_details->status??'open')==='open' ? 'var(--kv-green)' : 'var(--kv-muted)'; @endphp
                <span style="padding:3px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;background:{{ ($ticket_details->status??'open')==='open'?'rgba(67,160,71,.1)':'rgba(0,0,0,.04)' }};color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:40px;height:40px;border-radius:50%;background:{{ $is_admin ? 'var(--kv-blue)' : 'var(--kv-red)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;color:#fff;">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? 'var(--kv-light)' : 'var(--kv-bg)' }};border:2px solid {{ $is_admin ? 'var(--kv-border)' : 'rgba(30,136,229,.2)' }};border-radius:var(--kv-radius);padding:14px;">
                <div style="font-size:12px;color:var(--kv-muted);margin-bottom:8px;font-weight:600;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--kv-dark);font-size:14px;line-height:1.7;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--kv-blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-weight:700;">
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
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:16px;">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div style="margin-bottom:14px;">
            <textarea name="message" class="summernote" rows="5"
                      style="width:100%;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);padding:10px 14px;font-size:14px;outline:none;resize:vertical;"
                      placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div>
                <label style="font-size:11px;color:var(--kv-muted);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px;font-weight:700;">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--kv-dark);">
            </div>
            <button type="submit" class="kv-btn kv-btn-red" style="margin-top:18px;">
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
