@extends(route_prefix().'admin.admin-master')

@section('title')
    {{__('Page Settings')}}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-file-alt text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Page Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Assign pages to different sections of your site')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.general.page.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="lnd-label">{{__('Home Page Display')}}</label>
                    <select name="home_page" class="lnd-input">
                        @foreach($all_home_pages as $page)
                            <option value="{{$page->id}}" @selected($page->id == get_static_option('home_page'))>{{$page->title}}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="lnd-label">{{__('Blog Page Display')}}</label>
                    <select name="blog_page" class="lnd-input">
                        @foreach($all_home_pages as $page)
                            <option value="{{$page->id}}" @selected($page->id == get_static_option('blog_page'))>{{$page->title}}</option>
                        @endforeach
                    </select>
                </div>

                @if(!tenant())
                    <div>
                        <label class="lnd-label">{{__('Price Plan Page Display')}}</label>
                        <select name="pricing_plan" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('pricing_plan'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Terms and Condition Page Display')}}</label>
                        <select name="terms_condition" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('terms_condition'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Privacy Policy Page Display')}}</label>
                        <select name="privacy_policy" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('privacy_policy'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(tenant())
                    <div>
                        <label class="lnd-label">{{__('Shop Page Display')}}</label>
                        <select name="shop_page" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('shop_page'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(tenant() && tenant_has_digital_product())
                    <div>
                        <label class="lnd-label">{{__('Digital Shop Page Display')}}</label>
                        <select name="digital_shop_page" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('digital_shop_page'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(tenant())
                    <div>
                        <label class="lnd-label">{{__('Order Track Page Display')}}</label>
                        <select name="track_order" class="lnd-input">
                            @foreach($all_home_pages as $page)
                                <option value="{{$page->id}}" @selected($page->id == get_static_option('track_order'))>{{$page->title}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="pt-4 mt-5 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
