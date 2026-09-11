@php
    if(!isset($tag)){
        $tag = null;
        $tag_name = "";
    }else{
        $tag_name_arr = $tag->pluck("tag_name")?->toArray();
        $tag_name = implode(",",$tag_name_arr ?? []);
    }

    if(!isset($singlebadge)){
        $singlebadge = null;
    }
@endphp

<div>
    <h4 class="product-section-title">{{ __("Product Tags and Badge") }}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Tags") }} <x-fields.mandatory-indicator/></label>
            <input type="text" name="tags" class="lnd-input tags_input" data-role="tagsinput" value="{{ $tag_name }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Labels") }}</label>
            <input type="hidden" name="badge_id" value="{{ $singlebadge }}" id="badge_id_input"/>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($badges as $badge)
                    <div class="badge-select-item {{ $badge->id === $singlebadge ? "active" : "" }}" data-badge-id="{{ $badge->id }}">
                        <span class="w-5 h-5 inline-flex items-center justify-center">
                            {{render_image_markup_by_attachment_id($badge->badgeImage, null,'thumb')}}
                        </span>
                        <span class="text-xs font-semibold">{{ $badge->name }}</span>
                        <span class="text-[9px] px-1 rounded {{ $badge->type ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning' }}">{{ $badge->type ? __("Permanent") : __("Temporary") }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
