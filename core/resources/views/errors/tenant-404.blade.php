<!DOCTYPE html>
<html dir="{{ \App\Facades\GlobalLanguage::user_lang_dir() }}"
      lang="{{ \App\Facades\GlobalLanguage::user_lang_slug() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{__('404 Not Found')}}</title>

    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/css/style.css')}}">

    @if(\App\Facades\GlobalLanguage::user_lang_dir() == 'rtl')
        <link rel="stylesheet" href="{{asset('assets/landlord/frontend/css/rtl.css')}}">
    @endif

    <script src="{{global_asset('assets/new-landlord/js/tailwind_browser.js')}}"></script>
    <script src="{{global_asset('assets/new-landlord/js/tailwind-config.js')}}"></script>
</head>
<body class="font-inter antialiased bg-white">

    <div class="min-h-screen flex items-center justify-center px-6 py-16">
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

</body>
</html>
