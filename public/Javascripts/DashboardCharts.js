// ============================================
    // TOP 5 PRODUCTS CHART
    // ============================================
    const topProductsData = {
        labels: window.chartData.topProductsLabels,
        datasets: [{
            label: 'Units Sold',
            data: window.chartData.topProductsValues,
            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
            borderColor: ['#2563EB', '#059669', '#D97706', '#DC2626', '#7C3AED'],
            borderWidth: 2
        }]
    };

    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: topProductsData,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sold: ' + context.parsed.y + ' units';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Units Sold'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Products'
                    }
                }
            }
        }
    });

    // ============================================
    // WEEKLY SALES CHART
    // ============================================
    const weeklySalesData = {
        labels: window.chartData.weeklySalesLabels,
        datasets: [{
            label: '₱ Sales',
            data: window.chartData.weeklySalesValues,
            borderColor: '#16A34A',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#16A34A',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    };

    new Chart(document.getElementById('weeklySalesChart'), {
        type: 'line',
        data: weeklySalesData,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH');
                        }
                    },
                    title: {
                        display: true,
                        text: 'Sales Amount (₱)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of the Week'
                    }
                }
            }
        }
    });