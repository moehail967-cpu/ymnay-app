{{--
    Reusable Tailwind Multi-Select component — matches the admin panel design system.

    Props:
      name          (string, required)  — form input name, submitted as name[]
      id            (string, required)  — base DOM id; must be unique per page
      options       (iterable)          — collection/array of option objects/arrays
      selected      (array)             — array of currently selected values  [default: []]
      placeholder   (string)            — trigger placeholder text
      searchPlaceholder (string)        — search input placeholder
      optionValue   (string)            — key/property used as option value    [default: 'id']
      optionLabel   (string)            — key/property used as option label    [default: 'name']
      label         (string)            — label text shown above the control   [default: null]
      required      (bool)              — show mandatory indicator              [default: false]
      disabled      (bool)              — disables interaction                  [default: false]
      class         (string)            — extra classes on the wrapper div

    Usage:
      <x-fields.tw-multiselect
          name="child_category"
          id="child_category"
          :options="$childCategories"
          :selected="$selectedChildCat ?? []"
          placeholder="Select child categories…"
          label="Child Category"
      />

    AJAX / dynamic options:
      Replace the options in the hidden <select id="{id}"> via JS and the
      widget will automatically reflect the change via MutationObserver.
--}}

@php
    $msId             = $id;
    $msName           = $name;
    $msOptions        = $options       ?? [];
    $msSelected       = $selected      ?? [];
    $msPlaceholder    = $placeholder   ?? __('Select options…');
    $msSearchPh       = $searchPlaceholder ?? __('Search…');
    $msValKey         = $optionValue   ?? 'id';
    $msLabelKey       = $optionLabel   ?? 'name';
    $msLabel          = $label         ?? null;
    $msRequired       = $required      ?? false;
    $msDisabled       = $disabled      ?? false;
    $msClass          = $class         ?? '';

    // Normalise each option to ['value' => ..., 'label' => ...]
    $msNorm = collect($msOptions)->map(function ($opt) use ($msValKey, $msLabelKey) {
        if (is_object($opt)) {
            return ['value' => $opt->{$msValKey}, 'label' => $opt->{$msLabelKey}];
        }
        return ['value' => $opt[$msValKey] ?? '', 'label' => $opt[$msLabelKey] ?? ''];
    })->filter(fn($o) => $o['value'] !== '')->values();

    $msSelectedArr = array_map('strval', (array) $msSelected);
    $dropdownId    = 'tw-ms-dropdown-' . $msId;
    $wrapperId     = 'tw-ms-wrapper-' . $msId;
    $triggerId     = 'tw-ms-trigger-' . $msId;
    $listId        = 'tw-ms-list-' . $msId;
    $searchId      = 'tw-ms-search-' . $msId;
    $emptyId       = 'tw-ms-empty-' . $msId;
    $phId          = 'tw-ms-ph-' . $msId;
@endphp

{{-- ── Label ────────────────────────────────────────────────────────────── --}}
@if($msLabel)
<label class="block text-[10px] font-bold tracking-widest uppercase mb-1.5"
       style="color: var(--color-text-muted, #6b7280)">
    {{ $msLabel }}
    @if($msRequired) <x-fields.mandatory-indicator/> @endif
</label>
@endif

{{-- ── Hidden native select (AJAX + form submission) ─────────────────────── --}}
<select name="{{ $msName }}[]" multiple id="{{ $msId }}"
        {{ $msDisabled ? 'disabled' : '' }}
        style="display:none; position:absolute; pointer-events:none;">
    @foreach($msNorm as $opt)
        <option value="{{ $opt['value'] }}"
            {{ in_array((string)$opt['value'], $msSelectedArr, true) ? 'selected' : '' }}>
            {{ $opt['label'] }}
        </option>
    @endforeach
</select>

{{-- ── Wrapper + trigger ───────────────────────────────────────────────── --}}
<div id="{{ $wrapperId }}" class="tw-ms-wrapper relative {{ $msClass }}"
     data-ms-id="{{ $msId }}" data-ms-disabled="{{ $msDisabled ? '1' : '0' }}">

    <div id="{{ $triggerId }}"
         tabindex="{{ $msDisabled ? '-1' : '0' }}"
         role="button"
         aria-haspopup="listbox"
         aria-expanded="false"
         class="tw-ms-trigger lnd-input flex flex-wrap gap-1.5 min-h-[42px] cursor-pointer
                {{ $msDisabled ? 'opacity-60 !cursor-not-allowed' : '' }}"
         style="padding: 0.4rem 0.875rem; transition: border-color .15s, box-shadow .15s;">

        <span id="{{ $phId }}"
              class="tw-ms-placeholder text-sm self-center leading-none"
              style="color: var(--color-text-subtle, #9ca3af)">
            {{ $msPlaceholder }}
        </span>
    </div>
