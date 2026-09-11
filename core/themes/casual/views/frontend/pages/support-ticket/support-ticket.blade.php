@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Ticket') }} @endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-ticket-alt"></i> {{ __('Submit a Ticket') }}
    </div>
</div>

@if(auth()->guard('web')->check())

    @if(get_static_option('support_ticket_form_title'))
    <p class="cs-track-desc mb-4">{{ get_static_option('support_ticket_form_title') }}</p>
    @endif

    {!! theme_error_msg() !!}

    <div class="cs-dash-box">
        <div class="cs-dash-box-body">
            <form action="{{ theme_user_ticket_store_url() }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="via" value="{{ __('website') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-dash-label">{{ __('Title') }} <span class="cs-required">*</span></label>
                        <input type="text" class="cs-dash-input" name="title" placeholder="{{ __('Ticket title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-dash-label">{{ __('Subject') }} <span class="cs-required">*</span></label>
                        <input type="text" class="cs-dash-input" name="subject" placeholder="{{ __('Subject') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-dash-label">{{ __('Priority') }}</label>
                        <select name="priority" class="cs-dash-input cs-dash-select">
                            <option value="low">{{ __('Low') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="high">{{ __('High') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cs-dash-label">{{ __('Department') }}</label>
                        <select name="departments" class="cs-dash-input cs-dash-select">
                            @foreach($departments as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="cs-dash-label">{{ __('Description') }}</label>
                        <textarea name="description" class="cs-dash-input cs-dash-textarea" rows="6" placeholder="{{ __('Describe your issue…') }}"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="cs-dash-submit-btn" type="submit">
                            <i class="las la-paper-plane"></i>
                            {{ get_static_option('support_ticket_button_text') ?? __('Submit Ticket') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@else

    <div class="cs-dash-box">
        <div class="cs-dash-box-body">
            @if(get_static_option('support_ticket_login_notice'))
            <p class="cs-track-desc mb-4">{{ get_static_option('support_ticket_login_notice') }}</p>
            @endif
            @include('tenant.frontend.partials.ajax-login-form', ['title' => get_static_option('support_ticket_login_notice')])
        </div>
    </div>
    {!! theme_ajax_login_js() !!}

@endif

@endsection
