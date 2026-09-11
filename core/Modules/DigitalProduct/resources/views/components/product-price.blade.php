@php
    if (!isset($product)) {
        $product = null;
    }
    if (!isset($taxes)) {
        $taxes = [];
    }
@endphp

<div>
    <h4 class="product-section-title">{{__('Price Manage')}}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Accessibility") }}</label>
            <select name="accessibility" id="accessibility" class="lnd-input">
                <option value="paid" {{$product?->accessibility == 'paid' ? 'selected' : ''}}>{{__('Paid')}}</option>
            </select>
        </div>

        <div id="tax-price-info" style="{{$product?->accessibility == 'free' ? 'display:none' : ''}}" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Tax") }}</label>
                <select name="tax" id="tax" class="lnd-input">
                    <option value="">{{__('No tax applicable')}}</option>
                    @foreach($taxes as $tax)
                        <option value="{{$tax->id}}" {{$product?->tax == $tax->id ? 'selected' : ''}}>{{$tax->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Regular Price") }} <x-fields.mandatory-indicator/></label>
                <input type="text" class="lnd-input" value="{{ $product?->regular_price }}" name="price" placeholder="{{ __("Enter Regular Price...") }}">
                <p class="text-xs text-warning mt-1">{{ __("This price will display like this") }} <del>({{ site_currency_symbol() }}10)</del> {{', '.__('If you add sale price too')}}</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Sale Price") }}</label>
                <input type="text" class="lnd-input" value="{{ $product?->sale_price }}" name="sale_price" placeholder="{{ __("Enter Sale Price...") }}">
                <p class="text-xs text-muted mt-1">{{ __("This will be your product selling price") }}</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Free Date") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
                <input type="date" class="lnd-input flatpickr" id="free_date" value="{{ $product?->free_date ?? "" }}" name="free_date" placeholder="{{ __("Select free date...") }}">
                <p class="text-xs text-muted mt-1">{{__('This product will be free until this selected date is over')}}</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Promotional Date") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
                <input type="date" class="lnd-input flatpickr" id="promotional_date" value="{{ $product?->promotional_date ?? "" }}" name="promotional_date" placeholder="{{ __("Select promotional date...") }}">
                <p class="text-xs text-muted mt-1">{{__('Promotional discounted price will be applied on this product until this selected date is over')}}</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Promotional Price") }}</label>
                <input type="text" class="lnd-input" value="{{ $product?->promotional_price }}" name="promotional_price" placeholder="{{ __("Enter promotional price...") }}">
                <p class="text-xs text-muted mt-1">{{ __("This price will be applied on this product during the promotional time period") }}</p>
            </div>
        </div>
    </div>
</div>
