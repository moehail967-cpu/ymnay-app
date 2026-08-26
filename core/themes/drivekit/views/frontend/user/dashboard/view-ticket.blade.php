@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:800;color:var(--dk-white);font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--dk-red);"></i> {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="dk-btn dk-btn-ghost dk-btn-sm">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;margin-bottom:20px;">
    <div class="row g-2" style="font-size:13px;">
        <div class="col-md-6">
            <span style="color:var(--dk-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }}</span>
            <div style="color:var(--dk-white);font-weight:700;margin-top:4px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <span style="color:var(--dk-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }}</span>
            <div style="margin-top:4px;">
                @php $pc = match($ticket_details->priority??'low'){ 'high','urgent'=>'var(--dk-red)', 'medium'=>'#FFC107', default=>'#4CAF50' }; @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(255,255,255,.06);color:{{ $pc }};">{{ __($ticket_details->priority ?? 'low') }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <span style="color:var(--dk-muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</span>
            <div style="margin-top:4px;">
                @php $sc = ($ticket_details->status??'open')==='open' ? '#4CAF50' : 'var(--dk-muted)'; @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(255,255,255,.06);color:{{ $sc }};">{{ __($ticket_details->status ?? 'open') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $is_admin ? 'var(--dk-muted)' : 'var(--dk-red)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;color:#fff;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? 'var(--dk-surface)' : 'rgba(229,48,48,.1)' }};border:1px solid {{ $is_admin ? 'var(--dk-border)' : 'rgba(229,48,48,.25)' }};border-radius:var(--dk-radius);padding:14px;">
                <div style="font-size:12px;color:var(--dk-muted);margin-bottom:8px;">
                    {{ $is_admin ? __('Support Agent') : __('You') }} &bull; {{ $msg->created_at->format('d M Y, h:i A') }}
                </div>
                <div style="color:var(--dk-silver);font-size:14px;line-height:1.6;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank" style="font-size:12px;color:var(--dk-red);text-decoration:none;">
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
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;">
    <div style="font-weight:700;color:var(--dk-white);margin-bottom:16px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div style="margin-bottom:14px;">
            <textarea name="message" class="summernote" rows="5"
                style="width:100%;background:var(--dk-panel);border:1.5px solid var(--dk-border);border-radius:var(--dk-radius);color:var(--dk-white);padding:10px 14px;font-size:14px;outline:none;resize:vertical;"
                placeholder="{{ __('Your message…') }}"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div>
                <label style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:6px;">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--dk-silver);">
            </div>
            <button type="submit" class="dk-btn dk-btn-red" style="margin-top:18px;padding:10px 24px;">
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
