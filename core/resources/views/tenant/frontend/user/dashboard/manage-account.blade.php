@extends('tenant.frontend.user.dashboard.user-master')
@section('title') {{ __('Manage Account') }} @endsection
@section('section')
@include('themes.components.common-manage-account')
@endsection
@section('scripts')
<x-media-upload.js/>
<x-custom-js.phone-number-config selector="#phone" key="1"/>
<x-custom-js.phone-number-config selector="#address_phone" key="2"/>
@endsection
