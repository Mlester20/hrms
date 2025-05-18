document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle button
    const themeToggle = document.getElementById('themeToggle');
    const themeText = document.getElementById('themeText');
    const themeIcon = themeToggle.querySelector('i');
    
    // Check for saved theme preference or use preferred color scheme
    const savedTheme = localStorage.getItem('theme');
    
    // Apply theme on page load
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        enableDarkTheme();
    } else {
        enableLightTheme();
    }
    
    // Toggle theme on click
    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (document.body.classList.contains('dark-theme')) {
            enableLightTheme();
        } else {
            enableDarkTheme();
        }
    });
    
    // Function to enable dark theme
    function enableDarkTheme() {
        document.body.classList.add('dark-theme');
        themeText.textContent = 'Light Mode';
        themeIcon.className = 'fas fa-sun';
        localStorage.setItem('theme', 'dark');
        updateChartTheme(true);
    }
    
    // Function to enable light theme
    function enableLightTheme() {
        document.body.classList.remove('dark-theme');
        themeText.textContent = 'Dark Mode';
        themeIcon.className = 'fas fa-moon';
        localStorage.setItem('theme', 'light');
        updateChartTheme(false);
    }
    
    // Function to update chart themes if they exist
    function updateChartTheme(isDark) {
        if (window.campaignChart) {
            updateChartColors(window.campaignChart, isDark);
        }
        
        if (window.revenueChart) {
            // For the revenue/pie chart, we don't need to change the colors as they're custom
            // But we do need to update the text colors
            window.revenueChart.options.plugins.title.color = isDark ? '#e9ecef' : '#333';
            window.revenueChart.options.plugins.legend.labels.color = isDark ? '#e9ecef' : '#333';
            window.revenueChart.update();
        }
    }
    
    // Update chart colors based on theme
    function updateChartColors(chart, isDark) {
        // Update title and legend text colors
        chart.options.plugins.title.color = isDark ? '#e9ecef' : '#333';
        chart.options.plugins.legend.labels.color = isDark ? '#e9ecef' : '#333';
        
        // Update axis colors
        if (chart.options.scales && chart.options.scales.y) {
            chart.options.scales.y.ticks.color = isDark ? '#e9ecef' : '#333';
            chart.options.scales.y.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        }
        
        if (chart.options.scales && chart.options.scales.x) {
            chart.options.scales.x.ticks.color = isDark ? '#e9ecef' : '#333';
            chart.options.scales.x.grid.color = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        }
        
        // Update the chart
        chart.update();
    }
});