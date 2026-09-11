@extends('tenant.admin.admin-master')

@section('title') {{ __('FOMO Notifications — Preview') }} @endsection

@section('content')

{{-- Page header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-eye text-primary text-lg"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-dark font-urbanist">{{ __('FOMO Widget Preview') }}</h3>
            <p class="text-xs text-muted">{{ __('Simulates the purchase notification popup exactly as it appears on your storefront') }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('tenant.admin.fomo-notifications.entries') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-format-list-bulleted text-sm"></i> {{ __('Entries') }}
            </a>
            <a href="{{ route('tenant.admin.fomo-notifications.settings') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-cog-outline text-sm"></i> {{ __('Settings') }}
            </a>
        </div>
    </div>
</div>

{{-- Preview frame --}}
<div class="bg-surface rounded-xl border border-main shadow-main overflow-hidden">
    <div class="px-5 py-4 border-b border-main flex items-center justify-between">
        <div>
            <p class="text-sm font-bold text-dark">{{ __('Live Widget Preview') }}</p>
            <p class="text-xs text-muted mt-0.5">
                @if(count($syntheticEntries) > 0 && isset($syntheticEntries[0]['product_name']) && $syntheticEntries[0]['product_name'] !== 'Classic Sneakers')
                    {{ __('Using your synthetic entries — widget cycles through them below.') }}
                @else
                    {{ __('No active entries found — showing demo data. Add entries under Synthetic Entries.') }}
                @endif
            </p>
        </div>
        <button id="fomo-replay-btn"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark transition-colors">
            <i class="mdi mdi-replay text-sm"></i> {{ __('Replay') }}
        </button>
    </div>

    <div class="p-5">
        {{-- Mock storefront frame --}}
        <div id="fomo-preview-frame"
             class="relative rounded-xl border border-main overflow-hidden"
             style="min-height:480px; background:linear-gradient(135deg,#f0f4ff 0%,#fafafa 100%);">

            {{-- Fake page chrome --}}
            <div class="absolute top-0 left-0 right-0 h-12 bg-white/80 border-b border-gray-100 flex items-center px-5 gap-3">
                <div class="w-20 h-3 bg-gray-200 rounded-full"></div>
                <div class="flex gap-2 ml-auto">
                    <div class="w-12 h-2.5 bg-gray-200 rounded-full"></div>
                    <div class="w-12 h-2.5 bg-gray-200 rounded-full"></div>
                    <div class="w-12 h-2.5 bg-gray-200 rounded-full"></div>
                </div>
            </div>

            {{-- Fake product content --}}
            <div class="absolute inset-0 flex items-center justify-center flex-col gap-3 pt-12">
                <div class="w-48 h-48 rounded-2xl bg-gray-200/60"></div>
                <div class="w-32 h-3 bg-gray-200 rounded-full"></div>
                <div class="w-20 h-3 bg-gray-300 rounded-full"></div>
                <div class="w-28 h-8 bg-gray-200 rounded-xl"></div>
            </div>

            {{-- FOMO widget container — absolute inside frame --}}
            <div id="fomo-preview-widget" style="position:absolute; bottom:24px; left:24px; max-width:300px; width:calc(100% - 48px); pointer-events:none;"></div>
        </div>
    </div>
</div>

{{-- Widget styles (scoped to preview) --}}
<style>
.fomo-preview-toast {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.13);
    padding: 12px 14px;
    pointer-events: all;
    opacity: 0;
    transform: translateX(-120%);
    transition: opacity 0.35s ease, transform 0.35s ease;
    cursor: default;
}
.fomo-preview-toast.visible {
    opacity: 1;
    transform: translateX(0);
}
.fomo-preview-toast.hiding {
    opacity: 0;
    transform: translateX(-120%);
}
.fpt-img-placeholder {
    width: 48px; height: 48px; border-radius: 10px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 22px; color: #9ca3af;
}
.fpt-img { width:48px; height:48px; border-radius:10px; object-fit:cover; flex-shrink:0; }
.fpt-body { flex:1; min-width:0; }
.fpt-name { font-weight:600; font-size:13px; color:#111827; }
.fpt-name span { font-weight:400; color:#6b7280; }
.fpt-product { font-size:12px; color:#374151; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fpt-time { font-size:11px; color:#9ca3af; margin-top:2px; }
</style>

<script>
(function () {
    var entries   = @json($syntheticEntries);
    var duration  = {{ ($config['duration'] ?? 6) * 1000 }};
    var interval  = {{ ($config['interval'] ?? 15) * 1000 }};
    var delay     = 1200; // short delay for preview
    var container = document.getElementById('fomo-preview-widget');
    var idx = 0, toast = null, timer = null;

    function timeSince(m) {
        if (m < 60) return m + ' minute' + (m !== 1 ? 's' : '') + ' ago';
        var h = Math.round(m / 60);
        return h + ' hour' + (h !== 1 ? 's' : '') + ' ago';
    }

    function buildToast(e) {
        var t = document.createElement('div');
        t.className = 'fomo-preview-toast';
        var img = e.product_image
            ? '<img class="fpt-img" src="' + e.product_image + '" alt="">'
            : '<div class="fpt-img-placeholder">&#128722;</div>';
        var variant = e.variant ? ' &middot; ' + e.variant : '';
        t.innerHTML = img +
            '<div class="fpt-body">' +
              '<div class="fpt-name">' + e.name + (e.location ? ' <span>from ' + e.location + '</span>' : '') + '</div>' +
              '<div class="fpt-product">bought <strong>' + e.product_name + '</strong>' + variant + '</div>' +
              '<div class="fpt-time">' + timeSince(e.minutes_ago) + '</div>' +
            '</div>';
        return t;
    }

    function show(entry) {
        if (toast) { container.removeChild(toast); }
        toast = buildToast(entry);
        container.appendChild(toast);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.classList.add('visible'); });
        });
        timer = setTimeout(hide, duration);
    }

    function hide() {
        clearTimeout(timer);
        if (!toast) return;
        toast.classList.add('hiding');
        toast.classList.remove('visible');
        var t = toast;
        setTimeout(function () {
            if (t.parentNode) t.parentNode.removeChild(t);
            if (toast === t) toast = null;
            idx = (idx + 1) % entries.length;
            timer = setTimeout(function () { show(entries[idx]); }, interval);
        }, 350);
    }

    function start() {
        if (!entries.length) return;
        idx = 0;
        clearTimeout(timer);
        if (toast) { container.removeChild(toast); toast = null; }
        timer = setTimeout(function () { show(entries[idx]); }, delay);
    }

    start();

    document.getElementById('fomo-replay-btn').addEventListener('click', start);
})();
</script>

@endsection
