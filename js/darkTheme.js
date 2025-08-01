document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const themeText = document.getElementById('themeText');
    const themeIcon = themeToggle?.querySelector('i');

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        enableDarkTheme();
    } else {
        enableLightTheme();
    }

    themeToggle?.addEventListener('click', function (e) {
        e.preventDefault();
        document.body.classList.contains('dark-theme') ? enableLightTheme() : enableDarkTheme();
    });

    function enableDarkTheme() {
        document.body.classList.add('dark-theme');
        if (themeText) themeText.textContent = 'Light Theme';
        if (themeIcon) themeIcon.className = 'fas fa-sun';
        localStorage.setItem('theme', 'dark');
        updateChartTheme(true);
    }

    function enableLightTheme() {
        document.body.classList.remove('dark-theme');
        if (themeText) themeText.textContent = 'Dark Theme';
        if (themeIcon) themeIcon.className = 'fas fa-moon';
        localStorage.setItem('theme', 'light');
        updateChartTheme(false);
    }

    function updateChartTheme(isDark) {
        // List of global chart variables
        const charts = ['revenueChart', 'bookingsChart', 'statusChart'];

        charts.forEach(chartName => {
            const chart = window[chartName];
            if (!chart || !chart.options || !chart.options.plugins) return;

            // Title
            if (chart.options.plugins.title) {
                chart.options.plugins.title.color = isDark ? '#e9ecef' : '#333';
            }

            // Legend
            if (chart.options.plugins.legend?.labels) {
                chart.options.plugins.legend.labels.color = isDark ? '#e9ecef' : '#333';
            }

            // Axes (skip for pie chart)
            if (chart.options.scales) {
                if (chart.options.scales.x) {
                    if (chart.options.scales.x.ticks) {
                        chart.options.scales.x.ticks.color = isDark ? '#e9ecef' : '#333';
                    }
                    if (chart.options.scales.x.grid) {
                        chart.options.scales.x.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                    }
                }

                if (chart.options.scales.y) {
                    if (chart.options.scales.y.ticks) {
                        chart.options.scales.y.ticks.color = isDark ? '#e9ecef' : '#333';
                    }
                    if (chart.options.scales.y.grid) {
                        chart.options.scales.y.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                    }
                }
            }

            chart.update();
        });
    }
});
