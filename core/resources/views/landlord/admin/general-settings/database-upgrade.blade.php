@extends('landlord.admin.admin-master')
@section('title') {{__('Database Upgrade')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-database text-info text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Database Upgrade')}}</h3>
            <p class="text-xs text-muted">{{__('Run database migrations and upgrades')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route('landlord.admin.general.database.upgrade.settings')}}" method="POST" id="cache_settings_form" enctype="multipart/form-data">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition clear-cache-submit-btn"
                    data-value="cache">
                <i class="las la-arrow-up"></i> {{__('Database Upgrade')}}
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                $(document).on('click','.clear-cache-submit-btn',function(e){
                    $(this).html('<i class="las la-spinner la-spin"></i> {{__("Processing")}}');
                });
            });
        })(jQuery);
    </script>
@endsection
