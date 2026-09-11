@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Ticket') }} @endsection
@section('site-title') {{ __('Support Ticket') }} @endsection

@section('section')

@if(session('msg'))
<div style="padding:12px 16px;margin-bottom:20px;border-radius:6px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.3);color:#166534;font-size:13px;">
    {{ session('msg') }}
</div>
@endif

{!! theme_error_msg() !!}

@if(auth()->guard('web')->check())

<div class="seller-profile-details-wrapper">
    <div class="seller-profile-edit-flex">
        <h3 class="title-seller">{{ get_static_option('support_ticket_form_title') ?: __('Open a Support Ticket') }}</h3>
    </div>

    <form action="{{ theme_user_ticket_store_url() }}" method="POST" enctype="multipart/form-data" class="support-ticket-form-wrapper">
        @csrf
        <input type="hidden" name="via" value="{{ __('website') }}">

        <div class="row margin-top-10">
            <div class="col-lg-6 col-md-6 margin-top-30">
                <div class="single-info-input">
                    <label class="info-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input class="form--control" type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('Ticket title') }}" required>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 margin-top-30">
                <div class="single-info-input">
                    <label class="info-title">{{ __('Subject') }} <span class="text-danger">*</span></label>
                    <input class="form--control" type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Brief subject') }}" required>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 margin-top-30">
                <div class="single-info-input">
                    <label class="info-title">{{ __('Priority') }} <span class="text-danger">*</span></label>
                    <select class="form--control" name="priority" required>
                        @foreach(['low' => __('Low'), 'medium' => __('Medium'), 'high' => __('High'), 'urgent' => __('Urgent')] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('priority') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 margin-top-30">
                <div class="single-info-input">
                    <label class="info-title">{{ __('Department') }} <span class="text-danger">*</span></label>
                    <select class="form--control" name="departments" required>
                        <option value="">{{ __('Select department') }}</option>
                        @foreach($departments ?? [] as $dep)
                            <option value="{{ $dep->id }}" @selected(old('departments') == $dep->id)>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-12 margin-top-30">
                <div class="single-info-input">
                    <label class="info-title">{{ __('Description') }} <span class="text-danger">*</span></label>
                    <textarea class="form--control textarea-form" name="description" rows="7" placeholder="{{ __('Describe your issue in detail…') }}" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="col-lg-12 margin-top-30">
                <div class="btn-wrapper">
                    <button class="btn-submit cmn-btn-bg-1" type="submit">
                        {{ get_static_option('support_ticket_button_text') ?: __('Submit Ticket') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@else
    @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
@endif

@endsection
