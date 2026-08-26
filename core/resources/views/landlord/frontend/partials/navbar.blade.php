@php
    $currentPath = request()->path();
    $isHomePage = in_array($currentPath, ['/', 'home', 'home-2']);
    $logoKey = $isHomePage ? 'site_white_logo' : 'site_logo';
    $logo_type = render_image_markup_by_attachment_id(get_static_option($logoKey), 'w-28 h-9 rounded-xl')
        ?: render_image_markup_by_attachment_id(get_static_option('site_logo'), 'w-28 h-9 rounded-xl')
        ?: render_image_markup_by_attachment_id(get_static_option('site_white_logo'), 'w-28 h-9 rounded-xl');
    $site_title = get_static_option('site_'.get_user_lang().'_title') ?: get_static_option('site_title');
    $navbarClass = $isHomePage ? 'navbar-home' : 'navbar-inner';
    $navBtnClass = $isHomePage ? 'nav-auth-btn' : 'site-btn';
    $navBtnClassTwo = $isHomePage ? 'nav-mobile-auth-btn_two': 'site-secondary-btn';

    $featuredPlan = \App\Models\PricePlan::where('status', 1)
        ->whereNotNull('package_badge')
        ->where('package_badge', '!=', '')
        ->orderBy('id')
        ->first()
        ?? \App\Models\PricePlan::where('status', 1)->orderBy('id')->first();
    $getStartedRoute = $featuredPlan
        ? route('landlord.frontend.plan.order', $featuredPlan->id)
        : route('landlord.user.login');
@endphp

<header class="fixed top-0 left-0 w-full z-50 {{ $navbarClass }}">
    <div id="mainNav" class="pt-2 pb-2 lg:pb-4 transition-all duration-300 ease-in-out">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <nav>
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="z-50">
                        <a href="{{ url('/') }}">
                            @if($logo_type)
                                {!! $logo_type !!}
                            @else
                                <h2 class="site-title text-xl font-extrabold tracking-tight">{{ $site_title }}</h2>
                            @endif
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center justify-center gap-8">
                        <ul class="flex items-center justify-center gap-8">
                            {!! render_frontend_desktop_menu($primary_menu) !!}
                        </ul>
                    </div>

                    <!-- Desktop Auth Buttons -->
                    <div class="hidden lg:block">
                        <div class="flex items-center justify-center gap-4">
                            @if(Auth::guard('web')->check())
                                @php
                                    $route = auth()->guest() == 'admin' ? route('landlord.admin.dashboard') : route('landlord.user.home');
                                @endphp
                                <a href="{{ $route }}"
                                   class="{{ $navBtnClassTwo }} font-medium px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] flex items-center gap-2 transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95">
                                    {{ get_static_option('default_dashboard_text') ?? __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('landlord.user.login') }}"
                                   class="{{ $navBtnClassTwo }} px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] font-medium transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95">
                                    {{ __('Login') }}
                                </a>
                                <a href="{{ $getStartedRoute }}"
                                   class="{{ $navBtnClass }} font-medium px-4 sm:px-6 py-2 sm:py-2.5 lg:py-2.5 lg:px-6 text-sm sm:text-base rounded-[8px] flex items-center gap-2 transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-105 active:scale-95">
                                    {{ get_static_option('default_register_text') ?? __('Create Store') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button id="menuToggle" class="lg:hidden z-50 w-10 h-10 flex items-center justify-center">
                        <i id="menuIcon" class="ti tabler-menu-2 text-[28px]"></i>
                    </button>
                </div>
            </nav>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <!-- Mobile Sidebar -->
    <div id="sidebar"
         class="fixed top-0 right-0 h-full w-70 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300">
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-6 border-b border-[hsl(210,10%,84%)]">
                <a href="{{ url('/') }}">
                    @if($logo_type)
                        {!! $logo_type !!}
                    @else
                        <h2 class="site-title">{{ $site_title }}</h2>
                    @endif
                </a>
                <button id="closeBtn" class="w-8 h-8 flex items-center justify-center">
                    <i class="ti tabler-x text-[24px] text-secondarys"></i>
                </button>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <ul class="space-y-6">
                    {!! render_frontend_mobile_menu($primary_menu) !!}
                </ul>

                <div class="mt-8 space-y-4">
                    @if(Auth::guard('web')->check())
                        @php
                            $route = auth()->guest() == 'admin' ? route('landlord.admin.dashboard') : route('landlord.user.home');
                        @endphp
                        <a href="{{ $route }}"
                           class="nav-mobile-auth-btn_two w-full font-medium px-6 py-2.5 rounded-full flex items-center justify-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            {{ get_static_option('default_dashboard_text') ?? __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('landlord.user.login') }}"
                           class="nav-mobile-auth-btn_two w-full font-medium px-6 py-2.5 rounded-full flex items-center justify-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ $getStartedRoute }}"
                           class="nav-mobile-auth-btn w-full font-medium px-6 py-2.5 rounded-full flex items-center justify-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                            {{ get_static_option('default_register_text') ?? __('Create Store') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
