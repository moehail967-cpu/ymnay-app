@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Site Identity')}} @endsection
@section('style')
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.site.identity')}}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Site Logo --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-image-outline text-primary text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark font-urbanist">{{__('Site Logo')}}</h4>
                    <p class="text-[10px] text-muted">{{__('Main logo for headers')}}</p>
                </div>
            </div>
            <div class="px-4 py-5 flex flex-col items-center">
                <x-fields.tw-media-upload name="site_logo" title="{{__('Site Logo')}}" :id="get_static_option('site_logo')"/>
            </div>
        </div>

        {{-- White Logo --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-text-dark, #1f2937);">
                    <i class="mdi mdi-image-filter-hdr text-white text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark font-urbanist">{{__('White Logo')}}</h4>
                    <p class="text-[10px] text-muted">{{__('For dark backgrounds')}}</p>
                </div>
            </div>
            <div class="px-4 py-5 flex flex-col items-center">
                <x-fields.tw-media-upload name="site_white_logo" title="{{__('Site White Logo')}}" :id="get_static_option('site_white_logo')"/>
            </div>
        </div>

        {{-- Favicon --}}
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-star-four-points-outline text-success text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark font-urbanist">{{__('Favicon')}}</h4>
                    <p class="text-[10px] text-muted">{{__('Browser tab icon')}}</p>
                </div>
            </div>
            <div class="px-4 py-5 flex flex-col items-center">
                <x-fields.tw-media-upload name="site_favicon" title="{{__('Site Favicon')}}" :id="get_static_option('site_favicon')"/>
            </div>
        </div>

    </div>

    {{-- Save --}}
    <div class="flex items-center">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
        </button>
    </div>

</form>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
