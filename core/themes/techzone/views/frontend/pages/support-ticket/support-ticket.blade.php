@extends(include_theme_path('user.dashboard.user-master'))
@section('title') {{ __('Support Ticket') }} @endsection
@section('dashboard_content')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--tz-radius-sm);background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;font-size:13px;">{{ session('msg') }}</div>
@endif

@if($errors->any())
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--tz-radius-sm);background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);color:#991b1b;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
@php $inp = 'width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;'; @endphp
<div class="tz-dash-card">
    <div style="font-size:16px;font-weight:700;color:var(--tz-text);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-headset" style="color:var(--tz-blue);"></i>
        {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
    </div>
    <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="via" value="{{ __('website') }}">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label style="font-size:12px;color:var(--tz-muted);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.5px;">{{ __('Title') }} <span style="color:var(--tz-blue);">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" style="{{ $inp }}" placeholder="{{ __('Ticket title') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-size:12px;color:var(--tz-muted);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.5px;">{{ __('Subject') }} <span style="color:var(--tz-blue);">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" style="{{ $inp }}" placeholder="{{ __('Brief subject') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-size:12px;color:var(--tz-muted);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.5px;">{{ __('Priority') }} <span style="color:var(--tz-blue);">*</span></label>
                <select name="priority" style="{{ $inp }}cursor:pointer;" required>
                    @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $v=>$l)
                        <option value="{{ $v }}" @selected(old('priority')===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-size:12px;color:var(--tz-muted);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.5px;">{{ __('Department') }} <span style="color:var(--tz-blue);">*</span></label>
                <select name="departments" style="{{ $inp }}cursor:pointer;" required>
                    <option value="">{{ __('Select department') }}</option>
                    @foreach($departments ?? [] as $dep)
                        <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label style="font-size:12px;color:var(--tz-muted);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.5px;">{{ __('Description') }} <span style="color:var(--tz-blue);">*</span></label>
                <textarea name="description" rows="6" style="{{ $inp }}resize:vertical;" placeholder="{{ __('Describe your issue…') }}" required>{{ old('description') }}</textarea>
            </div>
        </div>
        <button type="submit" style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:var(--tz-blue);color:#fff;border:none;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);">
            <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
        </button>
    </form>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
