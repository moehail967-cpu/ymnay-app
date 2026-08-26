@extends('landlord.frontend.frontend-page-master')
@section('page-title')
    {{__('Deposit Cancelled')}}
@endsection
@section('content')
    <section class="my-10">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-lg mx-auto">

                {{-- Cancel Icon --}}
                <div class="flex flex-col items-center justify-center text-center mb-8">
                    <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mb-5">
                        <i class="ti tabler-circle-x text-[2.5rem] text-yellow-500"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{__('Deposit Cancelled')}}</h1>
                    <p class="text-gray-500 mt-2 text-sm sm:text-base">{{__('Your deposit has been canceled. No amount has been deducted.')}}</p>
                </div>

                {{-- Alert Card --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-6 py-5 mb-8 flex items-start gap-4">
                    <i class="ti tabler-alert-triangle text-[1.5rem] text-yellow-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800 text-sm sm:text-base">{{__('Transaction Not Completed')}}</h3>
                        <p class="text-yellow-700 text-sm mt-1">{{__('Your deposit request was cancelled. If this was a mistake, please try again from your wallet.')}}</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap justify-center gap-3">
                    @if(Auth::guard('web')->check())
                        <a href="{{route('landlord.user.wallet.history')}}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:opacity-90 transition-opacity text-sm">
                            <i class="ti tabler-wallet"></i>
                            {{__('Back To Wallet')}}
                        </a>
                    @endif
                    <a href="{{url('/')}}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-sm">
                        <i class="ti tabler-home"></i>
                        {{__('Back To Home')}}
                    </a>
                </div>

            </div>
        </div>
    </section>
@endsection
