@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Invoice Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-9">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-receipt-text-outline text-info text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Invoice Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure invoice currency settings')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{route('landlord.admin.invoice.settings')}}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        {{-- Currency Fraction Code --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Currency Fraction Code')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-currency-usd text-lg text-primary"></i>
                                <input type="text" name="currency_fraction_code"
                                       value="{{get_static_option('currency_fraction_code') ?? 'ct'}}"
                                       placeholder="{{__('e.g. ct')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                            <p class="text-[11px] text-primary mt-1.5">{{__('Example, $100.5 - One Hundred USD and 5 Cent, 5 Cent is Fraction Here.')}}</p>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
