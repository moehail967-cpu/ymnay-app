


// ── TABLE DATA ──────────────────────────────────────────────
const rows = [
    { id: '#TXN001474', amount: '$82.00', status: 'Pending', method: 'Bank Account', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Complete', method: 'UPI', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Failed', method: 'UPI', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Complete', method: 'Bank Account', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Complete', method: 'PayPal', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Failed', method: 'UPI', date: '23-09-2025' },
    { id: '#TXN001474', amount: '$82.00', status: 'Complete', method: 'Bank Account', date: '23-09-2025' },
];

const statusStyle = {
    Pending: { bg: 'bg-yellow-50', border: 'border-yellow-300', text: 'text-yellow-600' },
    Complete: { bg: 'bg-green-50', border: 'border-green-400', text: 'text-green-600' },
    Failed: { bg: 'bg-red-50', border: 'border-red-300', text: 'text-red-500' },
};

const tbody = document.getElementById('tableBody');
rows.forEach((r, i) => {
    const s = statusStyle[r.status];
    const isLast = i === rows.length - 1;
    tbody.innerHTML += `
        <tr class="${isLast ? '' : 'border-b border-borderCS'}">
          <td class="px-6 py-4 text-sm text-gray-700">${r.id}</td>
          <td class="px-4 py-4 text-sm text-gray-700">${r.amount}</td>
          <td class="px-4 py-4">
            <span class="inline-block border text-xs font-medium px-4 py-1 rounded-full ${s.bg} ${s.border} ${s.text}">${r.status}</span>
          </td>
          <td class="px-4 py-4 text-sm text-gray-700">${r.method}</td>
          <td class="px-4 py-4 text-sm text-gray-700">${r.date}</td>
        </tr>`;
});

// ── LINE CHART ───────────────────────────────────────────────
const lineLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
const lineData = [450, 1050, 1400, 1100, 950, 1350, 1350];

const lCtx = document.getElementById('lineChart').getContext('2d');
const lineGrad = lCtx.createLinearGradient(0, 0, 0, 220);
lineGrad.addColorStop(0, 'rgba(27,63,80,0.15)');
lineGrad.addColorStop(1, 'rgba(27,63,80,0.01)');

const lineChart = new Chart(lCtx, {
    type: 'line',
    data: {
        labels: lineLabels,
        datasets: [{
            data: lineData,
            borderColor: '#0C4D54',
            borderWidth: 2,
            backgroundColor: lineGrad,
            fill: true,
            tension: 0.45,
            pointRadius: lineData.map((_, i) => i === 1 ? 6 : 4),
            pointBackgroundColor: '#0C4D54',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: {
            x: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                border: { display: false },
                ticks: { color: '#0C4D54', font: { size: 11, family: 'Inter' } }
            },
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                border: { display: false },
                ticks: {
                    color: '#0C4D54',
                    font: { size: 11, family: 'Inter' },
                    callback: v => '$' + v,
                    stepSize: 450,
                },
                min: 0, max: 1800,
            }
        }
    }
});

// Position line tooltip at Feb point
function positionLineTooltip() {
    const meta = lineChart.getDatasetMeta(0);
    const pt = meta.data[1];
    if (!pt) return;
    const tip = document.getElementById('lineTooltip');
    tip.style.left = (pt.x - 10) + 'px';
    tip.style.top = (pt.y + 14) + 'px';
    tip.classList.remove('hidden');
}
setTimeout(positionLineTooltip, 150);
window.addEventListener('resize', () => setTimeout(positionLineTooltip, 150));

// ── BAR CHART ────────────────────────────────────────────────
const barLabels = ['Complete', 'Pending', 'Failed'];
const barData = [1500, 420, 180];

const bCtx = document.getElementById('barChart').getContext('2d');

const barChart = new Chart(bCtx, {
    type: 'bar',
    data: {
        labels: barLabels,
        datasets: [{
            data: barData,
            backgroundColor: barLabels.map((_, i) => i === 0 ? '#0C4D54' : (i === 1 ? '#0C4D54' : '#0C4D54')),
            hoverBackgroundColor: '#0F6A73',
            borderRadius: 6,
            borderSkipped: false,
            barThickness: 60,
            
            
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: {
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { color: '#9ca3af', font: { size: 11, family: 'Inter' } }
            },
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                border: { display: false },
                ticks: {
                    color: '#9ca3af',
                    font: { size: 11, family: 'Inter' },
                    callback: v => '$' + v,
                    stepSize: 400,
                },
                min: 0, max: 1600,
            }
        }
    }
});

// Position bar tooltip at Complete bar
function positionBarTooltip() {
    const meta = barChart.getDatasetMeta(0);
    const bar = meta.data[0];
    if (!bar) return;
    const tip = document.getElementById('barTooltip');
    tip.style.left = (bar.x + 10) + 'px';
    tip.style.top = (bar.y + 10) + 'px';
    tip.classList.remove('hidden');
}
setTimeout(positionBarTooltip, 150);
window.addEventListener('resize', () => setTimeout(positionBarTooltip, 150));




(function () {
    // small inline SVG fallback (URL-encoded) used when external logos fail to load
    const fallback = 'data:image/svg+xml;utf8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40" viewBox="0 0 120 40">'
        + '<rect width="120" height="40" fill="#F3F4F6" /> '
        + '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" '
        + 'font-size="10" fill="#9CA3AF" font-family="Arial, Helvetica, sans-serif">Logo</text>'
        + '</svg>'
    );

    function applyFallbacks() {
        const container = document.getElementById('withdraw-methods');
        if (!container) return;
        container.querySelectorAll('img').forEach(img => {
            // set lazy loading for performance
            img.loading = 'lazy';
            // if image fails to load, replace with inline SVG fallback
            img.addEventListener('error', function onErr() {
                this.removeEventListener('error', onErr);
                this.src = fallback;
                this.classList.add('broken-logo');
                this.alt = this.alt || 'logo';
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyFallbacks);
    else applyFallbacks();
})();


// const withbrawBody = document.getElementById('withbrawBody')
// const paymentMethod = document.getElementById('paymentMethod')
// const btnWithDrawal = document.getElementById('btnWithDrawal')

// let isBack = false;

// btnWithDrawal.addEventListener('click', function () {

//     if (!isBack) {
//         // Forward view
//         withbrawBody.classList.add('hidden')
//         paymentMethod.classList.remove('hidden')
//         btnWithDrawal.innerHTML = `
//                                                <button
//                                         class="px-6 py-3 flex items-center gap-2 rounded-xl text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary">
//                                         <i class="icon-base ti tabler-arrow-narrow-left"></i>
//                                        Back
//                                     </button>
//         `
//         isBack = true;
//     } else {
//         // Back view
//         withbrawBody.classList.remove('hidden')
//         paymentMethod.classList.add('hidden')
//         btnWithDrawal.innerHTML = `
//                                               <button
//                                         class="px-6 py-3 flex items-center gap-2 rounded-xl text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary">
//                                         <i class="icon-base ti tabler-plus"></i>
//                                         New Withdrawal
//                                     </button>
//         `
//         isBack = false;
//     }

// })