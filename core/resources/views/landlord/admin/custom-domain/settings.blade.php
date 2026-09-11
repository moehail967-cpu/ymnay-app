@extends('landlord.admin.admin-master')

@section('title')
    {{__('Custom Domain Settings')}}
@endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-globe text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Custom Domain Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Configure domain setup instructions for tenants')}}</p>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="px-4 sm:px-6 py-5">
        <form action="{{route('landlord.admin.custom.domain.requests.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="lnd-label">{{__('Title')}}</label>
                <input type="text" name="custom_domain_settings_title" class="lnd-input"
                       value="{{get_static_option_central('custom_domain_settings_title') ?? get_static_option('custom_domain_settings_title')}}">
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Description')}}</label>
                <textarea name="custom_domain_settings_description" class="lnd-input" cols="30" rows="6">{{get_static_option_central('custom_domain_settings_description') ?? get_static_option('custom_domain_settings_description')}}</textarea>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Table Info Data Title')}}</label>
                <input type="text" name="custom_domain_table_title" class="lnd-input"
                       value="{{get_static_option_central('custom_domain_table_title') ?? get_static_option('custom_domain_table_title')}}">
            </div>

            {{-- DNS Records Section --}}
            <div class="bg-secondary rounded-xl border border-main overflow-hidden mb-5">
                <div class="px-4 py-3 border-b border-main flex items-center gap-2">
                    <i class="las la-server text-primary"></i>
                    <h4 class="text-sm font-bold text-dark">{{__('DNS Record Configuration')}}</h4>
                </div>
                <div class="p-4 space-y-4">
                    {{-- Record Row 1 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Type One')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{ __('CNAME Record') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Host One')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="www">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Value One')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{env('CENTRAL_DOMAIN')}}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('TTL One')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{ __('Automatic') }}">
                        </div>
                    </div>

                    {{-- Record Row 2 --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Type Two')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{ __('CNAME Record') }}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Host Two')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="@">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('Value Two')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{env('CENTRAL_DOMAIN')}}">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-muted block mb-1">{{__('TTL Two')}}</label>
                            <input type="text" readonly class="lnd-input bg-muted" value="{{ __('Automatic') }}">
                        </div>
                    </div>

                    {{-- Cloudflare A Record --}}
                    <div class="pt-3 border-t border-main">
                        <p class="text-xs text-muted mb-3">
                            <i class="las la-cloud text-info mr-0.5"></i>
                            {{__('Use this if you are using Cloudflare')}}
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <input class="lnd-input bg-muted" value="{{ __('A Record') }}" disabled>
                            </div>
                            <div>
                                <input class="lnd-input bg-muted" value="@" disabled>
                            </div>
                            <div>
                                <input type="text" class="lnd-input server-ip" name="server_ip"
                                       value="{{get_static_option_central('server_ip') ?? $_SERVER['SERVER_ADDR']}}" readonly>
                            </div>
                            <div>
                                <input class="lnd-input bg-muted" value="{{ __('Automatic') }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label class="lnd-label">{{__('Screen Shot Example')}}</label>
                <x-fields.tw-media-upload name="custom_domain_settings_screem_show_image"
                                          :id="get_static_option_central('custom_domain_settings_screem_show_image') ?? get_static_option('custom_domain_settings_screem_show_image')"/>
            </div>

            <div class="pt-4 mt-4 border-t border-main">
                <button type="submit" id="update"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-summernote.js/>
    <x-media-upload.tw-js/>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.server-ip', function () {
                $(this).attr('readonly', false);
            });
        });
    </script>
@endsection
