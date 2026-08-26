
let isOpen = true;

function toggleMenu() {
    var body = document.getElementById('menu-body');
    var icon = document.getElementById('toggle-icon');
    if (isOpen) {
        body.style.display = 'none';
        icon.className = 'ti tabler-plus';
    } else {
        body.style.display = 'block';
        icon.className = 'ti tabler-minus';
    }
    isOpen = !isOpen;
}

function setActive(el) {
    document.querySelectorAll('.menu-item').forEach(function (item) {
        item.style.backgroundColor = '';
        item.style.color = '';
        item.classList.remove('active-item');
        item.classList.add('text-gray-200');
    });
    el.style.backgroundColor = '#84cc16';
    el.style.color = '#1a2e1a';
    el.classList.add('active-item');
    el.classList.remove('text-gray-200');
}

/* ── Earnings Trend Chart ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded — earnings chart skipped');
        return;
    }

    var container = document.getElementById('earningsChartContainer');
    if (!container) return;

    var labels = window.earningsLabels || ['Jan','Feb','Mar','Apr','May','Jun','Jul'];
    var data   = window.earningsData   || [0,0,0,0,0,0,0];
    var sym    = window.currencySymbol  || '$';

    function fmt(v) {
        return sym + parseFloat(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // find peak
    var peakIdx = 0, peakVal = 0;
    for (var i = 0; i < data.length; i++) {
        if (data[i] > peakVal) { peakVal = data[i]; peakIdx = i; }
    }

    var yMax     = peakVal > 0 ? Math.ceil(peakVal * 1.2 / 100) * 100 : 100;
    var stepSize = Math.ceil(yMax / 4 / 50) * 50 || 50;

    // Build HTML
    container.innerHTML =
        '<div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.05);padding:20px;">' +
            '<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">' +
                '<div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;background-color:#1b3f50;flex-shrink:0;">' +
                    '<i class="ti tabler-trending-up" style="color:#fff;font-size:20px;"></i>' +
                '</div>' +
                '<div>' +
                    '<p style="font-size:16px;font-weight:700;color:#1f2937;line-height:1.2;">Earnings Trend</p>' +
                    '<p style="font-size:14px;color:#9ca3af;margin-top:2px;">Monthly performance overview</p>' +
                '</div>' +
            '</div>' +
            '<div style="position:relative;width:100%;height:220px;">' +
                '<canvas id="earningsChart"></canvas>' +
                '<div id="chartTooltip" style="position:absolute;display:none;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);padding:8px 16px;font-size:14px;pointer-events:none;z-index:10;transition:all .15s ease;">' +
                    '<p id="tooltipMonth" style="font-weight:600;color:#1f2937;"></p>' +
                    '<p id="tooltipEarn" style="color:#6b7280;font-size:12px;margin-top:2px;"></p>' +
                '</div>' +
            '</div>' +
        '</div>';

    // Create chart after DOM update
    requestAnimationFrame(function () {
        var canvas = document.getElementById('earningsChart');
        if (!canvas) { console.warn('earningsChart canvas not found'); return; }
        var ctx = canvas.getContext('2d');

        var gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(27,63,80,0.18)');
        gradient.addColorStop(1, 'rgba(27,63,80,0.01)');

        // external tooltip handler
        function tooltipHandler(context) {
            var tooltip = context.tooltip;
            var el  = document.getElementById('chartTooltip');
            var mEl = document.getElementById('tooltipMonth');
            var eEl = document.getElementById('tooltipEarn');
            if (!el) return;

            if (tooltip.opacity === 0) {
                showPeak(context.chart);
                return;
            }

            var idx = tooltip.dataPoints[0].dataIndex;
            if (mEl) mEl.textContent = labels[idx];
            if (eEl) eEl.textContent = 'Earn : ' + fmt(data[idx]);

            el.style.left = tooltip.caretX + 'px';
            el.style.top  = (tooltip.caretY + 12) + 'px';
            el.style.display = 'block';
        }

        window._earningsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: '#1b3f50',
                    borderWidth: 2,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0,
                    pointRadius: function (c) { return c.dataIndex === peakIdx ? 6 : 0; },
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1b3f50',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#1b3f50',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800, easing: 'easeOutQuart' },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false, external: tooltipHandler }
                },
                scales: {
                    x: {
                        grid: {
                            color: function (c) { return c.index === peakIdx ? '#94a3b8' : 'rgba(0,0,0,0.06)'; },
                            lineWidth: function (c) { return c.index === peakIdx ? 1.5 : 1; },
                            borderDash: function (c) { return c.index === peakIdx ? [5, 4] : []; }
                        },
                        border: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 11, family: 'Inter' } }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        border: { display: false },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11, family: 'Inter' },
                            callback: function (v) { return sym + v; },
                            stepSize: stepSize
                        },
                        min: 0,
                        max: yMax
                    }
                },
                onHover: function (evt, els) {
                    if (evt.native) evt.native.target.style.cursor = els.length ? 'pointer' : 'default';
                }
            }
        });

        function showPeak(chart) {
            if (!chart || peakVal === 0) return;
            var meta  = chart.getDatasetMeta(0);
            var point = meta && meta.data && meta.data[peakIdx];
            var el  = document.getElementById('chartTooltip');
            var mEl = document.getElementById('tooltipMonth');
            var eEl = document.getElementById('tooltipEarn');
            if (!point || !el) return;

            if (mEl) mEl.textContent = labels[peakIdx];
            if (eEl) eEl.textContent = 'Earn : ' + fmt(peakVal);
            el.style.left = (point.x - 10) + 'px';
            el.style.top  = (point.y + 14) + 'px';
            el.style.display = 'block';
        }

        setTimeout(function () { showPeak(window._earningsChart); }, 900);
        window.addEventListener('resize', function () {
            setTimeout(function () { showPeak(window._earningsChart); }, 150);
        });
    });
});
