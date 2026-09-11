<style>
/* ── Themes Five (ts5) ──────────────────────────────────────── */
.ts5-section {
    background-color: #f4f6f9 !important;
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
}

/* Heading */
.ts5-head { text-align: center !important; margin-bottom: 48px !important; width: 100% !important; }
.ts5-badge {
    display: inline-block !important;
    font-size: 11px !important; font-weight: 700 !important;
    letter-spacing: 1.8px !important; text-transform: uppercase !important;
    color: var(--main-color-one, #F04751) !important;
    margin-bottom: 12px !important;
    line-height: 1 !important;
    text-align: center !important;
}
.ts5-title {
    font-size: clamp(24px, 3.5vw, 40px) !important;
    font-weight: 800 !important; color: #111 !important;
    line-height: 1.22 !important; margin: 0 0 14px !important;
    text-align: center !important;
    width: 100% !important;
}
.ts5-subtitle {
    display: block !important;
    font-size: 15px !important; color: #666 !important;
    line-height: 1.7 !important;
    max-width: 560px !important; margin: 0 auto !important;
    text-align: center !important;
}
.ts5-title-count {
    color: var(--main-color-one, #F04751) !important;
    font-weight: 900 !important;
}

/* Grid */
.ts5-grid {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 24px !important;
}
@media (max-width: 991px) { .ts5-grid { grid-template-columns: repeat(2, 1fr) !important; } }
@media (max-width: 575px) { .ts5-grid { grid-template-columns: 1fr !important; } }

/* Card */
.ts5-card {
    display: block !important; text-decoration: none !important;
    background: #fff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    transition: border-color .22s ease, box-shadow .22s ease, transform .22s ease !important;
    cursor: pointer !important;
    box-shadow: 0 1px 4px rgba(0,0,0,.06) !important;
}
.ts5-card:hover {
    border-color: var(--main-color-one, #F04751) !important;
    box-shadow: 0 10px 32px rgba(0,0,0,.12) !important;
    transform: translateY(-4px) !important;
}

/* Screenshot — fixed height, show top of page */
.ts5-card-img {
    width: 100% !important;
    height: 210px !important;
    overflow: hidden !important;
    background: #eef0f4 !important;
    display: block !important;
}
.ts5-card-img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: top center !important;
    display: block !important;
    transition: transform .5s ease !important;
}
.ts5-card:hover .ts5-card-img img { transform: scale(1.04) !important; }

.ts5-card-placeholder {
    width: 100% !important; height: 100% !important;
    display: flex !important; align-items: center !important;
    justify-content: center !important;
    color: #ccc !important; font-size: 40px !important;
}

/* Card footer */
.ts5-card-footer {
    display: flex !important; align-items: center !important;
    justify-content: space-between !important;
    padding: 14px 18px !important;
    gap: 10px !important;
    background: #fff !important;
    border-top: 1px solid #f0f4f8 !important;
}
.ts5-card-info { display: flex !important; flex-direction: column !important; gap: 3px !important; min-width: 0 !important; }
.ts5-card-name {
    font-size: 15px !important; font-weight: 700 !important; color: #111 !important;
    white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;
    display: block !important;
}
.ts5-card-cat {
    font-size: 12px !important; color: #888 !important;
    text-transform: capitalize !important; display: block !important;
}

/* Hover overlay on screenshot */
.ts5-card-img { position: relative !important; }
.ts5-card-overlay {
    position: absolute !important; inset: 0 !important;
    background: rgba(74, 124, 60, 0.82) !important;
    display: flex !important; align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: opacity .28s ease !important;
}
.ts5-card:hover .ts5-card-overlay { opacity: 1 !important; }
.ts5-card-preview {
    background: #fff !important;
    color: #3a6b2a !important;
    font-size: 16px !important; font-weight: 700 !important;
    padding: 12px 28px !important;
    border-radius: 50px !important;
    white-space: nowrap !important;
    transform: translateY(8px) !important;
    transition: transform .28s ease !important;
    box-shadow: 0 4px 16px rgba(0,0,0,.18) !important;
}
.ts5-card:hover .ts5-card-preview { transform: translateY(0) !important; }

/* Link icon — always visible */
.ts5-card-link {
    width: 34px !important; height: 34px !important;
    border-radius: 8px !important; flex-shrink: 0 !important;
    display: flex !important; align-items: center !important;
    justify-content: center !important;
    background: #f0f6ff !important;
    color: var(--main-color-one, #F04751) !important;
    font-size: 16px !important;
    transition: background .2s ease, color .2s ease !important;
}
.ts5-card:hover .ts5-card-link {
    background: var(--main-color-one, #F04751) !important;
    color: #fff !important;
}

/* CTA */
.ts5-cta { text-align: center !important; margin-top: 56px !important; }
.ts5-btn {
    display: inline-flex !important; align-items: center !important; gap: 10px !important;
    padding: 16px 40px !important;
    background: linear-gradient(135deg, var(--main-color-one, #F04751) 0%, color-mix(in srgb, var(--main-color-one, #F04751) 70%, #000) 100%) !important;
    color: #fff !important; font-size: 16px !important; font-weight: 700 !important;
    border-radius: 60px !important; text-decoration: none !important;
    letter-spacing: .3px !important;
    transition: transform .22s ease, box-shadow .22s ease !important;
    box-shadow: 0 6px 24px rgb(161 231 211 / 35%), 0 2px 8px rgba(0, 0, 0, .12) !important;
    position: relative !important;
    overflow: hidden !important;
}
/* shimmer sweep */
.ts5-btn::before {
    content: '' !important;
    position: absolute !important; inset: 0 !important;
    background: linear-gradient(100deg, transparent 25%, rgba(255,255,255,.18) 50%, transparent 75%) !important;
    background-size: 200% 100% !important;
    background-position: 200% 0 !important;
    transition: background-position .55s ease !important;
}
.ts5-btn:hover::before { background-position: -200% 0 !important; }
.ts5-btn:hover {
    color: #fff !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 24px rgba(172, 246, 225, 0.35), 0 2px 8px rgba(0, 0, 0, .12) !important;
}
.ts5-btn-num {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(255,255,255,.22) !important;
    border: 1.5px solid rgba(255,255,255,.55) !important;
    color: #fff !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    padding: 4px 12px !important;
    border-radius: 20px !important;
    letter-spacing: .5px !important;
    text-shadow: 0 1px 3px rgba(0,0,0,.15) !important;
}
.ts5-btn-arrow {
    font-size: 18px !important;
    transition: transform .22s ease !important;
    display: inline-flex !important;
}
.ts5-btn:hover .ts5-btn-arrow { transform: translateX(4px) !important; }
/* ──────────────────────────────────────────────────────────── */
</style>

<section class="ts5-section">
    <div class="container mx-auto px-8">

        {{-- Section heading --}}
        <div class="ts5-head">
            @if(!empty($badgeText))
                <span class="ts5-badge">{{ $badgeText }}</span><br>
            @endif
            <h2 class="ts5-title">{!! str_replace('{count}', '<span class="ts5-title-count">'.($totalThemes - 1).'</span>', e($title)) !!}</h2>
            @if(!empty($subtitle))
                <p class="ts5-subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Theme cards grid --}}
        @if(!empty($cards))
            <div class="ts5-grid">
                @foreach($cards as $card)
                    <a href="{{ $card['url'] }}" target="_blank" rel="noopener" class="ts5-card">
                        <div class="ts5-card-img">
                            @if(!empty($card['image']))
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy">
                            @else
                                <div class="ts5-card-placeholder">
                                    <i class="ti tabler-photo"></i>
                                </div>
                            @endif
                            <div class="ts5-card-overlay">
                                <span class="ts5-card-preview">{{ __('Preview Theme') }}</span>
                            </div>
                        </div>
                        <div class="ts5-card-footer">
                            <div class="ts5-card-info">
                                <span class="ts5-card-name">{{ $card['title'] }}</span>
                                @if(!empty($card['category']))
                                    <span class="ts5-card-cat">{{ $card['category'] }}</span>
                                @endif
                            </div>
                            <span class="ts5-card-link" aria-hidden="true">
                                <i class="ti tabler-external-link"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- CTA button --}}
        @if(!empty($buttonText) && !empty($buttonUrl))
            <div class="ts5-cta">
                <a href="{{ $buttonUrl }}" class="ts5-btn">
                    {{ __('Browse All') }}
                    <span class="ts5-btn-num">{{ $totalThemes - 1 }}+</span>
                    {{ $buttonText }}
                    <i class="ti tabler-arrow-right ts5-btn-arrow"></i>
                </a>
            </div>
        @endif

    </div>
</section>
