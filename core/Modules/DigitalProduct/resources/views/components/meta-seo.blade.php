@php
    if(!isset($metaData)){
        $metaData = null;
    }
@endphp

<div>
    <h4 class="product-section-title">{{ __("Meta SEO") }}</h4>

    <div class="dp-meta-tabs">
        <button type="button" class="dp-meta-tab active" data-target="dp-general-meta">{{__("General meta info")}}</button>
        <button type="button" class="dp-meta-tab" data-target="dp-facebook-meta">{{__("Facebook meta")}}</button>
        <button type="button" class="dp-meta-tab" data-target="dp-twitter-meta">{{__("Twitter meta")}}</button>
    </div>

    <div id="dp-general-meta" class="dp-meta-pane">
        <h5 class="text-sm font-semibold text-dark mb-3">{{__('General Info')}}</h5>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Title")}}</label>
                <input type="text" id="general-title" value="{{ $metaData?->title }}" data-role="tagsinput" class="lnd-input tags_input" name="general_title" placeholder="{{__("General info title")}}">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Description")}}</label>
                <textarea id="general-description" name="general_description" class="lnd-input" rows="6" placeholder="{{__("General info description")}}">{{ $metaData?->description }}</textarea>
            </div>
        </div>
    </div>

    <div id="dp-facebook-meta" class="dp-meta-pane" style="display:none;">
        <h5 class="text-sm font-semibold text-dark mb-3">{{__("Facebook Info")}}</h5>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Title")}}</label>
                <input type="text" id="facebook-title" name="facebook_title" value="{{ $metaData?->fb_title }}" data-role="tagsinput" class="lnd-input tags_input" placeholder="{{__("General info title")}}">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Description")}}</label>
                <textarea id="facebook-description" name="facebook_description" class="lnd-input" rows="6" placeholder="{{__("General info description")}}">{{ $metaData?->fb_description }}</textarea>
            </div>
            <div>
                <x-fields.tw-media-upload :id="$metaData?->fb_image" :title="__('Facebook Meta Image')" :name="'facebook_image'" :dimentions="'200x200'"/>
            </div>
        </div>
    </div>

    <div id="dp-twitter-meta" class="dp-meta-pane" style="display:none;">
        <h5 class="text-sm font-semibold text-dark mb-3">{{__("Twitter Info")}}</h5>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Title")}}</label>
                <input type="text" id="twitter-title" value="{{ $metaData?->tw_title }}" name="twitter_title" data-role="tagsinput" class="lnd-input tags_input" placeholder="{{__("General info title")}}">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__("Description")}}</label>
                <textarea id="twitter-description" name="twitter_description" class="lnd-input" rows="6" placeholder="{{__("General info description")}}">{{ $metaData?->tw_description }}</textarea>
            </div>
            <div>
                <x-fields.tw-media-upload :id="$metaData?->tw_image" :title="__('Twitter Meta Image')" :name="'twitter_image'" :dimentions="'200x200'"/>
            </div>
        </div>
    </div>
</div>
