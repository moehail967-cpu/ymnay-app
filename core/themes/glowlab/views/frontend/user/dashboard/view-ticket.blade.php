@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('dash-style')
<x-summernote.css/>
@endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:700;color:var(--gl-dark);font-size:15px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--gl-gold);"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:border-color .2s;"
       onmouseover="this.style.borderColor='var(--gl-gold)'" onmouseout="this.style.borderColor='var(--gl-border)'">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:20px;box-shadow:var(--gl-shadow);">
    <div class="row g-2" style="font-size:13px;">
        <div class="col-md-6">
            <span style="font-size:11px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</span>
            <div style="color:var(--gl-dark);font-weight:700;margin-top:4px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:11px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</span>
            <div style="margin-top:4px;">
                @php
                    $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'#c53030', 'medium'=>'#d97706', default=>'#38a169' };
                    $pb = match($ticket_details->priority??'low'){ 'high','urgent'=>'rgba(229,62,62,.1)', 'medium'=>'rgba(245,158,11,.1)', default=>'rgba(72,187,120,.1)' };
                @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $pb }};color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <span style="font-size:11px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</span>
            <div style="margin-top:4px;">
                @php $sc = ($ticket_details->status??'open')==='open' ? '#38a169' : 'var(--gl-muted)'; @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ ($ticket_details->status??'open')==='open'?'rgba(72,187,120,.1)':'rgba(0,0,0,.04)' }};color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--gl-gold)' : 'var(--gl-dark)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;color:#fff;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? '#fff' : 'var(--gl-gold-pale)' }};border:1px solid {{ $is_admin ? 'var(--gl-border)' : 'rgba(184,150,90,.25)' }};border-radius:var(--gl-radius);padding:14px;">
                <div style="font-size:12px;color:var(--gl-muted);margin-bottom:8px;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--gl-dark);font-size:14px;line-height:1.6;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--gl-gold);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
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
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;box-shadow:var(--gl-shadow);">
    <div style="font-weight:700;color:var(--gl-dark);margin-bottom:16px;font-size:11px;text-transform:uppercase;letter-spacing:1px;">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div style="margin-bottom:14px;">
            <textarea name="message" class="summernote" rows="5"
                      style="width:100%;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);padding:10px 14px;font-size:14px;font-family:inherit;outline:none;resize:vertical;"
                      placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div>
                <label style="font-size:11px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--gl-dark);">
            </div>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;margin-top:18px;"
                    onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                <i class="mdi mdi-send"></i> {{ __('Send Reply') }}
            </button>
        </div>
    </form>
</div>
@endif

@endsection

@section('dashboard-scripts')
<x-summernote.js/>
@endsection
