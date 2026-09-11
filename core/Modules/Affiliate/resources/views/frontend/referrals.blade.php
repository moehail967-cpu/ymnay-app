@extends(View::exists('theme::frontend.user.user-master') ? 'theme::frontend.user.user-master' : 'theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Referral Programme') }} @endsection
@section('page-title') {{ __('Referral Programme') }} @endsection

{{-- 'section' — bakerco, chefhome, drivekit, glowlab, goldcraft, kidville, etc --}}
@section('section')
    @include('affiliate::frontend.referrals-body')
@endsection

{{-- 'dashboard-content' — velvetlux, fitpeak, freshmart, pharmacy, trailco, electro --}}
@section('dashboard-content')
    @include('affiliate::frontend.referrals-body')
@endsection

{{-- 'dashboard_content' (underscore) — sportzone, techzone --}}
@section('dashboard_content')
    @include('affiliate::frontend.referrals-body')
@endsection

{{-- 'dash-content' — luxegems --}}
@section('dash-content')
    @include('affiliate::frontend.referrals-body')
@endsection
