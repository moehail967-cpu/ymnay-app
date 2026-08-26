@extends(route_prefix()."admin.admin-master")
@section('title') {{__('Siteways Payment Gateway Settings')}} @endsection

@section('style')
    <x-media-upload.css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-credit-card text-success text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Siteways Payment Gateway')}}</h3>
            <p class="text-xs text-muted">{{__('Configure Siteways API credentials and status')}}</p>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="px-4 sm:px-6 py-5">
        <form class="forms-sample" method="post" action="{{route('sitepaymentgateway.'.route_prefix().'admin.settings')}}">
            @csrf

            <div class="space-y-5">
                {{-- API Credentials --}}
                <div>
                    <h4 class="text-xs font-bold text-muted uppercase tracking-widest mb-4">{{__('API Credentials')}}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="lnd-label">{{__('Api Key')}}</label>
                            <input type="text" class="lnd-input" name="sitesway_api_key"
                                   value="{{get_static_option('sitesway_api_key')}}"
                                   placeholder="{{__('Api Key')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Brand Id')}}</label>
                            <input type="text" class="lnd-input" name="sitesway_brand_id"
                                   value="{{get_static_option('sitesway_brand_id')}}"
                                   placeholder="{{__('Brand Id')}}">
                        </div>
                    </div>
                </div>

                {{-- Status Toggles --}}
                @php
                    $label_text = is_null(tenant()) ? 'Enable/Disable Landlord & Tenant' : 'Enable/Disable';
                    $status = is_null(tenant()) ? $sitesways->status : get_static_option('sitesway_status');
                @endphp

                <div>
                    <h4 class="text-xs font-bold text-muted uppercase tracking-widest mb-4">{{__('Gateway Status')}}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                            <div>
                                <p class="text-sm font-semibold text-dark">{{__($label_text)}}</p>
                                <p class="text-xs text-muted mt-0.5">{{__('Master switch for Siteways')}}</p>
                            </div>
                            <label class="dr-toggle">
                                <input type="checkbox" name="sitesway_status" @checked(!empty($status))>
                                <span class="dr-toggle-track"></span>
                            </label>
                        </div>

                        @if(is_null(tenant()))
                            <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Landlord Websites')}}</p>
                                    <p class="text-xs text-muted mt-0.5">{{__('Enable for landlord site')}}</p>
                                </div>
                                <label class="dr-toggle">
                                    <input type="checkbox" name="sitesway_landlord_status" @checked(!empty($sitesways->landlord))>
                                    <span class="dr-toggle-track"></span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Tenant Websites')}}</p>
                                    <p class="text-xs text-muted mt-0.5">{{__('Enable for tenant sites')}}</p>
                                </div>
                                <label class="dr-toggle">
                                    <input type="checkbox" name="sitesway_tenant_status" @checked(!empty($sitesways->admin_settings->show_admin_tenant))>
                                    <span class="dr-toggle-track"></span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Gateway Logo --}}
                <div>
                    <h4 class="text-xs font-bold text-muted uppercase tracking-widest mb-4">{{__('Gateway Logo')}}</h4>
                    <x-fields.tw-media-upload name="sitesway_logo" title="{{__('Siteways Logo')}}" :id="get_static_option('sitesway_logo')"/>
                </div>
            </div>

            {{-- Save --}}
            <div class="pt-4 mt-5 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
