<style>
    /* ═══════════════════════════════════════════════
       SECTION
       ─────────────────────────────────────────────── */
    #{{ $widgetId }} {
        background-color: {{ $sectionBg ?: 'var(--section-bg-1, #ffffff)' }};
    }
    #{{ $widgetId }} .pc-section-title {
        color: {{ $sectionTitleColor ?: 'var(--heading-color, #1f2937)' }};
    }

    /* ═══════════════════════════════════════════════
       CARD — NORMAL STATE
       Auto-follows global Color Settings vars.
       ─────────────────────────────────────────────── */
    #{{ $widgetId }} .pc-card {
        background-color: var(--section-bg-1, #ffffff);
        border-color: {{ $cardBorderColor ?: 'var(--extra-light-color, #e5e7eb)' }};
        transition: background-color 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }
    #{{ $widgetId }} .pc-title {
        color: {{ $cardHeadingColor ?: 'var(--heading-color, #1f2937)' }};
    }
    #{{ $widgetId }} .pc-price {
        color: {{ $cardHeadingColor ?: 'var(--heading-color, #1f2937)' }};
    }
    #{{ $widgetId }} .pc-body,
    #{{ $widgetId }} .pc-period,
    #{{ $widgetId }} .pc-feature {
        color: {{ $cardBodyColor ?: 'var(--body-color, #666666)' }};
    }
    #{{ $widgetId }} .pc-divider {
        border-color: {{ $cardDividerColor ?: 'var(--extra-light-color, #e5e7eb)' }};
    }
    #{{ $widgetId }} .pc-feature-icon {
        color: {{ $featureIconColor ?: 'var(--heading-color, #1f2937)' }};
    }

    /* ═══════════════════════════════════════════════
       POPULAR BADGE — NORMAL STATE
       Auto-follows var(--main-color-two) from Color Settings.
       ─────────────────────────────────────────────── */
    #{{ $widgetId }} .pc-badge {
        background-color: var(--main-color-two, #84cc16);
        color: {{ $badgeTextColor ?: 'var(--heading-color, #1f2937)' }};
    }

    /* ═══════════════════════════════════════════════
       BUTTON — NORMAL STATE
       ─────────────────────────────────────────────── */
    #{{ $widgetId }} .pc-btn {
        background-color: {{ $btnBg }};
        color: {{ $btnTextColor ?: 'var(--heading-color, #1f2937)' }};
        border-color: {{ $btnBorderColor }};
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    #{{ $widgetId }} .pc-btn:hover {
        background-color: {{ $btnHoverBg ?: 'var(--heading-color, #1f2937)' }};
        color: {{ $btnHoverTextColor }};
        border-color: {{ $btnHoverBorderColor }};
    }

    /* ═══════════════════════════════════════════════
       CARD HOVER — ALL CARDS (desktop only)
       Background auto-follows var(--main-color-one).
       ─────────────────────────────────────────────── */
    @media (min-width: 1024px) {
        #{{ $widgetId }} .pc-card:hover {
            background-color: var(--main-color-one, #0f766e);
            border-color: transparent;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.18), 0 8px 10px -6px rgba(0,0,0,.12);
        }
        #{{ $widgetId }} .pc-card:hover .pc-title        { color: {{ $hoverTitleColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-price        { color: {{ $hoverPriceColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-body,
        #{{ $widgetId }} .pc-card:hover .pc-period       { color: {{ $hoverBodyColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-divider      { border-color: {{ $hoverDividerColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-feature      { color: {{ $hoverFeatureTextColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-feature-icon { color: {{ $hoverFeatureIconColor }}; }
        #{{ $widgetId }} .pc-card:hover .pc-badge {
            background-color: {{ $badgeHoverBg ?: 'var(--main-color-two, #84cc16)' }};
            color: {{ $badgeHoverText ?: 'var(--heading-color, #1f2937)' }};
        }
        #{{ $widgetId }} .pc-card:hover .pc-btn {
            background-color: {{ $cardHoverBtnBg }};
            color: {{ $cardHoverBtnText ?: 'var(--heading-color, #1f2937)' }};
            border-color: {{ $cardHoverBtnBorder }};
        }
        #{{ $widgetId }} .pc-card:hover .pc-btn:hover { opacity: 0.88; }
    }
</style>

