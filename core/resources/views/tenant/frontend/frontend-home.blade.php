@extends('tenant.frontend.frontend-page-master')
@section('content')
    @include('tenant.frontend.partials.page-builder-content', ['page_post' => $page_post])
@endsection
