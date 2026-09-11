@extends('tenant.admin.admin-master')

@section('title')
    {{__('Inventory Settings')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-9">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cog-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Inventory Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure stock warning thresholds and messages')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{route('tenant.admin.product.inventory.settings')}}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Product Warning Threshold')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-alert-outline text-lg text-primary"></i>
                                <input type="number" name="stock_threshold_amount"
                                       value="{{get_static_option('stock_threshold_amount')}}"
                                       placeholder="{{__('example: 5')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                            <p class="text-xs text-muted mt-1.5">{{__('You will get alert notifications when any individual product stock reach to this amount')}}</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Stock Warning Message')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-message-alert-outline text-lg text-primary"></i>
                                <input type="text" name="stock_warning_message"
                                       value="{{get_static_option('stock_warning_message')}}"
                                       placeholder="{{__('Following products stock are running low')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                            <p class="text-xs text-muted mt-1.5">{{__('Your custom email notification message for stock warning')}}</p>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Settings')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
