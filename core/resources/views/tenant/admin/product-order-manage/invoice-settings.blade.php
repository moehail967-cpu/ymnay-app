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
                    <p class="text-xs text-muted">{{__('Configure invoice settings')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{route(route_prefix().'admin.product.invoice.settings')}}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Invoice Serial Number Padding')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-numeric text-lg text-primary"></i>
                                <input type="number" name="invoice_number_padding"
                                       value="{{get_static_option('invoice_number_padding') ?? ''}}"
                                       placeholder="{{__('2')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                            <p class="text-[11px] text-muted mt-1.5">{{__('Sequence will be padded accordingly, for ex. Serial No. 00001')}}</p>
                        </div>

                        <div>
                            @php
                                $options = ['each' => __('Each Product tax'), 'total' => __('Total Tax')];
                            @endphp
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Invoice Tax Position')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-calculator-variant text-lg text-primary"></i>
                                <select name="invoice_tax_position" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    @foreach($options as $index => $option)
                                        <option value="{{$index}}" {{get_static_option('invoice_tax_position') == $index ? 'selected' : ''}}>{{$option}}</option>
                                    @endforeach
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                            <p class="text-[11px] text-muted mt-1.5">{{__('It will determine where the tax will be shown')}}</p>
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
