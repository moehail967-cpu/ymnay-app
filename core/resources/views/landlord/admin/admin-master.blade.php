@include('landlord.admin.partials.header')
    <!-- Content Area -->
    <div class="flex-1 overflow-y-auto bg-secondary flex flex-col">
        <div class="p-4 sm:p-8 flex-1">
            <!-- Breadcrumb -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
                <h3 class="text-xl font-semibold text-dark font-urbanist">@yield('title')</h3>
                <nav aria-label="breadcrumb">
                    <ol class="flex items-center gap-2 text-sm">
                        <li>
                            <a href="{{route('landlord.admin.home')}}"
                                class="text-subtle hover:text-brand transition-colors">
                                <i class="mdi mdi-home text-base"></i>
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-main"></span>
                            <span class="text-muted">@yield('title')</span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>

        @include('landlord.admin.partials.footer')
    </div>
