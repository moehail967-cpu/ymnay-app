@extends('tenant.admin.admin-master')

@section('title') {{__('Update Campaign')}} @endsection

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
    <a href="{{ route('frontend.products.campaign', $campaign->id) }}" target="_blank"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-xs font-semibold text-dark hover:bg-primary-soft hover:text-primary transition">
        <i class="mdi mdi-eye-outline text-sm"></i> {{__('Preview Campaign')}}
    </a>
</div>

@can('campaign-edit')
<form action="{{ route('tenant.admin.campaign.update') }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $campaign->id }}">

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
                            <p class="text-xs text-muted">{{__('Update campaign details')}}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="campaign_name" value="{{ $campaign->title }}" placeholder="{{__('Enter campaign name')}}" class="lnd-input" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Image')}}</label>
                        <x-fields.tw-media-upload :name="'image'" :id="$campaign?->campaignImage?->id" :dimentions="'1920x1080'"/>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Status')}}</label>
                        <select name="status" class="lnd-input">
                            <option value="draft" @if($campaign->status == 'draft') selected @endif>{{__('Draft')}}</option>
                            <option value="publish" @if($campaign->status == 'publish') selected @endif>{{__('Publish')}}</option>
                        </select>
                    </div>

                    {{-- Dates --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Campaign Duration')}} <span class="text-danger">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="block text-[10px] text-muted mb-1">{{__('Start')}}</span>
                                <input type="text" name="campaign_start_date" class="lnd-input flatpickr"
                                       value="{{ $campaign->start_date?->format('Y-m-d') }}" placeholder="{{__('From Date')}}" required>
                            </div>
                            <div>
                                <span class="block text-[10px] text-muted mb-1">{{__('End')}}</span>
                                <input type="text" name="campaign_end_date" class="lnd-input flatpickr"
                                       value="{{ $campaign->end_date?->format('Y-m-d') }}" placeholder="{{__('To Date')}}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Discount Rule --}}
                    <div class="p-3 rounded-xl border border-main bg-secondary space-y-3">
                        <span class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Discount Rule')}}</span>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-dark">
                                <input type="radio" name="discount_type" value="percentage" class="text-primary"
                                       @if($campaign->discount_type !== 'flat') checked @endif>
                                {{__('Percentage')}}
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-dark">
                                <input type="radio" name="discount_type" value="flat" class="text-primary"
                                       @if($campaign->discount_type === 'flat') checked @endif>
                                {{__('Flat Amount')}}
                            </label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="discount_value" id="discount_value" class="lnd-input flex-1"
                                   placeholder="{{__('e.g. 20')}}" min="0" step="0.01"
                                   value="{{ $campaign->discount_value }}">
                            <button type="button" id="sync_discount_btn"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90 transition whitespace-nowrap">
                                <i class="mdi mdi-sync text-sm"></i> {{__('Sync')}}
                            </button>
                        </div>
                        <p class="text-[10px] text-muted">{{__('Click Sync to apply to all products below')}}</p>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Campaign')}}
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
                @if($campaign->products->count() == 0)
                    @include("campaign::backend.add_new_campaign_product", ['remove_btn' => true])
                @endif

                @foreach ($campaign->products as $cp)
                    @php
                        $sale_price  = optional($cp->product)->sale_price ?? 1;
                        $percentage  = $sale_price > 0 ? round(100 - ($cp->campaign_price / $sale_price * 100), 1) : 0;
                    @endphp
                    <div class="cp-card bg-surface rounded-xl border border-main mb-4">
                        <div class="flex items-center justify-between px-4 py-2.5 bg-secondary border-b border-main rounded-t-xl">
                            <span class="text-xs font-bold text-dark flex items-center gap-1.5">
                                <i class="mdi mdi-package-variant-closed text-primary text-sm"></i>
                                {{ optional($cp->product)->name ?? __('Product') }}
                            </span>
                            <div class="flex items-center gap-2">
                                @if($percentage > 0)
                                    <span class="campaign-discount-badge product_percentage">-{{$percentage}}%</span>
                                @else
                                    <span class="campaign-discount-badge product_percentage" style="display:none"></span>
                                @endif
                                <button type="button" class="delete-campaign w-6 h-6 rounded-md flex items-center justify-center text-muted hover:bg-danger-soft hover:text-danger transition">
                                    <i class="mdi mdi-close text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="select_product">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Product') }}</label>
                                <input type="hidden" name="product_id[]" value="{{ $cp->product_id }}">
                                <div class="lnd-input bg-surface cursor-default text-sm text-dark py-2">
                                    {{ optional($cp->product)->name ?? '—' }}
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Original Price') }}</label>
                                    <input type="number" class="lnd-input original_price product_original_price" disabled
                                           value="{{ $sale_price }}" step="0.01">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Campaign Price') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="campaign_price[]" class="lnd-input campaign_price"
                                           value="{{ $cp->campaign_price }}" step="0.01">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Stock') }}</label>
                                    <input type="number" class="lnd-input" disabled
                                           value="{{ optional(optional($cp->product)->inventory)->stock_count ?? 0 }}">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Units for Sale') }}</label>
                                    <input type="number" name="units_for_sale[]" class="lnd-input units_for_sale"
                                           value="{{ $cp->units_for_sale }}" placeholder="∞">
                                    <span class="block text-[10px] text-muted mt-0.5">{{__('Sold:')}} {{ $cp->sold_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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

            // Remove new product card
            $(document).on('click', '.cross-btn', function () {
                $(this).closest('.cp-card').slideUp('slow', function () { $(this).remove(); });
            });

            // Delete existing campaign product row
            $(document).on('click', '.delete-campaign', function () {
                var container = $(this).closest('.cp-card');
                Swal.fire({
                    title: "{{ __('Remove this product?') }}",
                    text: "{{ __('It will be removed from the campaign on save') }}",
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Remove") }}',
                    confirmButtonColor: '#dd3333',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        container.slideUp('slow', function () { $(this).remove(); });
                    }
                });
            });

            // Sync discount to all cards
            $('#sync_discount_btn').on('click', function () {
                var val = parseFloat($('#discount_value').val()) || 0;
                if (!val) {
                    Swal.fire({ position: 'top-end', icon: 'warning', title: '{{ __("Set discount value first") }}', showConfirmButton: false, timer: 1500 });
                    return;
                }
                $('.cp-card').each(function () {
                    var orig = parseFloat($(this).find('.original_price, .product_original_price').first().val()) || 0;
                    if (orig) applyDiscountRule($(this), orig);
                });
                toastr.success('{{ __("Prices synced") }}');
            });

            // Discount badge on manual keyup
            $(document).on('keyup', '.campaign_price', function () {
                updateDiscountBadge($(this).closest('.cp-card'));
            });

            // New product card from repeater
            $('#add_product_btn').on('click', function () {
                var container = $('#product_repeater_container');
                var tpl = `@include("campaign::backend.add_new_campaign_product", ["remove_btn" => true])`;
                container.append(tpl);
                var el = container.children().last();
                el.find('.campaign_price, .units_for_sale').val('');
                el.hide().slideDown('slow');

                // Bind product select to fill price/stock
                el.find('.select_product select').on('change', function () {
                    var data = $(this).find('option:checked').data();
                    el.find('.available_num_of_units').val(data['stock']);
                    el.find('.original_price').val(data['sale_price']);
                    applyDiscountRule(el, data['sale_price']);
                });
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
                var orig = parseFloat(card.find('.original_price, .product_original_price').first().val()) || 0;
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
