@extends('tenant.admin.admin-master')

@section('title') {{__('Create Campaign')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{ global_asset('assets/common/css/flatpickr.min.css') }}">
@endsection

@section('content')

<x-error-msg-tw/>
<x-flash-msg-tw/>

{{-- Top Bar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <a href="{{ route('tenant.admin.campaign.all') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:opacity-80 transition">
        <i class="mdi mdi-arrow-left text-base"></i> {{__('All Campaigns')}}
    </a>
</div>

@can('campaign-create')
<form action="{{ route('tenant.admin.campaign.new') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">

        {{-- ============ LEFT: Campaign Info ============ --}}
        <div class="xl:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-20">

                <div class="px-5 py-4 border-b border-main bg-secondary">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-bullhorn-outline text-primary text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Campaign Info')}}</h3>
                            <p class="text-xs text-muted">{{__('Basic campaign details')}}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="campaign_name" value="{{old('campaign_name')}}" placeholder="{{__('Enter campaign name')}}" class="lnd-input" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Image')}}</label>
                        <x-fields.tw-media-upload :name="'image'" :dimentions="'1920x1080'"/>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" class="lnd-input">
                            <option value="draft" {{old('status') == 'draft' ? 'selected' : ''}}>{{__('Draft')}}</option>
                            <option value="publish" {{old('status') == 'publish' ? 'selected' : ''}}>{{__('Publish')}}</option>
                        </select>
                    </div>

                    {{-- Dates --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Duration')}} <span class="text-danger">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="block text-[10px] text-muted mb-1">{{__('Start')}}</span>
                                <input type="date" name="campaign_start_date" class="lnd-input flatpickr" placeholder="{{__('From Date')}}" required>
                            </div>
                            <div>
                                <span class="block text-[10px] text-muted mb-1">{{__('End')}}</span>
                                <input type="date" name="campaign_end_date" class="lnd-input flatpickr" placeholder="{{__('To Date')}}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Discount Rule --}}
                    <div class="p-3 rounded-xl border border-main bg-secondary space-y-3">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Discount Rule')}}</span>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-dark">
                                <input type="radio" name="discount_type" value="percentage" class="text-primary" checked>
                                {{__('Percentage')}}
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-dark">
                                <input type="radio" name="discount_type" value="flat" class="text-primary">
                                {{__('Flat Amount')}}
                            </label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="discount_value" id="discount_value" class="lnd-input flex-1" placeholder="{{__('e.g. 20')}}" min="0" step="0.01" value="{{old('discount_value', 0)}}">
                            <button type="button" id="sync_discount_btn"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition whitespace-nowrap">
                                <i class="mdi mdi-sync text-sm"></i> {{__('Sync')}}
                            </button>
                        </div>
                        <p class="text-[10px] text-muted">{{__('Click Sync to apply to all products below')}}</p>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Create Campaign')}}
                    </button>
                </div>
            </div>
        </div>

        {{-- ============ RIGHT: Products ============ --}}
        <div class="xl:col-span-8">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                    <i class="mdi mdi-package-variant-closed text-primary text-base"></i>
                    {{__('Campaign Products')}}
                </h4>
                <button type="button" id="add_product_btn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-xs font-semibold text-dark hover:bg-primary-soft hover:text-primary hover:border-primary transition">
                    <i class="mdi mdi-plus text-sm"></i> {{__('Add Product')}}
                </button>
            </div>

            <div id="product_repeater_container">
                @include("campaign::backend.add_new_campaign_product")
            </div>
        </div>

    </div>
</form>
@endcan

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
    <script src="{{ global_asset('assets/common/js/flatpickr.js') }}"></script>
    <script>
    (function ($) {
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });

            // Remove product card
            $(document).on('click', '.cross-btn', function () {
                var container = $(this).closest('.cp-card');
                container.slideUp('slow');
                setTimeout(function () { container.remove(); }, 1000);
            });

            // Product selection: fill price + stock
            $(document).on('change', '.select_product select', function () {
                var container = $(this).closest('.cp-card');
                var data = $(this).find('option:checked').data();
                var product_price = data['sale_price'];

                container.find('.available_num_of_units').val(data['stock']);
                container.find('.original_price').val(product_price);

                // Auto-apply discount rule if set
                applyDiscountRule(container, product_price);
            });

            // Sync discount to all cards
            $('#sync_discount_btn').on('click', function () {
                var val = parseFloat($('#discount_value').val()) || 0;
                if (!val) {
                    Swal.fire({ position: 'top-end', icon: 'warning', title: '{{ __("Set discount value first") }}', showConfirmButton: false, timer: 1500 });
                    return;
                }
                $('.cp-card').each(function () {
                    var orig = parseFloat($(this).find('.original_price').val()) || 0;
                    if (orig) applyDiscountRule($(this), orig);
                });
                toastr.success('{{ __("Prices synced") }}');
            });

            // Discount percentage badge on manual keyup
            $(document).on('keyup', '.campaign_price', function () {
                updateDiscountBadge($(this).closest('.cp-card'));
            });

            // Add product card
            $('#add_product_btn').on('click', function () {
                var container = $('#product_repeater_container');
                var tpl = `@include("campaign::backend.add_new_campaign_product", ["remove_btn" => true])`;
                container.append(tpl);
                container.children().last().find('.campaign_price, .units_for_sale').val('');
                container.children().last().hide().slideDown('slow');
            });

            function applyDiscountRule(card, orig_price) {
                var type = $('input[name="discount_type"]:checked').val();
                var val  = parseFloat($('#discount_value').val()) || 0;
                if (!orig_price || !val) return;
                var new_price = type === 'percentage'
                    ? orig_price - (orig_price * val / 100)
                    : orig_price - val;
                if (new_price < 0) new_price = 0;
                card.find('.campaign_price').val(new_price.toFixed(2));
                updateDiscountBadge(card);
            }

            function updateDiscountBadge(card) {
                var orig = parseFloat(card.find('.original_price').val()) || 0;
                var camp = parseFloat(card.find('.campaign_price').val()) || 0;
                var badge = card.find('.product_percentage');
                if (orig && camp && camp < orig) {
                    badge.text('-' + (100 - (camp / orig * 100)).toFixed(1) + '%').show();
                } else {
                    badge.hide();
                }
            }
        });
    })(jQuery);
    </script>
@endsection
