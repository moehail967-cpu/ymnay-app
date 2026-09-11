@extends('theme::frontend.user.dashboard.user-master')
@section('title') {{ __('Support Ticket') }} @endsection
@section('section')

@if(session('msg'))
<div class="bk-alert bk-alert-success mb-4">{{ session('msg') }}</div>
@endif

@if($errors->any())
<div class="bk-alert bk-alert-error mb-4">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

@if(auth()->guard('web')->check())
<div class="bk-dash-card">
    <div class="bk-dash-card-header">
        <i class="mdi mdi-headset" style="color:var(--bk-rose);margin-right:8px;"></i>
        {{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}
    </div>
    <div class="bk-dash-card-body">
        <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="bk-label">{{ __('Title') }} <span style="color:var(--bk-rose);">*</span></label>
                    <input class="bk-input" type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('Ticket title') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="bk-label">{{ __('Subject') }} <span style="color:var(--bk-rose);">*</span></label>
                    <input class="bk-input" type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Brief subject') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="bk-label">{{ __('Priority') }} <span style="color:var(--bk-rose);">*</span></label>
                    <select class="bk-input" name="priority" required>
                        @foreach(['low'=>__('Low'),'medium'=>__('Medium'),'high'=>__('High'),'urgent'=>__('Urgent')] as $v=>$l)
                            <option value="{{ $v }}" @selected(old('priority')===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="bk-label">{{ __('Department') }} <span style="color:var(--bk-rose);">*</span></label>
                    <select class="bk-input" name="departments" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments')==$dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="bk-label">{{ __('Description') }} <span style="color:var(--bk-rose);">*</span></label>
                    <textarea class="bk-input" name="description" rows="6" style="resize:vertical;" placeholder="{{ __('Describe your issue…') }}" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <button type="submit" class="bk-btn bk-btn-rose">
                <i class="mdi mdi-send-outline"></i> {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
            </button>
        </form>
    </div>
</div>
@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
