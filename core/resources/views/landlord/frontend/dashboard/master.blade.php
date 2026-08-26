@extends('landlord.frontend.frontend-page-master')

@section('content')
    <script src="{{ asset('assets/new-landlord/js/chart.umd.min.js') }}"></script>
    <style>
        .dash-avatar {
            width: 72px !important;
            height: 48px !important;
            border-radius: 50% !important;
            border: 3px solid #fff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            /*background: #fff !important;*/
            font-size: 26px !important;
            font-weight: 800 !important;
            color: #fff !important;
            overflow: hidden !important;
        }

        /* Dynamic active nav state */
        #dashboardSidebar .nav-item.active {
            background: rgba(0, 0, 0, 0.20) !important;
        }
        #dashboardSidebar .menu-item.active-item {
            background: rgba(0, 0, 0, 0.20) !important;
            color: #fff !important;
        }
        #dashboardSidebar .bottom-nav-border {
            border-color: rgba(255, 255, 255, 0.20);
            background: rgba(0, 0, 0, 0.12);
        }
    </style>
    <section class="my-16 lg:my-36">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="relative border border-borderCS rounded-3xl">
                <!-- Mobile Backdrop -->
                <div id="backdrop"
                     class="backdrop absolute rounded-3xl hidden inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>


                     <div class="grid grid-cols-12 rounded-3xl" style="background-color: var(--section-bg-3, #F4F8FB)">

                    <aside id="dashboardSidebar"
                           class="lg:col-span-3 absolute lg:top-[78px] left-0 hidden lg:sticky lg:block lg:w-full
                                 h-[896px] rounded-l-3xl text-white z-40 transform overflow-y-auto"
                           style="background-color: var(--main-color-one, #12727C);">

                        <!-- User Profile Section -->
                        <div class="p-4 flex justify-between items-center overflow-hidden border-b">
                            <a href="{{ route('landlord.user.home.edit.profile') }}">
                                <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    @php
                                    $imageId = get_attachment_image_by_id(auth()->guard('web')->user()->image);
                                    $u = auth('web')->user();

                                    @endphp
                                    @if(!auth()->guard('web')->user()->image)
                                        <div class="dash-avatar"> {{ strtoupper(substr($u?->name ?? 'U', 0, 1)) }}</div>

                                    @else
                                        <img class="w-full h-full object-cover rounded-full"
                                         src="{{ auth()->guard('web')->user()->image ? $imageId['img_url'] : asset('assets/images/dashboradImages/users.jpg') }}"
                                         alt="{{ auth()->guard('web')->user()->name ?? '' }}">
                                    @endif
                                </div>
                                <div class="font-urbanist">
                                    <h2 class="font-semibold text-lg font-display">{{ Auth::guard('web')->user()->name ?? __('Not Given') }}</h2>
                                </div>
                            </div>
                            </a>
                            <button id="btnSideberClose" class="block lg:hidden"><i
                                    class="icon-base ti tabler-x"></i></button>
                        </div>

                        <!-- Navigation Links -->
                        <div class="flex flex-col justify-between h-full rounded-tr-3xl max-h-[806px]">

                            <nav id="navMenuLink" class="py-6 px-3 tags">
                                <a href="{{ route('landlord.user.home') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <i class="icon-base ti tabler-layout-dashboard"></i>
                                    <span class="font-medium">{{__('Dashboard')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.shops') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.shops') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <i class="icon-base ti tabler-building-store"></i>
                                    <span class="font-medium">{{__('My Shops')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.dashboard.package.order') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.dashboard.package.order') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{__('Payment Logs')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.dashboard.custom.domain') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.dashboard.custom.domain') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    <span class="font-medium">{{__('Custom Domain')}}</span>
                                </a>

                                @if(!empty(get_static_option('user_wallet')))
                                    @php
                                        $walletActive = request()->routeIs('landlord.user.wallet.*') || request()->routeIs('landlord.user.pay.commission');
                                    @endphp
                                    <div class="mb-1">
                                        <div id="toggle-btn" onclick="toggleWalletMenu()" class="cursor-pointer flex items-center justify-between w-full  rounded-xl pr-4
                                        {{ $walletActive ? 'active' : '' }}
                                        ">
                                            <div class="flex items-center gap-2.5 space-x-3 px-4 py-3">
                                                <i class="ti tabler-wallet text-white text-lg"></i>
                                                <span class="text-white font-semibold text-base">{{__('My Wallet')}}</span>
                                            </div>
                                            <button id="toggle-btn" onclick="toggleWalletMenu()"
                                                class="text-white text-xl leading-none focus:outline-none">
                                                <i id="toggle-icon" class="ti {{ $walletActive ? 'tabler-minus' : 'tabler-plus' }}"></i>
                                            </button>
                                        </div>

                                        <!-- Menu Items -->
                                        <div id="walletSubMenu" class="{{ $walletActive ? '' : 'hidden' }} pl-5">
                                            <div class="border-l border-borderCS pl-6">
                                                <a href="{{ route('landlord.user.wallet.history') }}"
                                                   class="menu-item flex items-center gap-3 px-5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('landlord.user.wallet.history') ? 'active-item' : 'text-gray-200 hover:text-white transition-colors' }}"
                                                   @if(request()->routeIs('landlord.user.wallet.history')) style="background: rgba(0,0,0,0.20); color: #fff;" @endif>
                                                    <i class="ti tabler-wallet text-lg"></i>
                                                    <span>{{__('My Wallet')}}</span>
                                                </a>

                                                <a href="{{ route('landlord.user.wallet.settings') }}"
                                                   class="menu-item flex items-center gap-3 px-5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('landlord.user.wallet.settings') ? 'active-item' : 'text-gray-200 hover:text-white transition-colors' }}"
                                                   @if(request()->routeIs('landlord.user.wallet.settings')) style="background: rgba(0,0,0,0.20); color: #fff;" @endif>
                                                    <i class="ti tabler-settings text-lg"></i>
                                                    <span>{{__('Settings')}}</span>
                                                </a>

                                                <a href="{{ route('landlord.user.wallet.withdraw') }}"
                                                   class="menu-item flex items-center gap-3 px-5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('landlord.user.wallet.withdraw') || request()->routeIs('landlord.user.wallet.new_withdrawal') ? 'active-item' : 'text-gray-200 hover:text-white transition-colors' }}"
                                                   @if(request()->routeIs('landlord.user.wallet.withdraw') || request()->routeIs('landlord.user.wallet.new_withdrawal')) style="background: rgba(0,0,0,0.20); color: #fff;" @endif>
                                                    <i class="ti tabler-history text-lg"></i>
                                                    <span>{{__('Withdraw History')}}</span>
                                                </a>

                                                <a href="{{ route('landlord.user.pay.commission') }}"
                                                   class="menu-item flex items-center gap-3 px-5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('landlord.user.pay.commission') ? 'active-item' : 'text-gray-200 hover:text-white transition-colors' }}"
                                                   @if(request()->routeIs('landlord.user.pay.commission')) style="background: rgba(0,0,0,0.20); color: #fff;" @endif>
                                                    <i class="ti tabler-currency-dollar text-lg"></i>
                                                    <span>{{__('Commission Payment')}}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <script>
                                            function toggleWalletMenu() {
                                                var subMenu = document.getElementById('walletSubMenu');
                                                var icon = document.getElementById('toggle-icon');
                                                subMenu.classList.toggle('hidden');
                                                icon.classList.toggle('tabler-plus');
                                                icon.classList.toggle('tabler-minus');
                                            }
                                        </script>
                                    </div>
                                @endif

                                <a href="{{ route('landlord.user.home.support.tickets') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.support.tickets') ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="font-medium">{{__('Support Tickets')}}</span>
                                </a>

                                @php
                                    $__uid = auth()->guard('web')->id();
                                    $siteIssueCount = \App\Models\TenantException::whereHas('tenant', fn($q) => $q->where('user_id', $__uid))
                                        ->where('domain_create_status', 0)->count()
                                        + \App\Models\Tenant::where(function($q) use ($__uid) {
                                            $q->where('user_id', $__uid)
                                              ->orWhereHas('payment_log', fn($q2) => $q2->where('user_id', $__uid));
                                        })->where(function($q) {
                                            $q->whereNull('user_id')->orWhereNull('data');
                                        })->count();
                                @endphp
                                @if($siteIssueCount > 0)
                                <a href="{{ route('landlord.user.dashboard.site.issues') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.dashboard.site.issues') ? 'active' : '' }} flex items-center justify-between px-4 py-3 rounded-lg mb-1">
                                    <div class="flex items-center space-x-3">
                                        <i class="ti tabler-alert-triangle w-5 h-5 text-base"></i>
                                        <span class="font-medium">{{__('Site Issues')}}</span>
                                    </div>
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                        {{ $siteIssueCount }}
                                    </span>
                                </a>
                                @endif
                            </nav>

                            <!-- Bottom Section -->
                            <nav class="p-3 border-t bottom-nav-border">
                                <a href="{{ route('landlord.user.home.edit.profile') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.edit.profile') ? 'active' : '' }} flex items-center space-x-3 px-4 py-2 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-sm">{{__('Edit Profile')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.home.change.password') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.change.password') ? 'active' : '' }} flex items-center space-x-3 px-4 py-2 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    <span class="text-sm">{{__('Change Password')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.logout') }}"
                                   class="nav-item flex items-center space-x-3 px-4 py-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span class="text-sm">{{__('Logout')}}</span>
                                </a>
                            </nav>
                        </div>
                    </aside>

                    <!-- Main Content -->
                    @yield('section')

                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/new-landlord/js/nav-2.js') }}"></script>
    <script src="{{ asset('assets/new-landlord/js/sidebar.js')}}"></script>
    <script src="{{ asset('assets/new-landlord/js/dashboard.js')}}"></script>
@endsection
