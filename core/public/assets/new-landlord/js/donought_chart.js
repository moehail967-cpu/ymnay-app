
/* ── Commission Overview Donut Chart ──────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded — donut chart skipped');
        return;
    }

    var container = document.getElementById('commissionChartContainer');
    if (!container) return;

    var paid   = window.paidCommission   || 0;
    var unpaid = window.unpaidCommission || 0;
    var total  = window.totalCommission  || (paid + unpaid);
    var sym    = window.currencySymbol   || '$';

    function fmt(v) {
        return sym + parseFloat(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // arc angles
    var commTotal = paid + unpaid;
    var gap = 25;
    var paidSpan, unpaidSpan;

    if (commTotal > 0) {
        var available = 360 - (gap * 2);
        paidSpan   = (paid   / commTotal) * available;
        unpaidSpan = (unpaid / commTotal) * available;
    } else {
        paidSpan   = 155;
        unpaidSpan = 155;
    }

    // Build HTML with inline styles for reliable sizing
    container.innerHTML =
        '<div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 1px 2px rgba(0,0,0,.05);padding:20px;width:100%;">' +
            '<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">' +
                '<div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;background-color:#1b3f50;flex-shrink:0;">' +
                    '<i class="ti tabler-currency-dollar" style="color:#fff;font-size:20px;"></i>' +
                '</div>' +
                '<div>' +
                    '<p style="font-size:16px;font-weight:700;color:#1f2937;line-height:1.2;">Commission Overview</p>' +
                    '<p style="font-size:14px;color:#9ca3af;margin-top:2px;">Total: ' + fmt(total) + '</p>' +
                '</div>' +
            '</div>' +
            '<div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px;">' +
                '<div style="width:160px;height:160px;flex-shrink:0;">' +
                    '<canvas id="donut" width="160" height="160"></canvas>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:12px;flex:1;min-width:180px;">' +
                    '<div style="display:flex;align-items:center;gap:12px;border-radius:12px;border:1px solid #0C4D54;background:rgba(12,77,84,0.06);padding:0 12px;height:96px;">' +
                        '<div style="width:48px;height:48px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(12,77,84,0.1);flex-shrink:0;">' +
                            '<i class="ti tabler-circle-check" style="font-size:28px;color:#0C4D54;"></i>' +
                        '</div>' +
                        '<div>' +
                            '<p style="font-size:14px;color:#64748b;line-height:1.2;">Paid Commission</p>' +
                            '<p style="font-size:18px;font-weight:700;color:#1e293b;margin-top:2px;">' + fmt(paid) + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:12px;border-radius:12px;border:1px solid #0C4D54;background:#eff4f8;padding:0 12px;height:96px;">' +
                        '<div style="width:48px;height:48px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(12,77,84,0.1);flex-shrink:0;">' +
                            '<i class="ti tabler-alert-circle" style="font-size:28px;color:#0C4D54;"></i>' +
                        '</div>' +
                        '<div>' +
                            '<p style="font-size:14px;color:#64748b;line-height:1.2;">Unpaid Commission</p>' +
                            '<p style="font-size:18px;font-weight:500;color:#1e293b;margin-top:2px;">' + fmt(unpaid) + '</p>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    // animation state
    var animProgress = 0;
    var animDuration = 60;

    var combinedArcPlugin = {
        id: 'combinedArc',
        afterDraw: function (chart) {
            if (!chart || !chart.ctx) return;
            var ctx = chart.ctx;
            var w   = chart.width;
            var h   = chart.height;
            var cx  = w / 2;
            var cy  = h / 2;

            var outerR    = Math.min(w, h) / 2 - 6;
            var thickness = 18;
            var midR      = outerR - thickness / 2;

            function toRad(deg) { return (deg * Math.PI) / 180; }

            var ease = Math.min(animProgress / animDuration, 1);
            ease = 1 - Math.pow(1 - ease, 4);

            var currentPaidSpan   = paidSpan * ease;
            var currentUnpaidSpan = unpaidSpan * ease;

            var paidStart   = gap / 2;
            var paidEnd     = paidStart + currentPaidSpan;
            var unpaidStart = paidEnd + gap;
            var unpaidEnd   = unpaidStart + currentUnpaidSpan;

            if (currentPaidSpan > 1) {
                ctx.save();
                ctx.beginPath();
                ctx.arc(cx, cy, midR, toRad(paidStart), toRad(paidEnd), false);
                ctx.strokeStyle = '#92E721';
                ctx.lineWidth   = thickness;
                ctx.lineCap     = 'round';
                ctx.stroke();
                ctx.restore();
            }

            if (currentUnpaidSpan > 1) {
                ctx.save();
                ctx.beginPath();
                ctx.arc(cx, cy, midR, toRad(unpaidStart), toRad(unpaidEnd), false);
                ctx.strokeStyle = '#0C4D54';
                ctx.lineWidth   = thickness;
                ctx.lineCap     = 'round';
                ctx.stroke();
                ctx.restore();
            }

            if (commTotal > 0) {
                var paidPct = Math.round((paid / commTotal) * 100);
                ctx.save();
                ctx.textAlign    = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle    = '#1f2937';
                ctx.font         = 'bold 16px Inter, sans-serif';
                ctx.fillText(paidPct + '%', cx, cy - 6);
                ctx.fillStyle = '#9ca3af';
                ctx.font      = '11px Inter, sans-serif';
                ctx.fillText('Paid', cx, cy + 12);
                ctx.restore();
            }

            if (animProgress < animDuration) {
                animProgress++;
                requestAnimationFrame(function () { chart.draw(); });
            }
        }
    };

    // Create chart after DOM update
    requestAnimationFrame(function () {
        var canvas = document.getElementById('donut');
        if (!canvas) { console.warn('donut canvas not found'); return; }
        var ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            plugins: [combinedArcPlugin],
            data: {
                datasets: [{ data: [1], backgroundColor: ['transparent'], borderWidth: 0 }]
            },
            options: {
                cutout: '60%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                animation: { duration: 0 },
                responsive: true,
                maintainAspectRatio: true,
                events: []
            }
        });
    });
});
