@extends('tenant.frontend.frontend-page-master')
@section('title')
    {!! $page_post->title !!}
@endsection
@section('page-title')
    {!! $page_post->title !!}
@endsection
@section('content')
    {{--
        Delegates to the core partial which handles:
          - New xgenious page builder (use_page_builder)
          - Legacy addon page builder (page_builder === 1)
          - Plain page content (page_content)
          - Visibility / member-only gating on all three paths

        Override this view in your theme if you need a custom wrapper
        (e.g. breadcrumb, sidebar), but keep the @include so page builder
        rendering continues to work. The partial lives in:
        resources/views/tenant/frontend/partials/page-builder-content.blade.php
    --}}
    @include('tenant.frontend.partials.page-builder-content', ['page_post' => $page_post])
@endsection
