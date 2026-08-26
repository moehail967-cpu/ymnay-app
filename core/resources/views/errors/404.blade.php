@section('title', 'Page Not Found - 404')
@section('page-title', 'Page Not Found - 404')

@include(route_prefix('frontend.partials.header'))

{{-- Tailwind + Inter font already loaded via the header partial --}}

<div class="min-h-[{{tenant() ? '65vh' : '75vh'}}] flex items-center justify-center px-6 py-16 bg-white">
    <div class="text-center max-w-xl w-full">

        @if(!empty(get_static_option('error_image')))
            <div class="mx-auto mb-6 max-w-[260px]">
                {!! render_image_markup_by_attachment_id(get_static_option('error_image')) !!}
            </div>
        @endif

        <div class="text-[10rem] font-black leading-none tracking-tight mb-6" style="color: #1a3040;">
            404
        </div>

        <h1 class="text-2xl font-bold mb-3" style="color: #1a3040;">
            {{__('Oops! Page Not Found!')}}
        </h1>

        <p class="text-gray-500 text-base leading-relaxed mb-8 max-w-md mx-auto">
            {{__("We're sorry but we can't seem to find the page you requested. This might be because you have typed the web address incorrectly.")}}
        </p>

        <a href="{{url('/')}}"
           class="inline-block px-7 py-3 text-white rounded-md font-medium text-sm transition-colors"
           style="background-color: #155e5e;"
           onmouseover="this.style.backgroundColor='#0f4545'"
           onmouseout="this.style.backgroundColor='#155e5e'">
            {{__('Back to Home')}}
        </a>

    </div>
</div>

{{--@include(route_prefix('frontend.partials.footer'))--}}
