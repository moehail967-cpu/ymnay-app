@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Site Analytics') }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/analytics.css')}}">
@endsection

@section('content')

    @if(!extension_loaded('intl'))
        <div class="rounded-xl border px-4 py-3 mb-4 flex items-center gap-2" style="background: var(--color-danger-bg); border-color: var(--color-danger);">
            <i class="mdi mdi-alert-circle text-lg" style="color: var(--color-danger);"></i>
            <p class="text-sm font-medium" style="color: var(--color-danger);">{{__('The plugin requires PHP intl extension to run it properly. Kindly enable the extension on your server.')}}</p>
        </div>
    @endif

    <x-landlord-flash-msg/>
    <x-landlord-error-msg/>

    <div class="bg-surface rounded-2xl border border-main overflow-hidden" style="box-shadow: var(--shadow-main);">

        {{-- Header --}}
        <div class="px-5 sm:px-6 py-5 border-b border-main flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--color-primary-soft);">
                <i class="mdi mdi-chart-areaspline text-lg" style="color: var(--color-primary);"></i>
            </div>
            <div>
                <h3 class="text-base font-bold font-urbanist" style="color: var(--color-text-dark);">{{__("Site Analytics")}}</h3>
                <p class="text-xs" style="color: var(--color-text-muted);">{{__("Enable tracking and manage which analytics data to collect")}}</p>
            </div>
        </div>

        <form action="{{route(route_prefix().'admin.analytics.settings')}}" method="POST">
            @csrf
            <div class="p-5 sm:p-6">

                {{-- Main Toggle --}}
                <div class="analytics-setting" style="background: var(--color-primary-soft); border-color: var(--color-border-main);">
                    <label class="analytics-switch">
                        <input type="checkbox" name="site_analytics_status" class="site_analytics_status_btn" value="on" @checked(!empty(get_static_option('site_analytics_status')))>
                        <span class="track"></span>
                    </label>
                    <div>
                        <span class="setting-label">{{__('Enable Site Analytics')}}</span>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{__('Track visitors, page views, sources and more')}}</p>
                    </div>
                </div>

                {{-- Sub-settings --}}
                <div class="settings_wrapper {{ empty(get_static_option('site_analytics_status')) ? 'hidden' : '' }}">

                    <div class="mt-6 mb-3">
                        <h4 class="text-sm font-bold" style="color: var(--color-text-dark);">{{__('Data Collection')}}</h4>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{__('Choose which analytics data to track')}}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="analytics-setting">
                            <label class="analytics-switch">
                                <input type="checkbox" name="site_analytics_unique_user" value="on" @checked(!empty(get_static_option('site_analytics_unique_user')))>
                                <span class="track"></span>
                            </label>
                            <span class="setting-label">{{__('Unique Users')}}</span>
                        </div>
                        <div class="analytics-setting">
                            <label class="analytics-switch">
                                <input type="checkbox" name="site_analytics_page_view" value="on" @checked(!empty(get_static_option('site_analytics_page_view')))>
                                <span class="track"></span>
                            </label>
                            <span class="setting-label">{{__('Page Views')}}</span>
                        </div>
                        <div class="analytics-setting">
                            <label class="analytics-switch">
                                <input type="checkbox" name="site_analytics_view_source" value="on" @checked(!empty(get_static_option('site_analytics_view_source')))>
                                <span class="track"></span>
                            </label>
                            <span class="setting-label">{{__('Traffic Sources')}}</span>
                        </div>
                        <div class="analytics-setting">
                            <label class="analytics-switch">
                                <input type="checkbox" name="site_analytics_users_country" value="on" @checked(!empty(get_static_option('site_analytics_users_country')))>
                                <span class="track"></span>
                            </label>
                            <span class="setting-label">{{__('User Countries')}}</span>
                        </div>
                        <div class="analytics-setting">
                            <label class="analytics-switch">
                                <input type="checkbox" name="site_analytics_users_device" value="on" @checked(!empty(get_static_option('site_analytics_users_device')))>
                                <span class="track"></span>
                            </label>
                            <span class="setting-label">{{__('User Devices')}}</span>
                        </div>
                    </div>

                    @tenant
                        <div class="mt-6 mb-3">
                            <h4 class="text-sm font-bold" style="color: var(--color-text-dark);">{{__('Product Analytics')}}</h4>
                            <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{__('E-commerce specific tracking')}}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="analytics-setting">
                                <label class="analytics-switch">
                                    <input type="checkbox" name="site_analytics_most_viewed_products" value="on" @checked(!empty(get_static_option('site_analytics_most_viewed_products')))>
                                    <span class="track"></span>
                                </label>
                                <span class="setting-label">{{__('Most Viewed Products')}}</span>
                            </div>
                            <div class="analytics-setting">
                                <label class="analytics-switch">
                                    <input type="checkbox" name="site_analytics_purchase_bounce_rate" value="on" @checked(!empty(get_static_option('site_analytics_purchase_bounce_rate')))>
                                    <span class="track"></span>
                                </label>
                                <span class="setting-label">{{__('Purchase Bounce Rate')}}</span>
                            </div>
                        </div>
                    @endtenant
                </div>
            </div>

            <div class="flex items-center justify-end px-5 sm:px-6 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90" style="background: var(--color-primary);">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', 'input[name=site_analytics_status]', function () {
                let el = $(this);
                if (!el.is(':checked')) {
                    $('.settings_wrapper input[type=checkbox]').prop('checked', false);
                }
                $('.settings_wrapper').toggleClass('hidden');
            });
        });
    </script>
@endsection
