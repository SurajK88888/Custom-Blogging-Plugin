/**
 * Custom Blog Pro - Dashboard JS
 */
document.addEventListener('DOMContentLoaded', function() {
    
    if (typeof cbpDashboardData !== 'undefined' && typeof Chart !== 'undefined') {
        const ctx = document.getElementById('cbpOverviewChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Blog Views', 'Ad Clicks', 'Emails Sent'],
                    datasets: [{
                        data: [
                            parseInt(cbpDashboardData.views), 
                            parseInt(cbpDashboardData.clicks), 
                            parseInt(cbpDashboardData.emails)
                        ],
                        backgroundColor: [
                            '#3b82f6', // Blue
                            '#10b981', // Emerald
                            '#f59e0b'  // Amber
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Helper to extract labels and data from array of objects
        const extractChartData = (dataArray, labelKey, valueKey) => {
            if (!dataArray || !dataArray.length) return { labels: [], data: [] };
            return {
                labels: dataArray.map(item => item[labelKey] ? item[labelKey].charAt(0).toUpperCase() + item[labelKey].slice(1) : 'Unknown'),
                data: dataArray.map(item => parseInt(item[valueKey], 10))
            };
        };

        // Device Chart
        const ctxDevice = document.getElementById('cbpDeviceChart');
        if (ctxDevice && cbpDashboardData.devices) {
            const deviceData = extractChartData(cbpDashboardData.devices, 'device', 'views');
            new Chart(ctxDevice, {
                type: 'pie',
                data: {
                    labels: deviceData.labels,
                    datasets: [{
                        data: deviceData.data,
                        backgroundColor: ['#6366f1', '#ec4899', '#8b5cf6', '#14b8a6'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // Browser Chart
        const ctxBrowser = document.getElementById('cbpBrowserChart');
        if (ctxBrowser && cbpDashboardData.browsers) {
            const browserData = extractChartData(cbpDashboardData.browsers, 'browser', 'views');
            new Chart(ctxBrowser, {
                type: 'bar',
                data: {
                    labels: browserData.labels,
                    datasets: [{
                        label: 'Views',
                        data: browserData.data,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
});
