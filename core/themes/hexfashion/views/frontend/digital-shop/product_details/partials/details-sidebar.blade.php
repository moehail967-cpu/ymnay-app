<div class="hf-digital-pd-sidebar">
    <div class="hf-dash-card">
        <h4 class="hf-dash-card-title">
            <i class="las la-info-circle"></i> {{ __('Book Details') }}
        </h4>

        @if(!empty($product->additionalFields?->author?->name))
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Author') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $product->additionalFields?->author?->name }}</span>
            </div>
        @endif

        @if(!empty($product->additionalFields?->pages))
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Number of pages') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $product->additionalFields?->pages }}</span>
            </div>
        @endif

        @if(!empty($product->release_date))
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Release Date') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $product->release_date->format('d M Y') }}</span>
            </div>
        @endif

        @if(!empty($product->update_date))
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Update Date') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $product->update_date->format('d M Y') }}</span>
            </div>
        @endif

        @if(!empty($product->additionalFields?->high_resolution))
            @php
                $resolution = match($product->additionalFields?->high_resolution) {
                    'yes' => __('High Resolution'),
                    'no'  => __('Low Resolution'),
                    default => ''
                };
            @endphp
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Resolution') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $resolution }}</span>
            </div>
        @endif

        @if(!empty($product->additionalFields?->language))
            <div class="hf-digital-pd-sidebar-row">
                <span class="hf-digital-pd-sidebar-key">{{ __('Language') }}</span>
                <span class="hf-digital-pd-sidebar-val">{{ $product->additionalFields?->getLanguage?->name }}</span>
            </div>
        @endif

        @if(!empty($product->additionalCustomFields))
            @foreach($product->additionalCustomFields ?? [] as $customField)
                <div class="hf-digital-pd-sidebar-row">
                    <span class="hf-digital-pd-sidebar-key">{{ $customField->option_name }}</span>
                    <span class="hf-digital-pd-sidebar-val">{{ $customField->option_value }}</span>
                </div>
            @endforeach
        @endif
    </div>
</div>
