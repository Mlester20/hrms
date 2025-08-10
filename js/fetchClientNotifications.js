class NotificationManager {
    constructor() {
        this.notificationLink = document.getElementById('notificationLink');
        this.notificationBadge = document.querySelector('.notification-badge');
        this.notificationDropdown = document.querySelector('.notification-dropdown');
        this.isDropdownOpen = false;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadNotifications();
        this.startPolling();
        this.setupDropdown();
    }
    
    setupEventListeners() {
        // Toggle dropdown when clicking notification link
        this.notificationLink.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleDropdown();
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-item') && this.isDropdownOpen) {
                this.closeDropdown();
            }
        });
    }
    
    setupDropdown() {
        // Add styles for dropdown
        this.notificationDropdown.style.cssText = `
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            max-height: 400px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            overflow: hidden;
        `;
        
        // Make nav-item relative positioned
        this.notificationLink.closest('.nav-item').style.position = 'relative';
    }
    
    async loadNotifications() {
        try {
            const response = await fetch('../api/fetchClientNotifications.php?action=fetch');
            const data = await response.json();
            
            if (data.success) {
                this.updateBadge(data.unread_count);
                this.renderNotifications(data.notifications);
            } else {
                console.error('Failed to load notifications:', data.error);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
    
    async updateNotificationCount() {
        try {
            const response = await fetch('../api/fetchClientNotifications.php?action=get_count');
            const data = await response.json();
            
            if (data.success) {
                this.updateBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Error updating notification count:', error);
        }
    }
    
    updateBadge(count) {
        if (count > 0) {
            this.notificationBadge.textContent = count > 99 ? '99+' : count;
            this.notificationBadge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background: #dc3545;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 11px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            `;
        } else {
            this.notificationBadge.style.display = 'none';
        }
    }
    
    renderNotifications(notifications) {
        let html = `
            <div class="notification-header">
                <h6 class="m-0 p-3 border-bottom">Notifications</h6>
                ${notifications.filter(n => !n.is_read).length > 0 ? 
                    '<button class="btn btn-sm btn-link mark-all-read" style="position: absolute; right: 10px; top: 8px;">Mark all read</button>' : ''}
            </div>
            <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
        `;
        
        if (notifications.length === 0) {
            html += `
                <div class="text-center p-4 text-muted">
                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                    <p>No notifications yet</p>
                </div>
            `;
        } else {
            notifications.forEach((notification, index) => {
                const isUnread = !notification.is_read;
                const typeIcon = this.getTypeIcon(notification.type);
                const typeColor = this.getTypeColor(notification.type);
                
                html += `
                    <div class="notification-item ${isUnread ? 'unread' : ''}" 
                         data-id="${notification.notification_id}"
                         style="padding: 12px 16px; border-bottom: 1px solid #f1f1f1; cursor: pointer; ${isUnread ? 'background-color: #f8f9ff;' : ''}">
                        <div class="d-flex">
                            <div class="notification-icon me-3" style="color: ${typeColor};">
                                <i class="${typeIcon}"></i>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <h6 class="mb-1" style="font-size: 14px; font-weight: ${isUnread ? '600' : '500'};">
                                    ${notification.title}
                                </h6>
                                <p class="mb-1 text-muted" style="font-size: 13px; line-height: 1.4;">
                                    ${notification.message}
                                </p>
                                <small class="text-muted" style="font-size: 11px;">
                                    ${notification.time_ago}
                                </small>
                            </div>
                            ${isUnread ? '<div class="unread-indicator" style="width: 8px; height: 8px; background: #007bff; border-radius: 50%; margin-left: 8px; margin-top: 6px;"></div>' : ''}
                        </div>
                    </div>
                `;
            });
        }
        
        html += `
            </div>
            ${notifications.length > 0 ? '<div class="notification-footer p-2 border-top text-center"><a href="#" class="btn btn-sm btn-outline-primary">View All</a></div>' : ''}
        `;
        
        this.notificationDropdown.innerHTML = html;
        this.attachDropdownListeners();
    }
    
    attachDropdownListeners() {
        // Mark individual notification as read
        const notificationItems = this.notificationDropdown.querySelectorAll('.notification-item.unread');
        notificationItems.forEach(item => {
            item.addEventListener('click', () => {
                const notificationId = item.dataset.id;
                this.markAsRead(notificationId);
                item.classList.remove('unread');
                item.style.backgroundColor = '';
                item.querySelector('.unread-indicator')?.remove();
            });
        });
        
        // Mark all as read
        const markAllBtn = this.notificationDropdown.querySelector('.mark-all-read');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
            });
        }
    }
    
    async markAsRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);
            
            const response = await fetch('../api/fetchClientNotifications.php?action=mark_read', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationCount();
            } else {
                console.error('Failed to mark as read:', data.error);
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const response = await fetch('../api/fetchClientNotifications.php?action=mark_all_read', {
                method: 'POST'
            });
            
            const data = await response.json();
            if (data.success) {
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    toggleDropdown() {
        if (this.isDropdownOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        this.notificationDropdown.style.display = 'block';
        this.isDropdownOpen = true;
        this.loadNotifications();
    }
    
    closeDropdown() {
        this.notificationDropdown.style.display = 'none';
        this.isDropdownOpen = false;
    }
    
    getTypeIcon(type) {
        const icons = {
            'booking_confirmed': 'fas fa-check-circle',
            'booking_cancelled': 'fas fa-times-circle',
            'booking_rejected': 'fas fa-exclamation-triangle',
            'payment_received': 'fas fa-credit-card',
            'general': 'fas fa-info-circle'
        };
        return icons[type] || icons.general;
    }
    
    getTypeColor(type) {
        const colors = {
            'booking_confirmed': '#28a745',
            'booking_cancelled': '#dc3545',
            'booking_rejected': '#fd7e14',
            'payment_received': '#17a2b8',
            'general': '#6c757d'
        };
        return colors[type] || colors.general;
    }
    
    startPolling() {
        // Poll for new notifications every 30 seconds
        setInterval(() => {
            this.updateNotificationCount();
        }, 30000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Check if required elements exist
    const notificationLink = document.getElementById('notificationLink');
    const notificationBadge = document.querySelector('.notification-badge');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    
    if (!notificationLink) {
        console.error('Element with ID "notificationLink" not found!');
        return;
    }
    
    if (!notificationBadge) {
        console.error('Element with class "notification-badge" not found!');
        return;
    }
    
    if (!notificationDropdown) {
        console.error('Element with class "notification-dropdown" not found!');
        return;
    }
    
    new NotificationManager();
});

// Add some CSS styles to head
const style = document.createElement('style');
style.textContent = `
    .notification-item:hover {
        background-color: #f8f9fa !important;
    }
    
    .notification-item.unread {
        position: relative;
    }
    
    .mark-all-read {
        text-decoration: none !important;
        font-size: 12px !important;
        padding: 2px 8px !important;
    }
    
    .notification-content h6 {
        margin-bottom: 4px !important;
    }
    
    .notification-content p {
        margin-bottom: 4px !important;
    }
    
    .notification-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .notification-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
`;
document.head.appendChild(style);