@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('404 Error Page Settings')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-exclamation-triangle text-danger text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('404 Error Page Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Customize the 404 error page appearance')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.404.page.settings')}}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="lnd-label">{{__('Title')}}</label>
                <input type="text" class="lnd-input" name="error_404_page_subtitle"
                       value="{{get_static_option('error_404_page_subtitle')}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Button Text')}}</label>
                <input type="text" class="lnd-input" name="error_404_page_button_text"
                       value="{{get_static_option('error_404_page_button_text')}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Error Page Image')}}</label>
                <x-fields.tw-media-upload name="error_image" :id="get_static_option('error_image')"/>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Settings')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