<!-- Pricing Section -->
<section id="{{ $widgetId }}"
         style="padding-top: {{ $paddingTop }}px; padding-bottom: {{ $paddingBottom }}px;">
    <div class="container mx-auto px-8">

        <!-- Section Heading -->
        <div class="max-w-2xl mx-auto text-center">
            <span class="rounded-lg py-1 px-3 border-2 bg-subTitle text-base-200 border-borderCS text-base">
                {{ $badgeText }}
            </span>
            <h3 class="pc-section-title font-urbanist font-semibold text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                {{ $title }}
            </h3>
        </div>

        <!-- Plan Type Toggle -->
        @if(count($availableTypes) > 1)
            <div class="max-w-xl mx-auto flex flex-wrap items-center justify-center gap-6 mt-6 min-h-[40px]">
                @foreach($availableTypes as $index => $type)
                    <label class="relative flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment-{{ $widgetId }}" value="{{ $type['key'] }}"
                               {{ $index === 0 ? 'checked' : '' }}
                               class="sr-only peer pricing-type-radio-{{ $widgetId }}">
                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center
                                    peer-checked:border-primary peer-checked:bg-primary transition-all">
                            <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                        </div>
                        <span class="font-medium select-none" style="color: var(--heading-color, #1f2937)">
                            {{ $type['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif

        <!-- Plan Cards -->
        @foreach($plansByType as $type => $plans)
            <div class="pricing-group-{{ $widgetId }} grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-[4.375rem]"
                 data-type="{{ $type }}"
                 style="{{ $loop->first || count($availableTypes) <= 1 ? '' : 'display:none;' }}">

                @foreach($plans as $plan)
                    <div class="pc-card flex flex-col h-full relative border rounded-xl p-8 md:p-6 overflow-hidden">

                        <!-- Decorative Image -->
                        @if(!empty($decorativeImage))
                            <img src="{{ $decorativeImage }}" alt=""
                                 class="absolute z-10 top-0 right-0 w-32 h-32 md:w-56 md:h-60 [transform:rotateY(180deg)] opacity-60 pointer-events-none">
                        @endif

                        <!-- Header -->
                        <div class="relative z-20 mb-7">
                            @if($plan['is_popular'])
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h2 class="pc-title font-urbanist text-2xl md:text-[1.75rem] font-bold mb-1 tracking-tight">
                                        {{ $plan['plan_name'] }}
                                    </h2>
                                    <span class="pc-badge py-1 px-4 rounded-lg font-semibold text-sm">
                                        {{ $plan['popular_text'] }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h2 class="pc-title font-urbanist text-2xl md:text-[1.75rem] font-bold mb-1 tracking-tight">
                                        {{ $plan['plan_name'] }}
                                    </h2>
                                    @if($plan['popular_text'])
                                        <span class="pc-badge py-1 px-4 rounded-lg font-semibold text-sm">
                                            {{ $plan['popular_text'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if($plan['subtitle'])
                                <p class="pc-body text-base font-normal mt-1">{{ $plan['subtitle'] }}</p>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="mb-8 min-h-[72px] flex items-center">
                            @if(!empty($plan['price']))
                                <div class="flex items-baseline gap-1">
                                    <span class="pc-price text-4xl md:text-5xl font-urbanist font-bold">
                                        {{ $plan['price'] }}
                                    </span>
                                    <span class="pc-period text-base">
                                        {{ $plan['period'] }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Divider -->
                        <hr class="pc-divider border-t mb-8">

                        <!-- Features -->
                        @if(!empty($plan['features']))
                            <ul class="space-y-4 mb-8 flex-1">
                                @foreach($plan['features'] as $feature)
                                    <li class="pc-feature flex items-center gap-3">
                                        <i class="pc-feature-icon icon-base ti tabler-check w-5 h-5 flex-shrink-0"
                                           aria-hidden="true"></i>
                                        <span class="text-base">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <!-- CTA Button -->
                        <div class="mt-auto">
                            <a href="{{ $plan['btn_url'] }}"
                               class="pc-btn block w-full py-3 px-6 border font-semibold text-base rounded-lg text-center">
                                {{ $plan['btn_text'] }}
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
</section>

<script>
    (function () {
        var radios = document.querySelectorAll('.pricing-type-radio-{{ $widgetId }}');
        var groups = document.querySelectorAll('.pricing-group-{{ $widgetId }}');
        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                var sel = this.value;
                groups.forEach(function (g) {
                    g.style.display = g.dataset.type === sel ? '' : 'none';
                });
            });
        });
    })();
</script>
