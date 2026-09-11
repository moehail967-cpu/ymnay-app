@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:800;color:var(--ch-dark);font-size:15px;">
        <i class="las la-ticket-alt" style="color:var(--ch-red);"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="ch-btn ch-btn-outline ch-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="ch-dash-card" style="margin-bottom:20px;">
    <div class="ch-dash-card-body">
        <div class="row g-2" style="font-size:13px;">
            <div class="col-md-6">
                <span style="color:var(--ch-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</span>
                <div style="color:var(--ch-dark);font-weight:700;margin-top:4px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
            </div>
            <div class="col-md-3 col-6">
                <span style="color:var(--ch-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</span>
                <div style="margin-top:4px;">
                    @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'var(--ch-red)', 'medium'=>'var(--ch-amber)', default=>'var(--ch-green)' }; @endphp
                    <span class="ch-dash-badge" style="background:rgba(125,96,87,.08);color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <span style="color:var(--ch-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</span>
                <div style="margin-top:4px;">
                    @php $is_open = ($ticket_details->status??'open')==='open'; @endphp
                    <span class="ch-dash-badge" style="background:{{ $is_open ? 'rgba(39,174,96,.1)' : 'rgba(125,96,87,.1)' }};color:{{ $is_open ? 'var(--ch-green)' : 'var(--ch-muted)' }};">
                        {{ __($ticket_details->status ?? 'open') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--ch-brown)' : 'var(--ch-red)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;font-size:16px;">
            <i class="las {{ $is_admin ? 'la-headset' : 'la-user' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? '#fff' : 'var(--ch-red-light)' }};border:1px solid {{ $is_admin ? 'var(--ch-border)' : 'rgba(192,57,43,.2)' }};border-radius:var(--ch-radius);padding:14px;">
                <div style="font-size:12px;color:var(--ch-muted);margin-bottom:8px;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--ch-brown);font-size:14px;line-height:1.6;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank" style="font-size:12px;color:var(--ch-red);text-decoration:none;">
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
<div class="ch-dash-card">
    <div class="ch-dash-card-header">{{ __('Reply') }}</div>
    <div class="ch-dash-card-body">
        <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
            <input type="hidden" name="user_type" value="user">
            <div style="margin-bottom:14px;">
                <textarea name="message" class="summernote ch-dash-input" rows="5"
                    style="height:auto;resize:vertical;"
                    placeholder="{{ __('Your message…') }}"></textarea>
            </div>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <div>
                    <label class="ch-dash-label">{{ __('Attachment (zip only)') }}</label>
                    <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--ch-muted);">
                </div>
                <button type="submit" class="ch-btn ch-btn-primary" style="margin-top:18px;">
                    <i class="las la-paper-plane"></i> {{ __('Send Reply') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
@endsection
