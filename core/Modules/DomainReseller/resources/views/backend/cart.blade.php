@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Domain Reseller Plugin') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-file-contract text-warning text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Agreement to the Policy')}}</h3>
            <p class="text-xs text-muted">{{__('To proceed further, kindly read and agree to our policies')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        @if($agreements['status'])
            <div class="dr-agreement-scroll mb-5">
                @foreach($agreements['result'] ?? [] as $item)
                    <div class="{{$loop->last ? 'mt-4' : 'mb-4'}}">
                        <span class="dr-agreement-key">{{$item->agreementKey}}</span>
                        <div class="text-sm text-muted leading-relaxed mt-2">{!! $item->content !!}</div>
                    </div>
                @endforeach
            </div>
        @else
            <script>location.reload();</script>
        @endif

        <div class="flex items-center justify-between pt-4 border-t border-main">
            <label class="flex items-center gap-2 cursor-pointer">
                <input class="agreement-check w-4 h-4 rounded border-2 border-main accent-primary" type="checkbox" id="flexCheckDefault">
                <span class="text-sm text-dark font-medium">{{__('I agree to the policies')}}</span>
            </label>
            <a class="to-checkout-btn inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition"
               href="{{route('tenant.admin.domain-reseller.checkout')}}">
                {{__('Continue to Checkout')}}
                <i class="las la-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            $(document).on('click', '.to-checkout-btn', function (e) {
                e.preventDefault();

                if (!$('.agreement-check').is(':checked')) {
                    toastr.warning('{{__("You have to agree to the policies to continue.")}}');
                    return false;
                }

                location.href = $(this).attr('href');
            });
        })(jQuery);
    </script>
@endsection
