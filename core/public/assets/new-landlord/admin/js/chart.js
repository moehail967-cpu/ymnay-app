/**
 * Dashboard Charts Functionality
 */

const DashboardCharts = {
    initMonthlyRaised(url, token, labelText) {
        const canvas = document.getElementById('monthlyRaised');
        if (!canvas) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            success: function (data) {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: labelText,
                            backgroundColor: '#1a5c4e',
                            borderColor: 'transparent',
                            data: data.data,
                            barThickness: 20,
                            hoverBackgroundColor: '#1a4a3e',
                            borderRadius: 6,
                            minBarLength: 50,
                            indexAxis: "x",
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9b9590', font: { size: 11, weight: 500 } }
                            },
                            y: {
                                grid: { color: '#e0f0f0', drawBorder: false },
                                border: { dash: [4,4] },
                                ticks: { color: '#9b9590', font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
    },

    initDailyActivity(url, token, labelText) {
        const canvas = document.getElementById('monthlyRaisedPerDay');
        if (!canvas) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            success: function (data) {
                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: labelText,
                            borderColor: '#1a5c4e',
                            backgroundColor: 'rgba(26, 92, 78, 0.08)',
                            data: data.data,
                            borderWidth: 2.5,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#1a5c4e',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9b9590', font: { size: 10 } }
                            },
                            y: {
                                grid: { color: '#e0f0f0', drawBorder: false },
                                border: { dash: [4,4] },
                                ticks: { color: '#9b9590', font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
    }
};

window.DashboardCharts = DashboardCharts;
