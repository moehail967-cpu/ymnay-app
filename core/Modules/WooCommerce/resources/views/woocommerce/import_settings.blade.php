@extends('tenant.admin.admin-master')
@section('title') {{__('WooCommerce Import Settings')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $units = [
        1 => __('Kg'),
        2 => __('Lb'),
        3 => __('Dozen'),
        4 => __('Ltr'),
        5 => __('g'),
        6 => __('Piece')
    ];
@endphp

<form action="{{route('tenant.admin.woocommerce.settings.import')}}" method="POST" id="importDefaultsForm">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Content --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- Card 1: Import Defaults --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                {{-- Header --}}
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cog-transfer-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Import Defaults')}}</h3>
                        <p class="text-xs text-muted">{{__('Default values applied automatically when importing products from WooCommerce')}}</p>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="p-4 sm:p-6 space-y-5">

                    {{-- Product Unit --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Product Unit')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-weight text-lg text-primary"></i>
                            <select name="woocommerce_default_unit"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 cursor-pointer">
                                <option value="">{{__('Select Unit')}}</option>
                                @foreach($units as $index => $value)
                                    <option value="{{$index}}" {{$index == get_static_option('woocommerce_default_unit') ? 'selected' : ''}}>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- UOM --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Unit of Measurement (UOM)')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-ruler text-lg text-primary"></i>
                            <input type="text" name="woocommerce_default_uom"
                                   value="{{get_static_option('woocommerce_default_uom')}}"
                                   placeholder="{{__('e.g. 1')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Card 2: CSV Import (Coming Soon) --}}
            <div class="relative">
                <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                    {{-- Header --}}
                    <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-file-delimited-outline text-warning text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('CSV Import/Export Settings')}}</h3>
                            <p class="text-xs text-muted">{{__('Default values for importing data from CSV files')}}</p>
                        </div>
                    </div>

                    {{-- Fields (disabled) --}}
                    <div class="p-4 sm:p-6 space-y-5">

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Product Unit')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 opacity-50">
                                <i class="mdi mdi-weight text-lg text-muted"></i>
                                <select disabled class="flex-1 bg-transparent text-sm text-muted outline-none border-none focus:ring-0 p-0 cursor-not-allowed">
                                    <option>{{__('Select Unit')}}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Unit of Measurement (UOM)')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 opacity-50">
                                <i class="mdi mdi-ruler text-lg text-muted"></i>
                                <input type="text" disabled placeholder="{{__('e.g. 1')}}"
                                       class="flex-1 bg-transparent text-sm text-muted placeholder-subtle outline-none border-none focus:ring-0 p-0 cursor-not-allowed">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Coming Soon Overlay --}}
                <div class="absolute inset-0 z-10 rounded-xl bg-white/70 backdrop-blur-[2px] flex items-center justify-center">
                    <span class="inline-flex items-center gap-2 bg-warning-soft text-warning px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm">
                        <i class="mdi mdi-clock-outline text-base"></i>
                        {{__('Coming Soon')}}
                    </span>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Actions')}}</h4>
                </div>

                <div class="p-4 space-y-5">

                    {{-- Info --}}
                    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                        <span class="text-[11px] text-dark leading-relaxed">{{__('These defaults are applied to every product imported from WooCommerce.')}}</span>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Settings')}}
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection
