@extends('tenant.frontend.frontend-master')

@section('title') {{ __('Restricted') }} @endsection

@section('content')
<div class="cs-restricted-page">
    <div class="cs-restricted-card">
        <div class="cs-restricted-logo">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>
        <div class="cs-restricted-icon">
            <i class="las la-lock"></i>
        </div>
        <h2 class="cs-restricted-title">{{ __('Account Restricted') }}</h2>
        <p class="cs-restricted-msg">{{ __('Your account is under review or restricted. Kindly contact admin.') }}</p>
        <a href="{{ theme_home_url() }}" class="cs-result-btn">
            <i class="las la-home"></i> {{ __('Back To Home') }}
        </a>
    </div>
</div>
@endsection
