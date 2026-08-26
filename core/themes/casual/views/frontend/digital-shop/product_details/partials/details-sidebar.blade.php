<div class="cs-digi-sidebar">

    @if($product->additionalFields?->author?->name)
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Author') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $product->additionalFields->author->name }}</h4>
    </div>
    @endif

    @if(!empty($product->additionalFields?->pages))
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Number of Pages') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $product->additionalFields->pages }}</h4>
    </div>
    @endif

    @if(!empty($product->release_date))
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Release Date') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $product->release_date->format('d M Y') }}</h4>
    </div>
    @endif

    @if(!empty($product->update_date))
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Last Updated') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $product->update_date->format('d M Y') }}</h4>
    </div>
    @endif

    @if(!empty($product->additionalFields?->high_resolution))
    @php
        $resolution = match($product->additionalFields->high_resolution) {
            'yes' => __('High Resolution'),
            'no'  => __('Low Resolution'),
        };
    @endphp
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Resolution') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $resolution }}</h4>
    </div>
    @endif

    @if(!empty($product->additionalFields?->language))
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ __('Language') }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $product->additionalFields->getLanguage?->name }}</h4>
    </div>
    @endif

    @if(!empty($product->additionalCustomFields))
    @foreach($product->additionalCustomFields ?? [] as $field)
    <div class="cs-digi-sidebar-item">
        <span class="cs-digi-sidebar-label">{{ $field->option_name }}</span>
        <h4 class="cs-digi-sidebar-value">{{ $field->option_value }}</h4>
    </div>
    @endforeach
    @endif

</div>
