@extends('theme::frontend.user.dashboard.user-master')
@section('title') {{ __('Support Ticket') }} @endsection
@section('section')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--dk-radius);background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;font-size:13px;">{{ session('msg') }}</div>
@endif

@if($errors->any())
<div style="padding:12px 16px;margin-bottom:18px;border-radius:var(--dk-radius);background:rgba(229,48,48,.08);border:1px solid rgba(229,48,48,.3);color:#991b1b;font-size:13px;">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
<div style="background:var(--dk-surface,#1a1a1a);border:1px solid var(--dk-border,#2a2a2a);border-radius:var(--dk-radius);overflow:hidden;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--dk-border,#2a2a2a);display:flex;align-items:center;gap:10px;">
        <i class="mdi mdi-headset" style="color:var(--dk-red);font-size:20px;"></i>
        <span style="font-family:var(--dk-font-head);font-weight:700;color:var(--dk-silver);font-size:15px;">
            {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
        </span>
    </div>
    <div style="padding:24px 20px;">
        <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">
            @php $inp = 'width:100%;padding:10px 14px;background:var(--dk-bg,#111);border:1px solid var(--dk-border,#2a2a2a);border-radius:var(--dk-radius);color:var(--dk-silver);font-size:14px;font-family:inherit;outline:none;'; @endphp
            @php $lbl = 'font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--dk-muted,#888);margin-bottom:6px;display:block;'; @endphp
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Title') }} <span style="color:var(--dk-red);">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" style="{{ $inp }}" placeholder="{{ __('Ticket title') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Subject') }} <span style="color:var(--dk-red);">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" style="{{ $inp }}" placeholder="{{ __('Brief subject') }}" required>
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Priority') }} <span style="color:var(--dk-red);">*</span></label>
                    <select name="priority" style="{{ $inp }}cursor:pointer;" required>
                        @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $v=>$l)
                            <option value="{{ $v }}" @selected(old('priority')===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label style="{{ $lbl }}">{{ __('Department') }} <span style="color:var(--dk-red);">*</span></label>
                    <select name="departments" style="{{ $inp }}cursor:pointer;" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label style="{{ $lbl }}">{{ __('Description') }} <span style="color:var(--dk-red);">*</span></label>
                    <textarea name="description" rows="6" style="{{ $inp }}resize:vertical;" placeholder="{{ __('Describe your issue…') }}" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <button type="submit" class="dk-btn dk-btn-red">
                <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
            </button>
        </form>
    </div>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
