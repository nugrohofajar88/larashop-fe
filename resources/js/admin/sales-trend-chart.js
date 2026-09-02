import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip } from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip);

export const initAdminSalesTrendChart = () => {
    const canvas = document.querySelector('[data-sales-trend-chart]');
    if (!canvas) return;

    let points = [];
    try {
        points = JSON.parse(canvas.dataset.points || '[]');
    } catch (e) {
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: points.map((p) => p.period_label),
            datasets: [{
                label: 'Omzet',
                data: points.map((p) => p.revenue_value),
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
                pointHoverRadius: 5,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const point = points[ctx.dataIndex];
                            return 'Omzet: ' + point.revenue + ' (' + point.order_count + ' order)';
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp' + (value >= 1000000 ? (value / 1000000).toFixed(1) + 'jt' : value >= 1000 ? (value / 1000).toFixed(0) + 'rb' : value),
                    },
                },
                x: {
                    ticks: { maxRotation: 0, autoSkip: true },
                },
            },
        },
    });
};
