@extends('landlord.frontend.frontend-page-master')
@section('seo_data')
    {!! SEOMeta::generate() !!}
@endsection
@section('style')
    <style>{!! $page_post->pagebuilder_generated_styles !!}</style>
@endsection
@section('content')
    @if(isset($page_post->rendered_content))
        {!! $page_post->rendered_content !!}
    @else
        @include('tenant.frontend.partials.pages-portion.dynamic-page-builder-part',['page_post' => $page_post])
    @endif
@endsection
