@extends('landlord.frontend.frontend-page-master')

@section('style')
    <script src="{{ asset('assets/new-landlord/js/chart.umd.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.min.js"></script>
@endsection

@section('content')
    <section class="my-36">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="relative border border-borderCS rounded-3xl">
                <!-- Mobile Backdrop -->
                <div id="backdrop"
                     class="backdrop absolute rounded-3xl hidden inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>

                <div class="grid grid-cols-12 bg-[#F8FAFB] rounded-3xl pb-12">

                    <aside id="dashboardSidebar"
                           class="lg:col-span-3 absolute lg:top-[78px] left-0 hidden lg:sticky lg:block lg:w-full
                                 h-[896px] rounded-tl-3xl bg-[#F8FAFB] bg-aside gradient-bg text-white z-40 transform overflow-y-auto">

                        <!-- User Profile Section -->
                        <div class="p-5 flex justify-between items-center overflow-hidden border-b border-[#12727C]">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    <img class="w-full h-full object-cover rounded-full"
                                         src="{{ auth()->guard('web')->user()->image ? asset('assets/landlord/uploads/media-uploader/' . auth()->guard('web')->user()->image) : asset('assets/images/dashboradImages/users.jpg') }}"
                                         alt="{{ auth()->guard('web')->user()->name ?? '' }}">
                                </div>
                                <div class="font-urbanist">
                                    <h2 class="font-semibold text-lg font-display">{{ Auth::guard('web')->user()->name ?? __('Not Given') }}</h2>
                                </div>
                            </div>
                            <button id="btnSideberClose" class="block lg:hidden"><i
                                    class="icon-base ti tabler-x"></i></button>
                        </div>

                        <!-- Navigation Links -->
                        <div class="flex flex-col justify-between h-full rounded-tr-3xl max-h-[806px]">

                            <nav id="navMenuLink" class="py-6 px-3 tags">
                                <a href="{{ route('landlord.user.home') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <i class="icon-base ti tabler-layout-dashboard"></i>
                                    <span class="font-medium">{{__('Dashboard')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.dashboard.package.order') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.dashboard.package.order') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{__('Payment Logs')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.dashboard.custom.domain') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.dashboard.custom.domain') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    <span class="font-medium">{{__('Custom Domain')}}</span>
                                </a>

                                @if(!empty(get_static_option('user_wallet')))
                                    <a href="{{ route('landlord.user.wallet.history') }}"
                                       class="nav-item {{ request()->routeIs('landlord.user.wallet.*') || request()->routeIs('landlord.user.pay.commission') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span class="font-medium">{{__('My Wallet')}}</span>
                                        <button class="ml-auto text-teal-200 hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </a>
                                @endif

                                <a href="{{ route('landlord.user.home.support.tickets') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.support.tickets') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-3 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="font-medium">{{__('Support Tickets')}}</span>
                                </a>
                            </nav>

                            <!-- Bottom Section -->
                            <nav class="p-3 border-t border-teal-600 bg-teal-800 bg-opacity-30">
                                <a href="{{ route('landlord.user.home.edit.profile') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.edit.profile') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-2 rounded-lg mb-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-sm">{{__('Edit Profile')}}</span>
                                </a>

                                <a href="{{ route('landlord.user.home.change.password') }}"
                                   class="nav-item {{ request()->routeIs('landlord.user.home.change.password') ? 'active bg-[#12727C]' : '' }} flex items-center space-x-3 px-4 py-2 rounded-lg mb-1">
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
                    <x-error-msg-tw/>
                    <x-flash-msg-tw/>
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
