@extends(route_prefix().'admin.admin-master')
@section('title') {{__('License Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-key text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('License Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Activate your product license')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        @php
            $licenseStatus = get_static_option('item_license_status');
            $licenseMsg = get_static_option('item_license_msg');
            $currentKey = get_static_option('site_license_key');
        @endphp

        @if($licenseStatus === 'verified')
            <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-success-soft border border-success text-success text-sm">
                <i class="las la-check-circle text-base"></i>
                <span>{{__('License is active and verified.')}}</span>
            </div>
        @endif

        <form action="{{route('landlord.admin.general.licence.settings')}}" method="POST" class="space-y-4 max-w-xl">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-dark mb-1">{{__('License Key')}}</label>
                <input type="text" name="site_license_key"
                       value="{{old('site_license_key', $currentKey)}}"
                       class="w-full border border-main rounded-xl px-4 py-2.5 text-sm text-dark bg-secondary focus:outline-none focus:ring-2 focus:ring-primary/30"
                       placeholder="{{__('Enter your license key')}}">
                @error('site_license_key')
                    <p class="text-xs text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-dark mb-1">{{__('Envato Username')}}</label>
                <input type="text" name="envato_username"
                       value="{{old('envato_username', get_static_option('license_username'))}}"
                       class="w-full border border-main rounded-xl px-4 py-2.5 text-sm text-dark bg-secondary focus:outline-none focus:ring-2 focus:ring-primary/30"
                       placeholder="{{__('Enter your Envato username')}}">
                @error('envato_username')
                    <p class="text-xs text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="las la-save"></i>
                {{__('Activate License')}}
            </button>
        </form>
    </div>
</div>

@endsection
