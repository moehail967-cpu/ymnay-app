@extends("tenant.frontend.frontend-page-master")
@section("title")
    {!! $page_post->title !!}
@endsection
@section("page-title")
    {!! $page_post->title !!}
@endsection
@section("content")
    @include("tenant.frontend.partials.page-builder-content", ["page_post" => $page_post])
@endsection