</div>

{{-- ── Portal dropdown (appended to <body> by JS) ─────────────────────── --}}
<div id="{{ $dropdownId }}"
     class="tw-ms-dropdown"
     data-for="{{ $msId }}"
     style="display:none; position:fixed; z-index:99999; min-width:200px;">

    {{-- Search --}}
    <div class="tw-ms-search-wrap">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="flex-shrink:0; color:var(--color-text-subtle,#9ca3af)">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="{{ $searchId }}"
               class="tw-ms-search-input"
               placeholder="{{ $msSearchPh }}"
               autocomplete="off">
    </div>

    {{-- Options list --}}
    <ul id="{{ $listId }}" role="listbox" aria-multiselectable="true" class="tw-ms-list">
        {{-- populated by JS --}}
    </ul>

    {{-- Empty state --}}
    <div id="{{ $emptyId }}" class="tw-ms-empty" style="display:none">
        {{ __('No options found') }}
    </div>
</div>

{{-- ── Scoped styles (injected once per page via JS guard) ────────────── --}}
@once
<style id="tw-ms-styles">
/* ── Chip ─────────────────────────────────────────────────────────── */
.tw-ms-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px 2px 9px;
    background: var(--color-primary-soft, #e0f0f0);
    color: var(--color-primary, #1a5c4e);
    border: 1px solid var(--color-border-hover, #b0dede);
    border-radius: 9999px;
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.5;
    white-space: nowrap;
    max-width: 180px;
    cursor: default;
}
.tw-ms-chip-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tw-ms-chip-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 15px;
    height: 15px;
    border-radius: 9999px;
    background: transparent;
    color: var(--color-primary, #1a5c4e);
    opacity: .6;
    border: none;
    cursor: pointer;
    padding: 0;
    flex-shrink: 0;
    transition: opacity .15s, background .15s;
    line-height: 1;
}
.tw-ms-chip-remove:hover {
    opacity: 1;
    background: var(--color-primary-soft-hover, #adebeb);
}

/* ── Trigger focus state ──────────────────────────────────────────── */
.tw-ms-trigger:focus,
.tw-ms-trigger.is-open {
    border-color: var(--color-primary, #1a5c4e) !important;
    box-shadow: 0 0 0 3px rgba(26, 92, 78, 0.08) !important;
    outline: none;
}

/* ── Dropdown panel ───────────────────────────────────────────────── */
.tw-ms-dropdown {
    background: var(--color-bg-surface, #fff);
    border: 1px solid var(--color-border-main, #e0f0f0);
    border-radius: 0.625rem;
    box-shadow: 0 8px 30px -6px rgba(45, 106, 79, 0.14), 0 2px 8px -2px rgba(0,0,0,.08);
    overflow: hidden;
}

/* ── Search row ───────────────────────────────────────────────────── */
.tw-ms-search-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-bottom: 1px solid var(--color-border-subtle, #edf9f9);
    background: var(--color-bg-muted, #edf9f9);
}
.tw-ms-search-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    font-size: 13px;
    color: var(--color-text-main, #1a1816);
    min-width: 0;
    padding: 0;
    box-shadow: none !important;
}
.tw-ms-search-input::placeholder {
    color: var(--color-text-subtle, #9ca3af);
}

/* ── Options list ─────────────────────────────────────────────────── */
.tw-ms-list {
    max-height: 220px;
    overflow-y: auto;
    padding: 4px 0;
    margin: 0;
    list-style: none;
    scrollbar-width: thin;
    scrollbar-color: var(--color-border-hover, #b0dede) transparent;
}
.tw-ms-list::-webkit-scrollbar { width: 4px; }
.tw-ms-list::-webkit-scrollbar-track { background: transparent; }
.tw-ms-list::-webkit-scrollbar-thumb {
    background: var(--color-border-hover, #b0dede);
    border-radius: 2px;
}

/* ── Option item ──────────────────────────────────────────────────── */
.tw-ms-option {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 14px;
    cursor: pointer;
    font-size: 13px;
    color: var(--color-text-main, #1a1816);
    transition: background .1s;
    user-select: none;
}
.tw-ms-option:hover {
    background: var(--color-bg-muted, #edf9f9);
}
.tw-ms-option.tw-ms-selected {
    background: var(--color-primary-soft, #e0f0f0);
    color: var(--color-primary, #1a5c4e);
    font-weight: 500;
}
.tw-ms-option.tw-ms-selected:hover {
    background: var(--color-primary-soft-hover, #adebeb);
}

/* ── Custom checkbox ──────────────────────────────────────────────── */
.tw-ms-checkbox {
    width: 15px;
    height: 15px;
    border-radius: 4px;
    border: 1.5px solid var(--color-border-hover, #b0dede);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .15s, border-color .15s;
    background: var(--color-bg-surface, #fff);
}
.tw-ms-checkbox.checked {
    background: var(--color-primary, #1a5c4e);
    border-color: var(--color-primary, #1a5c4e);
}

/* ── Empty state ──────────────────────────────────────────────────── */
.tw-ms-empty {
    padding: 12px 14px;
    font-size: 12px;
    color: var(--color-text-subtle, #9ca3af);
    text-align: center;
}

/* ── Dark mode overrides ──────────────────────────────────────────── */
[data-theme='dark'] .tw-ms-dropdown {
    background: var(--color-bg-surface);
    border-color: var(--color-border-main);
}
[data-theme='dark'] .tw-ms-search-wrap {
    background: var(--color-bg-muted);
    border-color: var(--color-border-main);
}
[data-theme='dark'] .tw-ms-option:hover  { background: var(--color-bg-muted); }
[data-theme='dark'] .tw-ms-option.tw-ms-selected { background: rgba(26,92,78,.25); }
[data-theme='dark'] .tw-ms-chip {
    background: rgba(26,92,78,.3);
    border-color: rgba(26,92,78,.5);
    color: var(--color-primary-soft, #e0f0f0);
}
</style>

<script>
/* ── TwMultiSelect — shared runtime (loaded once) ─────────────────── */
(function () {
    if (window._TwMsReady) return;
    window._TwMsReady = true;

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function checkSvg() {
        return '<svg width="9" height="7" viewBox="0 0 9 7" fill="none">' +
            '<path d="M1 3.5L3.5 6L8 1" stroke="white" stroke-width="1.6" ' +
            'stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }

    function closeSvg() {
        return '<svg width="8" height="8" viewBox="0 0 8 8" fill="none">' +
            '<path d="M1 1l6 6M7 1L1 7" stroke="currentColor" stroke-width="1.6" ' +
            'stroke-linecap="round"/></svg>';
    }

    function TwMs(selectEl) {
        var msId    = selectEl.id;
        var wrapper = document.getElementById('tw-ms-wrapper-' + msId);
        var trigger = document.getElementById('tw-ms-trigger-' + msId);
        var drop    = document.getElementById('tw-ms-dropdown-' + msId);
        var list    = document.getElementById('tw-ms-list-' + msId);
        var search  = document.getElementById('tw-ms-search-' + msId);
        var empty   = document.getElementById('tw-ms-empty-' + msId);
        var ph      = document.getElementById('tw-ms-ph-' + msId);
        var disabled = wrapper && wrapper.dataset.msDisabled === '1';

        if (!wrapper || !trigger || !drop) return;

        // Portal: move dropdown to <body>
        document.body.appendChild(drop);

        var selected = {};
        var isOpen   = false;

        /* ── position ─────────────────────────────────────────────── */
        function position() {
            var r = trigger.getBoundingClientRect();
            drop.style.width = r.width + 'px';
            drop.style.left  = (r.left + window.scrollX) + 'px';
            var spaceBelow = window.innerHeight - r.bottom;
            var dropH = Math.min(320, drop.scrollHeight + 4);
            if (spaceBelow >= 120 || spaceBelow >= dropH) {
                drop.style.top    = (r.bottom + window.scrollY + 3) + 'px';
            } else {
                drop.style.top    = (r.top + window.scrollY - dropH - 3) + 'px';
            }
        }

        /* ── chip render ──────────────────────────────────────────── */
        function renderChips() {
            trigger.querySelectorAll('.tw-ms-chip').forEach(function (c) { c.remove(); });
            var keys = Object.keys(selected);
            ph.style.display = keys.length ? 'none' : '';
            keys.forEach(function (id) {
                var chip = document.createElement('span');
                chip.className  = 'tw-ms-chip';
                chip.dataset.id = id;
                chip.innerHTML  =
                    '<span class="tw-ms-chip-label" title="' + escHtml(selected[id]) + '">' + escHtml(selected[id]) + '</span>' +
                    '<button type="button" class="tw-ms-chip-remove" data-id="' + id + '">' + closeSvg() + '</button>';
                trigger.insertBefore(chip, ph);
            });
            syncSelect();
        }

        /* ── list render ──────────────────────────────────────────── */
        function renderList(filter) {
            filter = (filter || '').toLowerCase().trim();
            list.innerHTML = '';
            var opts    = Array.from(selectEl.options).filter(function (o) { return o.value; });
            var visible = filter ? opts.filter(function (o) { return o.text.toLowerCase().indexOf(filter) !== -1; }) : opts;
            empty.style.display = visible.length ? 'none' : '';
            visible.forEach(function (opt) {
                var sel = !!selected[opt.value];
                var li  = document.createElement('li');
                li.className = 'tw-ms-option' + (sel ? ' tw-ms-selected' : '');
                li.setAttribute('role', 'option');
                li.dataset.id   = opt.value;
                li.dataset.name = opt.text.trim();
                li.innerHTML =
                    '<span class="tw-ms-checkbox' + (sel ? ' checked' : '') + '">' +
                        (sel ? checkSvg() : '') +
                    '</span>' +
                    '<span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' +
                        escHtml(opt.text.trim()) + '</span>';
                list.appendChild(li);
            });
        }

        /* ── sync native select ───────────────────────────────────── */
        function syncSelect() {
            Array.from(selectEl.options).forEach(function (o) {
                o.selected = !!selected[o.value];
            });
            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        /* ── toggle ───────────────────────────────────────────────── */
        function toggle(id, name) {
            if (selected[id]) { delete selected[id]; } else { selected[id] = name; }
            renderChips();
            renderList(search.value);
            if (isOpen) position();
        }

        /* ── open / close ─────────────────────────────────────────── */
        function open() {
            if (isOpen || disabled) return;
            isOpen = true;
            search.value = '';
            renderList();
            drop.style.display = 'block';
            position();
            trigger.setAttribute('aria-expanded', 'true');
            trigger.classList.add('is-open');
            setTimeout(function () { search.focus(); }, 20);
        }

        function close() {
            if (!isOpen) return;
            isOpen = false;
            drop.style.display = 'none';
            trigger.setAttribute('aria-expanded', 'false');
            trigger.classList.remove('is-open');
        }

        /* ── events ───────────────────────────────────────────────── */
        trigger.addEventListener('click', function (e) {
            if (e.target.closest('.tw-ms-chip-remove')) return;
            isOpen ? close() : open();
        });
        trigger.addEventListener('click', function (e) {
            var btn = e.target.closest('.tw-ms-chip-remove');
            if (!btn) return;
            e.stopPropagation();
            delete selected[btn.dataset.id];
            renderChips();
            renderList(search.value);
            if (isOpen) position();
        });
        list.addEventListener('click', function (e) {
            var li = e.target.closest('.tw-ms-option');
            if (!li) return;
            toggle(li.dataset.id, li.dataset.name);
        });
        search.addEventListener('input', function () { renderList(search.value); });
        document.addEventListener('click', function (e) {
            if (isOpen && !wrapper.contains(e.target) && !drop.contains(e.target)) close();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen) close();
        });
        window.addEventListener('scroll', function () { if (isOpen) position(); }, true);
        window.addEventListener('resize', function () { if (isOpen) position(); });

        /* ── MutationObserver (AJAX option updates) ───────────────── */
        new MutationObserver(function () {
            var avail = {};
            Array.from(selectEl.options).forEach(function (o) {
                if (o.value) avail[o.value] = o.text.trim();
            });
            // Keep existing selections still available
            Object.keys(selected).forEach(function (id) {
                if (avail[id]) { selected[id] = avail[id]; } else { delete selected[id]; }
            });
            // Also pick up any pre-selected options injected by AJAX
            Array.from(selectEl.options).forEach(function (o) {
                if (o.selected && o.value && !selected[o.value]) {
                    selected[o.value] = o.text.trim();
                }
            });
            renderChips();
            renderList(search ? search.value : '');
        }).observe(selectEl, { childList: true });

        /* ── tw-ms:refresh — re-read native select's .selected state ─ */
        // Use after programmatically setting option.selected = true/false
        selectEl.addEventListener('tw-ms:refresh', function () {
            selected = {};
            Array.from(selectEl.options).forEach(function (o) {
                if (o.selected && o.value) selected[o.value] = o.text.trim();
            });
            renderChips();
            renderList(search ? search.value : '');
        });

        /* ── init from pre-selected ───────────────────────────────── */
        selected = {};
        Array.from(selectEl.options).forEach(function (o) {
            if (o.selected && o.value) selected[o.value] = o.text.trim();
        });
        renderChips();
    }

    /* ── Auto-init all .tw-ms-wrapper elements on DOMContentLoaded ─── */
    function initAll() {
        document.querySelectorAll('.tw-ms-wrapper[data-ms-id]').forEach(function (w) {
            var sel = document.getElementById(w.dataset.msId);
            if (sel && !w.dataset.msInit) {
                w.dataset.msInit = '1';
                TwMs(sel);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* ── Public API ────────────────────────────────────────────────── */
    // Init:    window.TwMultiSelect(selectEl)
    // Refresh: selectEl.dispatchEvent(new Event('tw-ms:refresh'))
    window.TwMultiSelect = TwMs;
})();
</script>
@endonce
