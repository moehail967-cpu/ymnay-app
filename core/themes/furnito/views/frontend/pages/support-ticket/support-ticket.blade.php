@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Support Ticket') }} @endsection

@section('section')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="fn-dash-section-title mb-0">
        <i class="las la-ticket-alt"></i> {{ __('Support Ticket') }}
    </div>
    <a href="{{ theme_user_tickets_url() }}" class="fn-btn fn-btn-outline fn-btn-sm">
        <i class="las la-list"></i> {{ __('My Tickets') }}
    </a>
</div>

@if(auth()->guard('web')->check())

<div class="fn-dash-box">
    <div class="fn-dash-box-header">
        <strong>{{ get_static_option('support_ticket_form_title') ?? __('Submit a Ticket') }}</strong>
    </div>
    <div class="fn-dash-box-pad">
        {!! theme_error_msg() !!}
        <form action="{{ theme_user_ticket_store_url() }}" method="post" class="support-ticket-form-wrapper" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="via" value="{{ __('website') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fn-label">{{ __('Title') }} <span class="fn-required">*</span></label>
                    <input type="text" class="fn-input" name="title" placeholder="{{ __('Ticket title') }}">
                </div>
                <div class="col-md-6">
                    <label class="fn-label">{{ __('Subject') }} <span class="fn-required">*</span></label>
                    <input type="text" class="fn-input" name="subject" placeholder="{{ __('Ticket subject') }}">
                </div>
                <div class="col-md-6">
                    <label class="fn-label">{{ __('Priority') }}</label>
                    <select name="priority" class="fn-input fn-select">
                        <option value="low">{{ __('Low') }}</option>
                        <option value="medium">{{ __('Medium') }}</option>
                        <option value="high">{{ __('High') }}</option>
                        <option value="urgent">{{ __('Urgent') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="fn-label">{{ __('Departments') }}</label>
                    <select name="departments" class="fn-input fn-select">
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="fn-label">{{ __('Description') }} <span class="fn-required">*</span></label>
                    <textarea name="description" class="fn-input fn-textarea" rows="6"
                              placeholder="{{ __('Describe your issue…') }}"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="fn-btn fn-btn-gold support-ticket-submit-btn">
                        <i class="las la-paper-plane"></i>
                        {{ get_static_option('support_ticket_button_text') ?? __('Submit Ticket') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@else

<div class="fn-dash-box fn-dash-box-pad text-center fn-dash-empty">
    <i class="las la-lock"></i>
    <p class="mb-3">{{ get_static_option('support_ticket_login_notice') ?? __('Please sign in to submit a ticket.') }}</p>
    <a href="{{ theme_login_url() }}" class="fn-btn fn-btn-gold">
        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
    </a>
</div>

@endif

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.support-ticket-submit-btn', function () {
    $(this).html('<i class="las la-spinner la-spin"></i> {{ __("Submitting…") }}').prop('disabled', true);
});
</script>
@endsection
