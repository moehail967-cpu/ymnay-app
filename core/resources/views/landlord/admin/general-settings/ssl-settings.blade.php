@extends('landlord.admin.admin-master')
@section('title') {{__('SSL Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-lock text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('SSL Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Force your website to open with HTTPS')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5 flex items-start gap-2">
            <i class="las la-exclamation-triangle text-amber-500 text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-amber-700">{{__('It will force your website to open with https')}}</p>
        </div>

        <form class="forms-sample" method="post" action="{{route('landlord.admin.general.ssl.settings')}}">
            @csrf

            <div class="flex items-center justify-between mb-5">
                <label class="lnd-label mb-0">{{__('Enable SSL')}}</label>
                <label class="dr-toggle">
                    <input type="hidden" name="site_force_ssl_redirection" value="">
                    <input type="checkbox" name="site_force_ssl_redirection" value="on"
                        @checked(!empty(get_static_option('site_force_ssl_redirection')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
