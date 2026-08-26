@extends('tenant.frontend.user.dashboard.user-master')
@section('title') {{ __('Support Ticket') }} @endsection
@section('site-title') {{ __('Support Ticket') }} @endsection
@section('section')
@include('themes.components.common-support-ticket')
@endsection
