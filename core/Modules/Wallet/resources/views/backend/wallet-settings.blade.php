@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Wallet Settings')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="max-w-2xl">
    {{-- Settings Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-wallet-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Wallet Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure wallet options for your users')}}</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="post" action="{{route('landlord.admin.wallet.settings')}}">
            @csrf

            <div class="p-4 sm:p-6 space-y-5">

                {{-- Enable/Disable Wallet --}}
                <div class="flex items-center justify-between bg-secondary border border-main rounded-xl px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-wallet-plus-outline text-success text-sm"></i>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-dark block">{{__('Enable/Disable Wallet for User')}}</span>
                            <span class="text-[11px] text-muted">{{__('Keep off to disable the wallet for all users')}}</span>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="user_wallet" class="sr-only peer" @if(!empty(get_static_option('user_wallet'))) checked @endif>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5
                                    after:transition-all peer-checked:after:translate-x-full after:shadow-sm"></div>
                    </label>
                </div>

                {{-- Info Note --}}
                <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                    <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                    <span class="text-[12px] text-dark leading-relaxed">
                        {{__('When the wallet is disabled, users will not be able to deposit, withdraw, or use their wallet balance for any transactions.')}}
                    </span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i>
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
