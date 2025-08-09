document.addEventListener("DOMContentLoaded", function () {
    function fetchNotifications() {
        fetch('../api/notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                const dropdown = document.querySelector('.notification-dropdown');
                const badge = document.querySelector('.notification-badge');
                
                dropdown.innerHTML = '';
                
                let unreadCount = data.filter(item => item.is_read === 0).length;

                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.classList.add('bg-danger');
                } else {
                    badge.textContent = '';
                    badge.classList.remove('bg-danger');
                }

                // Add header to dropdown
                const header = document.createElement('div');
                header.className = 'dropdown-header card-header';
                header.textContent = 'Recent Notifications';
                dropdown.appendChild(header);

                if (data.length === 0) {
                    const emptyItem = document.createElement('div');
                    emptyItem.className = 'dropdown-item text-muted';
                    emptyItem.textContent = 'No notifications';
                    dropdown.appendChild(emptyItem);
                } else {
                    data.forEach(notification => {
                        const item = document.createElement('a');
                        item.className = 'dropdown-item notification-item';
                        
                        // Add styling based on notification type
                        if (notification.is_read === 0) {
                            item.classList.add('unread');
                        }
                        
                        // Add specific classes for different notification types
                        switch (notification.type) {
                            case 'new_booking':
                                item.classList.add('notification-new');
                                break;
                            case 'booking_confirmed':
                                item.classList.add('notification-confirmed');
                                break;
                            case 'booking_cancelled':
                                item.classList.add('notification-cancelled');
                                // Make cancellation notifications more prominent
                                if (notification.is_read === 0) {
                                    item.style.borderLeft = '4px solid #dc3545';
                                    item.style.backgroundColor = '#fff5f5';
                                }
                                break;
                        }
                        
                        item.href = 'room_reservations.php?id=' + notification.id;
                        item.dataset.notificationId = notification.id;
                        item.dataset.notificationType = notification.type;
                        
                        // Create notification icon based on type
                        const icon = document.createElement('span');
                        icon.className = 'notification-icon me-2';
                        switch (notification.type) {
                            case 'new_booking':
                                icon.innerHTML = '📅';
                                break;
                            case 'booking_confirmed':
                                icon.innerHTML = '✅';
                                break;
                            case 'booking_cancelled':
                                icon.innerHTML = '❌';
                                break;
                            default:
                                icon.innerHTML = '🔔';
                        }
                        
                        // Create main text element
                        const text = document.createElement('div');
                        text.className = 'notification-text dark-text';
                        text.textContent = notification.message;
                        
                        // Create time element
                        const time = document.createElement('small');
                        time.className = 'notification-time text-muted';
                        const date = new Date(notification.updated_at || notification.created_at);
                        time.textContent = formatTimeAgo(date);
                        
                        // Create notification header with icon and time
                        const notificationHeader = document.createElement('div');
                        notificationHeader.className = 'd-flex justify-content-between align-items-start';
                        
                        const iconAndText = document.createElement('div');
                        iconAndText.className = 'd-flex align-items-start';
                        iconAndText.appendChild(icon);
                        iconAndText.appendChild(text);
                        
                        notificationHeader.appendChild(iconAndText);
                        notificationHeader.appendChild(time);
                        
                        // Append elements
                        item.appendChild(notificationHeader);
                        dropdown.appendChild(item);

                        // Add click event to mark as read
                        item.addEventListener('click', function(e) {
                            markAsRead(notification.id);
                        });
                    });
                    
                    // Add "View all" link
                    const viewAll = document.createElement('a');
                    viewAll.className = 'dropdown-item text-center text-primary view-all';
                    viewAll.href = 'room_reservations.php';
                    viewAll.textContent = 'View all reservations';
                    dropdown.appendChild(viewAll);
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                // Show error in dropdown
                const dropdown = document.querySelector('.notification-dropdown');
                dropdown.innerHTML = '<div class="dropdown-item text-danger">Error loading notifications</div>';
            });
    }
    
    // Function to mark notification as read
    function markAsRead(id) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('../api/mark_notifications_read.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh notifications after marking as read
                fetchNotifications();
            } else {
                console.error('Error marking notification as read:', data.error);
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Function to mark all notifications as read
    function markAllAsRead() {
        fetch('../api/mark_all_notifications_read.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            } else {
                console.error('Error marking all notifications as read:', data.error);
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
    
    // Helper function to format time ago
    function formatTimeAgo(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) {
            return 'just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return minutes + (minutes === 1 ? ' minute ago' : ' minutes ago');
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return hours + (hours === 1 ? ' hour ago' : ' hours ago');
        } else if (diffInSeconds < 7 * 86400) {
            const days = Math.floor(diffInSeconds / 86400);
            return days + (days === 1 ? ' day ago' : ' days ago');
        } else {
            // For older notifications, show actual date
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
            });
        }
    }

    // Add toggle behavior for notification dropdown
    const notificationLink = document.getElementById('notificationLink');
    const dropdown = document.querySelector('.notification-dropdown');
    
    if (notificationLink) {
        notificationLink.addEventListener('click', function(e) {
            e.preventDefault();
            dropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationLink.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    // Add notification sound for cancellations
    function playNotificationSound() {
        if (document.hasFocus()) {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmkfBkGX1+mLaB4FJ3vI7uKTQAoTXbPp7KpUEQlEmt/twnInBC13w+zfiT0HEF+u5OyrUQ4LR5rd7sV0KAUvdsHw34Y7CBBfruT1lDEMSJrf7sV0KAUvdsH');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Could not play notification sound'));
        }
    }

    // Store last notification count to detect new notifications
    let lastNotificationCount = 0;
    let lastCancellationId = null;

    // Enhanced fetch with cancellation detection
    function fetchNotificationsWithSound() {
        fetch('../api/notifications.php')
            .then(response => response.json())
            .then(data => {
                // Check for new cancellations
                const cancellations = data.filter(n => n.type === 'booking_cancelled' && n.is_read === 0);
                if (cancellations.length > 0) {
                    const latestCancellation = cancellations[0];
                    if (lastCancellationId !== latestCancellation.id) {
                        playNotificationSound();
                        lastCancellationId = latestCancellation.id;
                    }
                }
                
                // Update the UI
                return fetchNotifications();
            })
            .catch(error => {
                console.error('Error fetching notifications with sound:', error);
            });
    }

    // Fetch on page load and every 30 seconds
    fetchNotifications();
    setInterval(fetchNotificationsWithSound, 30000);
    
    // Make markAllAsRead available globally
    window.markAllAsRead = markAllAsRead;
});