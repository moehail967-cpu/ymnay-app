@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Site Analytics') }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/analytics.css')}}">
@endsection

@section('content')

    <x-landlord-flash-msg/>
    <x-landlord-error-msg/>

    {{-- Page Header --}}
    <div class="analytics-page-head">
        <div class="page-info">
            <div class="page-icon">
                <i class="mdi mdi-poll"></i>
            </div>
            <div>
                <h2 class="page-title">{{tenant() ? __("Product Analytics") : __("Plan Analytics")}}</h2>
                <p class="page-subtitle">{{tenant() ? __('Product views, sources & user insights') : __('Subscription plan views & user insights')}}</p>
            </div>
        </div>
        @include('siteanalytics::admin.data.filter', ['type' => 'analytics'])
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        <div class="analytics-chart">
            <div class="chart-head">
                <div class="icon"><i class="mdi mdi-chart-bar"></i></div>
                <div>
                    <h3>{{tenant() ? __("Product Views") : __("Plan Views")}}</h3>
                    <p>{{ucwords(str_replace('_',' ', $period))}} {{__('overview')}}</p>
                </div>
            </div>
            <div class="chart-body">
                <div id="chart-total"></div>
            </div>
        </div>

        <div class="analytics-chart">
            <div class="chart-head">
                <div class="icon"><i class="mdi mdi-map-marker-radius-outline"></i></div>
                <div>
                    <h3>{{__("Locations & Devices")}}</h3>
                    <p>{{__('User distribution breakdown')}}</p>
                </div>
            </div>
            <div class="chart-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-center mb-2" style="color: var(--color-text-muted);">{{__('By Country')}}</p>
                        <div id="chart-country"></div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-center mb-2" style="color: var(--color-text-muted);">{{__('By Device')}}</p>
                        <div id="chart-device"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        @includeWhen(empty(tenant()) && get_static_option('site_analytics_page_view') ,'siteanalytics::admin.data.plan-card')
        @includeWhen(!empty(tenant()) && get_static_option('site_analytics_most_viewed_products') ,'siteanalytics::admin.data.product-card')
        @includeWhen(!empty(get_static_option('site_analytics_view_source')) ,'siteanalytics::admin.data.sources-card')
        @includeWhen(!empty(get_static_option('site_analytics_users_country')) ,'siteanalytics::admin.data.users-card')
        @includeWhen(!empty(get_static_option('site_analytics_users_device')) ,'siteanalytics::admin.data.devices-card')
    </div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/landlord/admin/js/apexcharts.js')}}"></script>

    @includeWhen(empty(tenant()) ,'siteanalytics::admin.partials.landlord.analytics-charts-js')
    @includeWhen(!empty(tenant()) ,'siteanalytics::admin.partials.tenant.analytics-charts-js')
@endsection
