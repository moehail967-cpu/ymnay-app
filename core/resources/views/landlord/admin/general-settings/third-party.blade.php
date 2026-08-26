@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Third Party Scripts Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-code text-info text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Third Party Scripts')}}</h3>
            <p class="text-xs text-muted">{{__('Add tracking codes and third-party scripts')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.general.third.party.script.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="lnd-label">{{__('Third Party API Code')}}</label>
                <textarea name="site_third_party_tracking_code" id="site_third_party_tracking_code" cols="30" rows="10"
                          class="lnd-input font-mono text-xs" style="min-height: 200px;">{{get_static_option('site_third_party_tracking_code')}}</textarea>
                <p class="text-xs text-muted mt-1.5">{{__('This code will be loaded before </head> tag')}}</p>
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
