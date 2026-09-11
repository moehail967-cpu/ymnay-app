@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit With Page Builder')}} @endsection

@section('style')
    <x-summernote.css/>
    <x-pagebuilder::css/>
    <link href="{{ global_asset('assets/landlord/admin/css/nice-select.css') }}" rel="stylesheet">
    <style>
        .attachment-preview { overflow: hidden; }
        .attachment-preview .thumbnail .centered {
            position: absolute;
            top: 0;
            left: 0;
            transform: translate(50%, 50%);
            width: 100%;
            height: 100%;
        }
        .attachment-preview .thumbnail .centered img { object-fit: contain; }
    </style>
@endsection

@section('content')

{{-- Header Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-[#f3e8ff] flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-view-dashboard-edit-outline text-[#9333ea] text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{$page->title}}</h3>
            <p class="text-xs text-muted">{{__('Edit With Page Builder')}}</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{route(route_prefix().'admin.pages')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Pages')}}
            </a>
            <a href="{{route(route_prefix().'dynamic.page', $page->slug)}}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-info hover:border-info hover:text-info transition">
                <i class="mdi mdi-eye-outline text-base"></i> {{__('View')}}
            </a>
            <a href="{{route(route_prefix().'admin.pages.edit', $page->id)}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-pencil-outline text-base"></i> {{__('Edit')}}
            </a>
        </div>
    </div>
</div>

{{-- Page Builder Two-Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="p-4 sm:p-6">
                <x-pagebuilder::draggable location="dynamic_page" :page="$page"/>
            </div>
        </div>
    </div>
    <div class="lg:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="p-4 sm:p-6">
                <x-pagebuilder::widgets type="landlord"/>
            </div>
        </div>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/jquery.nice-select.min.js')}}"></script>
    <x-media-upload.tw-js/>
    <x-summernote.js/>
    <x-pagebuilder::js/>
    <x-pagebuilder::script :page="$page"/>
@endsection
