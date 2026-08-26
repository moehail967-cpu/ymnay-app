@extends(include_theme_path('user.dashboard.user-master'))
@section('title') {{ __('Support Ticket') }} @endsection
@section('page-title') {{ __('Support Ticket') }} @endsection
@section('dashboard-content')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--tr-radius);background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;font-size:13px;">{{ session('msg') }}</div>
@endif

@if($errors->any())
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--tr-radius);background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);color:#991b1b;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
@php
$inputStyle = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-family:inherit;font-size:14px;outline:none;background:#fff;transition:border-color .2s;';
$labelStyle = 'font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;';
@endphp
<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <i class="mdi mdi-headset" style="margin-right:8px;color:var(--tr-olive);"></i>
        {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
    </div>
    <div class="tr-dash-card-body">
        <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label style="{{ $labelStyle }}">{{ __('Title') }} <span style="color:var(--tr-olive);">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" style="{{ $inputStyle }}" placeholder="{{ __('Ticket title') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="{{ $labelStyle }}">{{ __('Subject') }} <span style="color:var(--tr-olive);">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" style="{{ $inputStyle }}" placeholder="{{ __('Brief subject') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="{{ $labelStyle }}">{{ __('Priority') }} <span style="color:var(--tr-olive);">*</span></label>
                    <select name="priority" style="{{ $inputStyle }}cursor:pointer;" required>
                        @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $v=>$l)
                            <option value="{{ $v }}" @selected(old('priority')===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label style="{{ $labelStyle }}">{{ __('Department') }} <span style="color:var(--tr-olive);">*</span></label>
                    <select name="departments" style="{{ $inputStyle }}cursor:pointer;" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label style="{{ $labelStyle }}">{{ __('Description') }} <span style="color:var(--tr-olive);">*</span></label>
                    <textarea name="description" rows="6" style="{{ $inputStyle }}resize:vertical;" placeholder="{{ __('Describe your issue…') }}" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <button type="submit" class="tr-btn tr-btn-outline" style="display:inline-flex;align-items:center;gap:8px;">
                <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
            </button>
        </form>
    </div>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
