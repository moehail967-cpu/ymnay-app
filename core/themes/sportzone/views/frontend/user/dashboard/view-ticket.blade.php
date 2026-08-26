@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="sz-dash-section-title" style="margin-bottom:0;padding-bottom:0;">
        <i class="mdi mdi-ticket-outline" style="margin-right:8px;color:var(--sz-red);"></i>
        {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="sz-dash-btn sz-dash-btn-outline" style="padding:7px 16px;font-size:11px;">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

{{-- Ticket Info --}}
<div class="sz-dash-card" style="margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Subject') }}</div>
            <div style="font-weight:600;color:var(--sz-dark);font-size:15px;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Priority') }}</div>
            @php
                $p = $ticket_details->priority ?? 'low';
                $pc = match($p){ 'high','urgent'=>'sz-dash-badge-danger', 'medium'=>'sz-dash-badge-warning', default=>'sz-dash-badge-muted' };
            @endphp
            <span class="sz-dash-badge {{ $pc }}">{{ __($p) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status ?? 'open') === 'open' ? 'sz-dash-badge-success' : 'sz-dash-badge-muted'; @endphp
            <span class="sz-dash-badge {{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

{{-- Messages Thread --}}
<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        {{-- Avatar --}}
        <div style="width:40px;height:40px;border-radius:50%;background:{{ $is_admin ? 'var(--sz-navy)' : 'var(--sz-red)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}" style="font-size:18px;color:#fff;"></i>
        </div>
        {{-- Bubble --}}
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? 'var(--sz-white)' : 'var(--sz-red-light)' }};border:2px solid {{ $is_admin ? 'var(--sz-border)' : 'rgba(198,40,40,.2)' }};padding:16px 20px;">
                <div style="font-family:var(--sz-font-head);font-size:11px;color:var(--sz-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                    <span>{{ $is_admin ? __('Support Agent') : __('You') }}</span>
                    <span>{{ $msg->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div style="font-size:14px;color:var(--sz-dark);line-height:1.7;font-family:var(--sz-font-body);">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--sz-red);text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;">
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
<div class="sz-dash-card">
    <div class="sz-dash-section-title">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div class="sz-dash-form-group">
            <textarea name="message" class="summernote sz-dash-form-input" rows="5"
                      placeholder="{{ __('Your message…') }}"
                      style="resize:vertical;height:auto;"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div>
                <label class="sz-dash-form-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--sz-muted);">
            </div>
            <button type="submit" class="sz-dash-btn sz-dash-btn-red">
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
