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

    // Build initial tags array for JS
    $initial_tags = $tag_name ? array_filter(explode(',', $tag_name)) : [];
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
            <span class="normal-case tracking-normal font-normal text-subtle ml-1">{{ __('(press Enter or comma to add)') }}</span>
        </label>

        {{-- Hidden input for form submission --}}
        <input type="hidden" name="tags" id="tags_hidden_input" value="{{ $tag_name }}">

        {{-- Tailwind Tag Input Container --}}
        <div id="tw-tags-container"
             class="flex flex-wrap gap-1.5 min-h-[44px] bg-surface border border-main rounded-xl px-3 py-2 focus-within:border-primary transition cursor-text">

            {{-- Pre-existing tag chips --}}
            @foreach($initial_tags as $t)
                @php $t = trim($t); @endphp
                @if($t)
                <span class="tw-tag inline-flex items-center gap-1 pl-2.5 pr-1.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20"
                      data-tag="{{ $t }}">
                    {{ $t }}
                    <button type="button"
                            class="tw-tag-remove w-4 h-4 flex items-center justify-center rounded-full hover:bg-primary/20 transition text-primary/60 hover:text-primary leading-none"
                            title="{{ __('Remove') }}">
                        <i class="mdi mdi-close text-[10px]"></i>
                    </button>
                </span>
                @endif
            @endforeach

            {{-- Type-in input --}}
            <input type="text"
                   id="tw-tag-input"
                   class="flex-1 min-w-[140px] bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 py-0.5"
                   placeholder="{{ __('Type and press Enter...') }}"
                   autocomplete="off">
        </div>
        <p class="text-[10px] text-muted mt-1.5">{{ __('Add relevant keywords like "cotton", "summer", "casual" to improve discoverability.') }}</p>
    </div>

    {{-- Badges Section --}}
    @if(count($badges) > 0)
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-warning/10 flex items-center justify-center">
                <i class="mdi mdi-shield-star-outline text-warning text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-wide">{{ __('Product Badge') }}</h4>
                <p class="text-[10px] text-muted">{{ __('Select a badge to highlight this product') }}</p>
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
        <p class="text-[10px] text-muted mt-1.5">{{ __('Click a badge to select it. Click again to deselect.') }}</p>
    </div>
    @endif
</div>

<script>
(function () {
    var container   = document.getElementById('tw-tags-container');
    var tagInput    = document.getElementById('tw-tag-input');
    var hiddenInput = document.getElementById('tags_hidden_input');

    if (!container || !tagInput || !hiddenInput) return;

    // ── helpers ──────────────────────────────────────────────────
    function getTags() {
        return Array.from(container.querySelectorAll('.tw-tag'))
                    .map(function(el){ return el.dataset.tag; });
    }

    function syncHidden() {
        hiddenInput.value = getTags().join(',');
        // trigger change so the unsaved-changes bar activates
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function tagExists(value) {
        return getTags().some(function(t){
            return t.toLowerCase() === value.toLowerCase();
        });
    }

    function addTag(value) {
        value = value.trim().replace(/,+$/, '');
        if (!value || tagExists(value)) return;

        var span = document.createElement('span');
        span.className = 'tw-tag inline-flex items-center gap-1 pl-2.5 pr-1.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20';
        span.dataset.tag = value;
        span.innerHTML =
            value +
            '<button type="button" class="tw-tag-remove w-4 h-4 flex items-center justify-center rounded-full hover:bg-primary/20 transition text-primary/60 hover:text-primary leading-none" title="{{ __('Remove') }}">' +
                '<i class="mdi mdi-close text-[10px]"></i>' +
            '</button>';

        // insert before the text input
        container.insertBefore(span, tagInput);
        syncHidden();
    }

    function removeTag(tagEl) {
        tagEl.remove();
        syncHidden();
    }

    // ── click on container → focus input ─────────────────────────
    container.addEventListener('click', function(e) {
        if (!e.target.closest('.tw-tag-remove')) {
            tagInput.focus();
        }
    });

    // ── remove button ─────────────────────────────────────────────
    container.addEventListener('click', function(e) {
        var btn = e.target.closest('.tw-tag-remove');
        if (btn) removeTag(btn.closest('.tw-tag'));
    });

    // ── keydown: Enter / comma → add; Backspace → remove last ─────
    tagInput.addEventListener('keydown', function(e) {
        var val = tagInput.value.trim();

        if ((e.key === 'Enter' || e.key === ',') && val) {
            e.preventDefault();
            addTag(val);
            tagInput.value = '';
        } else if (e.key === 'Backspace' && !val) {
            var tags = container.querySelectorAll('.tw-tag');
            if (tags.length) removeTag(tags[tags.length - 1]);
        }
    });

    // ── also handle paste with commas ─────────────────────────────
    tagInput.addEventListener('paste', function(e) {
        e.preventDefault();
        var pasted = (e.clipboardData || window.clipboardData).getData('text');
        pasted.split(',').forEach(function(t){ addTag(t); });
        tagInput.value = '';
    });
})();
</script>
