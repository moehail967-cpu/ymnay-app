<div class="col-span-1">
    <div class="analytics-stat">
        <div class="icon">
            <i class="mdi mdi-chart-line"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="label">{{ $stat['key'] }}</p>
            <div class="flex items-center gap-2.5">
                <span class="value">{{ $stat['value'] }}</span>
                @if (isset($stat['percentage']) && $stat['percentage'] > 0)
                    @include(sprintf('siteanalytics::admin.stats.%s-icon', $stat['increase'] ? 'increase' : 'decrease'))
                @endif
            </div>
        </div>
    </div>
</div>
