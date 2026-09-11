@extends(View::exists('theme::frontend.user.user-master') ? 'theme::frontend.user.user-master' : 'theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Loyalty Points') }} @endsection
@section('page-title') {{ __('Loyalty Points') }} @endsection

{{-- 'section' — bakerco, chefhome, drivekit, glowlab, goldcraft, kidville --}}
@section('section')
    @include('loyalty-points::frontend.my-points-body')
@endsection

{{-- 'dashboard-content' — velvetlux, fitpeak, freshmart, pharmacy, trailco --}}
@section('dashboard-content')
    @include('loyalty-points::frontend.my-points-body')
@endsection

{{-- 'dashboard_content' (underscore) — sportzone, techzone --}}
@section('dashboard_content')
    @include('loyalty-points::frontend.my-points-body')
@endsection

{{-- 'dash-content' — luxegems --}}
@section('dash-content')
    @include('loyalty-points::frontend.my-points-body')
@endsection
