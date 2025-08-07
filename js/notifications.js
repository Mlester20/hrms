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
                        if (notification.is_read === 0) {
                            item.classList.add('unread');
                        }
                        item.href = 'room_reservations.php?id=' + notification.id;
                        item.dataset.notificationId = notification.id;
                        
                        // Create main text element
                        const text = document.createElement('div');
                        text.className = 'notification-text';
                        text.textContent = notification.message;
                        
                        // Create time element
                        const time = document.createElement('small');
                        time.className = 'notification-time';
                        const date = new Date(notification.created_at);
                        time.textContent = formatTimeAgo(date);
                        
                        // Append elements
                        item.appendChild(text);
                        item.appendChild(time);
                        dropdown.appendChild(item);

                        // Add click event to mark as read
                        item.addEventListener('click', function(e) {
                            // Don't prevent default navigation here, we still want to follow the link
                            markAsRead(notification.id);
                        });
                    });
                    
                    // Add divider and "View all" link
                    // const divider = document.createElement('div');
                    // divider.className = 'dropdown-divider';
                    // dropdown.appendChild(divider);
                    
                    const viewAll = document.createElement('a');
                    viewAll.className = 'dropdown-item text-center text-primary view-all';
                    viewAll.href = 'room_reservations.php';
                    viewAll.textContent = 'View all reservations';
                    dropdown.appendChild(viewAll);
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
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
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return days + (days === 1 ? ' day ago' : ' days ago');
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

    // Fetch on page load and every 30 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});