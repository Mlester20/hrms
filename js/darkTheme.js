document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const themeText = document.getElementById('themeText');
    const themeIcon = themeToggle.querySelector('i');

    const savedTheme = localStorage.getItem('theme');

    // Apply theme on initial load
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        themeText.textContent = 'Light Mode';
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    } 

    // Always update charts on load based on current theme
    const isDarkMode = document.body.classList.contains('dark-theme');
    updateChartsTheme(isDarkMode);

    themeToggle.addEventListener('click', function (e) {
        e.preventDefault();
        document.body.classList.toggle('dark-theme');
        const isDarkMode = document.body.classList.contains('dark-theme');

        if (isDarkMode) {
            themeText.textContent = 'Light Mode';
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            themeText.textContent = 'Dark Mode';
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }

        updateChartsTheme(isDarkMode);
    });

    function updateChartsTheme(isDarkMode) {
        const textColor = isDarkMode ? '#e6e6e6' : '#333';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        
        // Make sure all chart instances are initialized before accessing them
        if (window.revenueChart) {
            window.revenueChart.options.scales.y.ticks.color = textColor;
            window.revenueChart.options.scales.x.ticks.color = textColor;
            window.revenueChart.options.scales.y.grid.color = gridColor;
            window.revenueChart.options.scales.x.grid.color = gridColor;
            window.revenueChart.options.plugins.title.color = textColor;
            window.revenueChart.options.plugins.legend.labels.color = textColor;
            window.revenueChart.update();
        }
        
        if (window.bookingStatusChart) {
            window.bookingStatusChart.options.plugins.title.color = textColor;
            window.bookingStatusChart.options.plugins.legend.labels.color = textColor;
            window.bookingStatusChart.update();
        }
        
        if (window.campaignChart) {
            window.campaignChart.options.plugins.title.color = textColor;
            window.campaignChart.options.plugins.legend.labels.color = textColor;
            window.campaignChart.options.scales.y.ticks.color = textColor;
            window.campaignChart.options.scales.x.ticks.color = textColor;
            window.campaignChart.options.scales.y.grid.color = gridColor;
            window.campaignChart.options.scales.x.grid.color = gridColor;
            window.campaignChart.update();
        }
    }
});