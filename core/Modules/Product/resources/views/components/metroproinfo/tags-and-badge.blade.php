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

<div class="space-y-6">
    {{-- Tags Section --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center">
                <i class="mdi mdi-tag-multiple-outline text-primary text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __('Product Tags') }}</h4>
                <p class="text-[10px] text-muted">{{ __('Help customers find your product through search') }}</p>
            </div>
        </div>

        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
            {{ __('Tags') }}
            <span class="normal-case tracking-normal font-normal text-subtle ml-1">{{ __('(press enter to add)') }}</span>
        </label>
        <input type="text" name="tags" class="lnd-input tags_input" data-role="tagsinput" value="{{ $tag_name }}">
        <p class="text-[10px] text-muted mt-1.5">{{ __('Add relevant keywords like "cotton", "summer", "casual" to improve discoverability.') }}</p>
    </div>

    {{-- Labels Section --}}
    @if(count($badges) > 0)
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-warning/10 flex items-center justify-center">
                <i class="mdi mdi-shield-star-outline text-warning text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __('Labels') }}</h4>
                <p class="text-[10px] text-muted">{{ __('Select a label to highlight this product') }}</p>
            </div>
        </div>

        <input type="hidden" name="badge_id" value="{{ $singlebadge }}" id="badge_id_input"/>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($badges as $badge)
                <div class="badge-select-item {{ $badge->id === $singlebadge ? 'active' : '' }}" data-badge-id="{{ $badge->id }}">
                    <span class="inline-flex items-center justify-center w-6 h-6 shrink-0 rounded">
                        {!! render_image_markup_by_attachment_id($badge->badgeImage, null, 'thumb') !!}
                    </span>
                    <span class="flex-1 text-sm font-medium truncate">{{ $badge->name }}</span>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full {{ $badge->type ? 'text-success bg-success/10' : 'text-warning bg-warning/10' }}">
                        {{ $badge->type ? __('Permanent') : __('Temporary') }}
                    </span>
                </div>
            @endforeach
        </div>
        <p class="text-[10px] text-muted mt-1.5">{{ __('Click a label to select it. Click again to deselect.') }}</p>
    </div>
    @endif
</div>
