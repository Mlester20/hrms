// Animate stat values on load
document.addEventListener('DOMContentLoaded', function () {
    const statValues = document.querySelectorAll('.stat-value');

    statValues.forEach(stat => {
        const target = parseInt(
            stat.getAttribute('data-target') ||
            stat.textContent.replace(/[^0-9]/g, '')
        );

        if (!isNaN(target)) {
            animateValue(stat, 0, target, 2000);
        }
    });
});

function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;

        if (current >= end) {
            current = end;
            clearInterval(timer);
        }

        const formatted = Math.floor(current).toLocaleString();

        if (element.textContent.includes('₱')) {
            element.textContent = '₱' + formatted + '.00';
        } else {
            element.textContent = formatted;
        }
    }, 16);
}

function animateCard(card) {
    card.style.transform = 'scale(0.95)';

    setTimeout(() => {
        card.style.transform = 'translateY(-5px)';
    }, 100);
}

// Updated setTimeFilter with dynamic data fetching
function setTimeFilter(btn, period) {
    // Remove active class from all buttons
    const parentFilters = btn.parentElement;
    parentFilters.querySelectorAll('.filter-btn')
        .forEach(b => b.classList.remove('active'));

    // Add active class to clicked button
    btn.classList.add('active');

    // Show loading state
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Fetch new revenue data
    fetch(`../api/getRevenueData.php?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update chart data
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.data;
                revenueChart.update('active');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Failed to load revenue data'
                });
            }
        })
        .catch(error => {
            console.error('Error fetching revenue data:', error);
            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'Failed to fetch revenue data. Please try again.'
            });
        })
        .finally(() => {
            // Restore button state
            btn.disabled = false;
            btn.textContent = originalText;
        });
}

// Chart Data (these must be defined globally via PHP before this file loads)
const statusLabels = window.statusLabels || [];
const statusCounts = window.statusCounts || [];
const bookingsData = window.bookingsData || [];
const revenueData = window.revenueData || [];

// Chart.js default settings for dark theme
Chart.defaults.color = '#8b92b8';
Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

// Booking Status Doughnut Chart
let statusChart;

if (statusLabels.length > 0) {
    statusChart = new Chart(
        document.getElementById('statusChart'),
        {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [
                    {
                        data: statusCounts,
                        backgroundColor: [
                            'rgba(0, 212, 255, 0.8)',
                            'rgba(0, 255, 136, 0.8)',
                            'rgba(255, 184, 0, 0.8)',
                            'rgba(255, 71, 87, 0.8)'
                        ],
                        borderWidth: 0
                    }
                ]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            color: '#8b92b8',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        }
    );
}

// Monthly Bookings Bar Chart
const bookingsChart = new Chart(
    document.getElementById('bookingsChart'),
    {
        type: 'bar',
        data: {
            labels: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],
            datasets: [
                {
                    label: 'Bookings',
                    data: bookingsData,
                    backgroundColor: context => {
                        const gradient =
                            context.chart.ctx.createLinearGradient(0, 0, 0, 400);

                        gradient.addColorStop(0, 'rgba(0, 212, 255, 0.8)');
                        gradient.addColorStop(1, 'rgba(107, 92, 231, 0.8)');

                        return gradient;
                    },
                    borderRadius: 8,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#8b92b8'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#8b92b8'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    }
);

// Monthly Revenue Line Chart
const revenueChart = new Chart(
    document.getElementById('revenueChart'),
    {
        type: 'line',
        data: {
            labels: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],
            datasets: [
                {
                    label: 'Revenue (₱)',
                    data: revenueData,
                    borderColor: '#00d4ff',
                    backgroundColor: context => {
                        const gradient =
                            context.chart.ctx.createLinearGradient(0, 0, 0, 400);

                        gradient.addColorStop(0, 'rgba(0, 212, 255, 0.2)');
                        gradient.addColorStop(1, 'rgba(0, 212, 255, 0)');

                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#00d4ff',
                    pointBorderColor: '#0a0e27',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱' + value.toLocaleString(),
                        color: '#8b92b8'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#8b92b8'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(26, 31, 58, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#8b92b8',
                    borderColor: 'rgba(0, 212, 255, 0.5)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: context =>
                            'Revenue: ₱' +
                            context.parsed.y.toLocaleString()
                    }
                }
            }
        }
    }
);

// Resize charts on window resize
window.addEventListener('resize', function () {
    if (typeof statusChart !== 'undefined') {
        statusChart.resize();
    }

    bookingsChart.resize();
    revenueChart.resize();
});

// Generate and download report
function downloadReport() {
    Swal.fire({
        title: 'Generating Report',
        html: 'Please wait while we prepare your report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Create a hidden iframe to trigger download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '../api/generateReport.php';
    document.body.appendChild(iframe);

    // Close loading after 2 seconds
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'Report Generated!',
            text: 'Your report is downloading now.',
            timer: 2000,
            showConfirmButton: false
        });
        
        // Remove iframe after download
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 3000);
    }, 2000);
}