@extends(include_theme_path('user.dashboard.user-master'))
@section('title') {{ __('Support Ticket') }} @endsection
@section('dashboard_content')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--sz-radius);background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;font-size:13px;">{{ session('msg') }}</div>
@endif

@if($errors->any())
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--sz-radius);background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);color:#991b1b;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
@php
$inputStyle  = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-family:var(--sz-font-body);font-size:14px;outline:none;background:var(--sz-white);transition:border-color .2s;';
$labelStyle  = 'font-family:var(--sz-font-head);font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;';
@endphp
<div class="sz-dash-card">
    <div class="sz-dash-section-title" style="margin-bottom:20px;">
        <i class="mdi mdi-headset" style="margin-right:8px;color:var(--sz-red);"></i>
        {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
    </div>
    <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="via" value="{{ __('website') }}">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label style="{{ $labelStyle }}">{{ __('Title') }} <span style="color:var(--sz-red);">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" style="{{ $inputStyle }}" placeholder="{{ __('Ticket title') }}" required>
            </div>
            <div class="col-md-6">
                <label style="{{ $labelStyle }}">{{ __('Subject') }} <span style="color:var(--sz-red);">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" style="{{ $inputStyle }}" placeholder="{{ __('Brief subject') }}" required>
            </div>
            <div class="col-md-6">
                <label style="{{ $labelStyle }}">{{ __('Priority') }} <span style="color:var(--sz-red);">*</span></label>
                <select name="priority" style="{{ $inputStyle }}cursor:pointer;" required>
                    @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $v=>$l)
                        <option value="{{ $v }}" @selected(old('priority')===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="{{ $labelStyle }}">{{ __('Department') }} <span style="color:var(--sz-red);">*</span></label>
                <select name="departments" style="{{ $inputStyle }}cursor:pointer;" required>
                    <option value="">{{ __('Select department') }}</option>
                    @foreach($departments ?? [] as $dep)
                        <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label style="{{ $labelStyle }}">{{ __('Description') }} <span style="color:var(--sz-red);">*</span></label>
                <textarea name="description" rows="6" style="{{ $inputStyle }}resize:vertical;" placeholder="{{ __('Describe your issue…') }}" required>{{ old('description') }}</textarea>
            </div>
        </div>
        <button type="submit" class="sz-dash-btn sz-dash-btn-red" style="display:inline-flex;align-items:center;gap:8px;">
            <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
        </button>
    </form>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
