// Enhanced Sidebar functionality with working dropdowns and notifications
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const brandText = document.getElementById('brandText');
    const notificationLink = document.getElementById('notificationLink');

    // Desktop sidebar toggle
    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        if (mainContent) {
            mainContent.classList.toggle('sidebar-collapsed');
        }

        // Update brand text based on sidebar state
        if (sidebar.classList.contains('collapsed')) {
            brandText.textContent = 'H';
        } else {
            brandText.textContent = 'HRMS';
        }
    });

    // Mobile sidebar toggle
    mobileToggle.addEventListener('click', function () {
        sidebar.classList.toggle('show');
        sidebarOverlay.classList.toggle('show');
        // Prevent body scroll when sidebar is open on mobile
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    });

    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', function () {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    });

    // Handle window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        updateBrandText();
    });

    // Update brand text on page load and resize
    function updateBrandText() {
        if (window.innerWidth <= 768) {
            brandText.textContent = 'HRMS';
        } else if (sidebar.classList.contains('collapsed')) {
            brandText.textContent = 'H';
        } else {
            brandText.textContent = 'HRMS';
        }
    }

    // Initialize brand text
    updateBrandText();

    // Active link functionality
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href === currentPath.split('/').pop()) {
            link.classList.add('active');
        }
    });

    // FIX FOR DROPDOWNS - Custom Implementation
    const dropdownToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    dropdownToggles.forEach(function(toggle) {
        const targetSelector = toggle.getAttribute('data-bs-target');
        const targetElement = document.querySelector(targetSelector);
        
        if (targetElement) {
            // Handle click events
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Close other dropdowns first
                dropdownToggles.forEach(function(otherToggle) {
                    if (otherToggle !== toggle) {
                        const otherTarget = document.querySelector(otherToggle.getAttribute('data-bs-target'));
                        if (otherTarget) {
                            otherTarget.classList.remove('show');
                            otherToggle.classList.add('collapsed');
                            otherToggle.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
                
                // Toggle current dropdown
                targetElement.classList.toggle('show');
                toggle.classList.toggle('collapsed');
                toggle.setAttribute('aria-expanded', targetElement.classList.contains('show'));
            });

            // Handle collapse events to update arrow rotation
            targetElement.addEventListener('shown.bs.collapse', function() {
                toggle.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded', 'true');
            });

            targetElement.addEventListener('hidden.bs.collapse', function() {
                toggle.classList.add('collapsed');
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // FIX FOR NOTIFICATIONS
    if (notificationLink) {
        // Create notification dropdown if it doesn't exist
        let notificationDropdown = document.querySelector('.notification-dropdown');
        
        if (!notificationDropdown) {
            notificationDropdown = document.createElement('div');
            notificationDropdown.className = 'notification-dropdown';
            notificationDropdown.innerHTML = `
                <div class="notification-header">
                    <h6>Notifications</h6>
                </div>
                <div class="notification-item">
                    <div class="notification-title">New Room Booking</div>
                    <div class="notification-text">Room 101 has been booked for tonight</div>
                    <div class="notification-time">5 minutes ago</div>
                </div>
                <div class="notification-item">
                    <div class="notification-title">Restaurant Reservation</div>
                    <div class="notification-text">Table 5 reserved for 7:00 PM</div>
                    <div class="notification-time">10 minutes ago</div>
                </div>
                <div class="notification-item">
                    <div class="notification-title">Staff Update</div>
                    <div class="notification-text">New employee added to the system</div>
                    <div class="notification-time">1 hour ago</div>
                </div>
                <div class="notification-footer">
                    <a href="#" class="view-all">View All Notifications</a>
                </div>
            `;
            
            // Insert after notification link
            notificationLink.parentNode.appendChild(notificationDropdown);
        }

        // Notification click handler
        notificationLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close all other dropdowns first
            document.querySelectorAll('.collapse.show').forEach(function(collapse) {
                const collapseInstance = bootstrap.Collapse.getInstance(collapse);
                if (collapseInstance) {
                    collapseInstance.hide();
                }
            });
            
            // Toggle notification dropdown
            notificationDropdown.classList.toggle('show');
            
            // Update notification badge
            const badge = document.querySelector('.notification-badge');
            if (badge && notificationDropdown.classList.contains('show')) {
                // Hide badge after a delay
                setTimeout(() => {
                    badge.style.display = 'none';
                }, 1000);
            }
        });

        // Close notifications when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationLink.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
    }

    // Theme toggle functionality (if exists)
    const themeToggle = document.getElementById('themeToggle');
    const themeText = document.getElementById('themeText');
    
    if (themeToggle && themeText) {
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const currentTheme = document.body.getAttribute('data-theme');
            const icon = themeToggle.querySelector('i');
            
            if (currentTheme === 'dark') {
                document.body.removeAttribute('data-theme');
                themeText.textContent = 'Dark Mode';
                if (icon) icon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                document.body.setAttribute('data-theme', 'dark');
                themeText.textContent = 'Light Mode';
                if (icon) icon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            themeText.textContent = 'Light Mode';
            const icon = themeToggle.querySelector('i');
            if (icon) icon.className = 'fas fa-sun';
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // ESC key closes sidebar on mobile and notifications
        if (e.key === 'Escape') {
            if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
            
            // Close notifications
            const notificationDropdown = document.querySelector('.notification-dropdown');
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
        }
    });
});