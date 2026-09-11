@extends(route_prefix().'admin.admin-master')
@section('title') {{__('WooCommerce Product Import')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-lg mx-auto mt-10">
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="p-8 sm:p-10 text-center">

            {{-- Icon --}}
            <div class="w-16 h-16 rounded-full bg-danger-soft mx-auto mb-5 flex items-center justify-center">
                <i class="mdi mdi-cloud-off-outline text-danger text-3xl"></i>
            </div>

            {{-- Heading --}}
            <h3 class="text-lg font-bold text-dark font-urbanist mb-2">{{__('Connection Error')}}</h3>

            {{-- Description --}}
            <p class="text-sm text-muted leading-relaxed mb-2">{{__('Your WooCommerce credentials are incorrect or missing.')}}</p>
            <p class="text-sm text-muted leading-relaxed mb-6">{{__('Please go to WooCommerce settings and update your credentials to connect your store.')}}</p>

            {{-- Action --}}
            <a href="{{route('tenant.admin.woocommerce.settings')}}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="mdi mdi-cog-outline text-base"></i>
                {{__('Go to Settings')}}
            </a>
        </div>
    </div>
</div>

@endsection
