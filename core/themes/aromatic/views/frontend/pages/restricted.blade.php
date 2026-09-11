@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Restricted') }} @endsection
@section('page-title') {{ __('Restricted') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Restricted') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Restricted') }}</span>
        </div>
    </div>
</div>
<div class="container">
    <div class="ar-restricted-wrap">
        {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        <p class="ar-restricted-msg">{{ __('Your account is under review or restricted. Kindly contact admin') }}</p>
        <a href="{{ theme_home_url() }}" class="ar-btn ar-btn-outline mt-4">
            <i class="mdi mdi-home-outline"></i> {{ __('Back to Home') }}
        </a>
    </div>
</div>
@endsection
