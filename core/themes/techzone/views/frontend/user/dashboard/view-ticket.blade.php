@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Ticket') }} #{{ $ticket_details->id }} @endsection

@section('style')
<x-summernote.css/>
@endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:18px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-ticket-outline" style="color:var(--tz-blue);"></i>
        {{ __('Ticket') }} #{{ $ticket_details->id }}
    </div>
    <a href="{{ theme_user_tickets_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;text-decoration:none;font-family:var(--tz-font);transition:all .2s;"
       onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
        <i class="mdi mdi-arrow-left"></i> {{ __('All Tickets') }}
    </a>
</div>

<div class="tz-dash-card tz-dash-card-body" style="margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Subject') }}</div>
            <div style="font-size:15px;font-weight:700;color:#fff;">{{ $ticket_details->title ?? $ticket_details->subject ?? '-' }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Priority') }}</div>
            @php $p = $ticket_details->priority ?? 'low'; $pc = match($p){ 'high','urgent'=>'tz-dash-badge-danger', 'medium'=>'tz-dash-badge-warning', default=>'tz-dash-badge-muted' }; @endphp
            <span class="tz-dash-badge {{ $pc }}">{{ __($p) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Status') }}</div>
            @php $sc = ($ticket_details->status ?? 'open') === 'open' ? 'tz-dash-badge-success' : 'tz-dash-badge-muted'; @endphp
            <span class="tz-dash-badge {{ $sc }}">{{ __($ticket_details->status ?? 'open') }}</span>
        </div>
    </div>
</div>

<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:24px;">
    @foreach($all_messages as $msg)
    @php $is_admin = $msg->type === 'admin'; @endphp
    <div style="display:flex;gap:12px;{{ $is_admin ? '' : 'flex-direction:row-reverse;' }}">
        <div style="width:40px;height:40px;border-radius:50%;background:{{ $is_admin ? 'var(--tz-surface)' : 'var(--tz-blue)' }};border:1px solid {{ $is_admin ? 'var(--tz-border)' : 'var(--tz-blue)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi {{ $is_admin ? 'mdi-headset' : 'mdi-account' }}" style="font-size:18px;color:{{ $is_admin ? 'var(--tz-muted)' : '#fff' }};"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="background:{{ $is_admin ? 'var(--tz-surface)' : 'var(--tz-blue-glow)' }};border:1px solid {{ $is_admin ? 'var(--tz-border)' : 'var(--tz-blue)' }};border-radius:var(--tz-radius);padding:16px 20px;">
                <div style="font-size:11px;color:var(--tz-muted);margin-bottom:8px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                    <span style="font-weight:700;">{{ $is_admin ? __('Support Agent') : __('You') }}</span>
                    <span>{{ $msg->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div style="font-size:14px;color:var(--tz-text);line-height:1.7;">{!! $msg->message !!}</div>
                @if($msg->attachment)
                <div style="margin-top:10px;">
                    <a href="{{ asset('assets/uploads/ticket/'.$msg->attachment) }}" target="_blank"
                       style="font-size:12px;color:var(--tz-blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                        <i class="mdi mdi-paperclip"></i> {{ __('Attachment') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(($ticket_details->status ?? '') !== 'close')
<div class="tz-dash-card tz-dash-card-body">
    <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">{{ __('Reply') }}</div>
    <form action="{{ theme_user_ticket_reply_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket_details->id }}">
        <input type="hidden" name="user_type" value="user">
        <div style="margin-bottom:14px;">
            <textarea name="message" class="summernote tz-dash-input" rows="5"
                      placeholder="{{ __('Your message…') }}"
                      style="resize:vertical;height:auto;"></textarea>
        </div>
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div>
                <label class="tz-dash-label">{{ __('Attachment (zip only)') }}</label>
                <input type="file" name="file" accept=".zip" style="font-size:13px;color:var(--tz-muted);">
            </div>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;border:0;padding:10px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
                    onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
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
