@extends(route_prefix().'admin.admin-master')
@section('title') {{__('SEO Settings')}} @endsection
@section('style')
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.seo.settings')}}">
    @csrf

    {{-- Basic Meta --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-search text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('General Meta')}}</h3>
                <p class="text-xs text-muted">{{__('Default meta tags for search engines')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5 space-y-5">
            <div>
                <label class="lnd-label">{{__('Meta Author')}}</label>
                <input type="text" class="lnd-input" name="site_meta_author" value="{{get_static_option('site_meta_author')}}" placeholder="{{__('e.g. John Doe')}}">
            </div>
            <div>
                <label class="lnd-label">{{__('Meta Keywords')}}</label>
                <textarea class="lnd-input" name="site_meta_keywords" rows="2" placeholder="{{__('keyword1, keyword2, keyword3')}}">{{get_static_option('site_meta_keywords')}}</textarea>
                <p class="text-xs text-muted mt-1">{{__('Separate with commas')}}</p>
            </div>
            <div>
                <label class="lnd-label">{{__('Meta Description')}}</label>
                <textarea class="lnd-input" name="site_meta_description" rows="2" placeholder="{{__('Brief description of your site for search results')}}">{{get_static_option('site_meta_description')}}</textarea>
            </div>
        </div>
    </div>

    {{-- Social Share Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        {{-- OG Meta --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 py-3.5 border-b border-main rounded-t-xl flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <i class="las la-globe text-emerald-500 text-sm"></i>
                </div>
                <h3 class="text-xs font-bold text-dark font-urbanist">{{__('Open Graph')}}</h3>
            </div>
            <div class="px-4 py-4 space-y-4">
                <div>
                    <label class="lnd-label">{{__('Title')}}</label>
                    <input type="text" class="lnd-input" name="site_og_meta_title" value="{{get_static_option('site_og_meta_title')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Description')}}</label>
                    <textarea class="lnd-input" name="site_og_meta_description" rows="2">{{get_static_option('site_og_meta_description')}}</textarea>
                </div>
                <x-fields.tw-media-upload name="site_og_meta_image" title="{{__('Image')}}" :id="get_static_option('site_og_meta_image')"/>
            </div>
        </div>

        {{-- Facebook Meta --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 py-3.5 border-b border-main rounded-t-xl flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="lab la-facebook-f text-blue-500 text-sm"></i>
                </div>
                <h3 class="text-xs font-bold text-dark font-urbanist">{{__('Facebook')}}</h3>
            </div>
            <div class="px-4 py-4 space-y-4">
                <div>
                    <label class="lnd-label">{{__('Title')}}</label>
                    <input type="text" class="lnd-input" name="site_fb_meta_title" value="{{get_static_option('site_fb_meta_title')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Description')}}</label>
                    <textarea class="lnd-input" name="site_fb_meta_description" rows="2">{{get_static_option('site_fb_meta_description')}}</textarea>
                </div>
                <x-fields.tw-media-upload name="site_fb_meta_image" title="{{__('Image')}}" :id="get_static_option('site_fb_meta_image')"/>
            </div>
        </div>

        {{-- Twitter Meta --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 py-3.5 border-b border-main rounded-t-xl flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0">
                    <i class="lab la-twitter text-sky-500 text-sm"></i>
                </div>
                <h3 class="text-xs font-bold text-dark font-urbanist">{{__('Twitter')}}</h3>
            </div>
            <div class="px-4 py-4 space-y-4">
                <div>
                    <label class="lnd-label">{{__('Title')}}</label>
                    <input type="text" class="lnd-input" name="site_tw_meta_title" value="{{get_static_option('site_tw_meta_title')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Description')}}</label>
                    <textarea class="lnd-input" name="site_tw_meta_description" rows="2">{{get_static_option('site_tw_meta_description')}}</textarea>
                </div>
                <x-fields.tw-media-upload name="site_tw_meta_image" title="{{__('Image')}}" :id="get_static_option('site_tw_meta_image')"/>
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="las la-save"></i> {{__('Save Changes')}}
        </button>
    </div>
</form>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
