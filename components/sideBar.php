<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-content">
    <!-- Toggle Button -->
    <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarContent" aria-expanded="true">
      <i class="fas fa-bars"></i>
    </button>
    
    <!-- Collapsible Content -->
    <div class="collapse show" id="sidebarContent">
      <!-- Dashboard/Home -->
      <div class="sidebar-item active">
        <i class="fas fa-home"></i>
        <span class="sidebar-text">Dashboard</span>
      </div>
      
      <!-- Lists Dropdown -->
      <div class="sidebar-item dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#listsSubmenu" aria-expanded="false">
        <i class="fas fa-folder"></i>
        <span class="sidebar-text">Lists</span>
        <i class="fas fa-chevron-right sidebar-arrow"></i>
      </div>
      <div class="collapse sidebar-submenu" id="listsSubmenu">
        <a href="manageRooms.php" class="sidebar-subitem">
          <i class="fas fa-door-closed"></i>
          <span class="sidebar-text">Rooms</span>
        </a>
        <a href="roomType.php" class="sidebar-subitem">
          <i class="fas fa-door-open"></i>
          <span class="sidebar-text">Room Types</span>
        </a>
        <a href="staffs.php" class="sidebar-subitem">
          <i class="fas fa-users"></i>
          <span class="sidebar-text">Manage Staffs</span>
        </a>
        <a href="shifts.php" class="sidebar-subitem">
          <i class="fas fa-clock"></i>
          <span class="sidebar-text">Shifts</span>
        </a>
        <a href="concerns.php" class="sidebar-subitem">
          <i class="fas fa-exclamation-circle"></i>
          <span class="sidebar-text">Customer Concerns</span>
        </a>
        <a href="description.php" class="sidebar-subitem">
          <i class="fas fa-book"></i>
          <span class="sidebar-text">Description</span>
        </a>
        <a href="specialOffers.php" class="sidebar-subitem">
          <i class="fas fa-tag"></i>
          <span class="sidebar-text">Special Offers</span>
        </a>
        <a href="restaurantMenu.php" class="sidebar-subitem">
          <i class="fas fa-utensils"></i>
          <span class="sidebar-text">Restaurant Menus</span>
        </a>
        <a href="manageTables.php" class="sidebar-subitem">
          <i class="fas fa-table"></i>
          <span class="sidebar-text">Restaurant Tables</span>
        </a>
        <a href="employeeTask.php" class="sidebar-subitem">
          <i class="fas fa-hammer"></i>
          <span class="sidebar-text">Employee Tasks</span>
        </a>
        <a href="manageUsers.php" class="sidebar-subitem">
          <i class="fas fa-users-cog"></i>
          <span class="sidebar-text">Manage Users</span>
        </a>
      </div>
      
      <!-- Bookings Dropdown -->
      <div class="sidebar-item dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#bookingsSubmenu" aria-expanded="false">
        <i class="fas fa-calendar"></i>
        <span class="sidebar-text">Bookings</span>
        <i class="fas fa-chevron-right sidebar-arrow"></i>
      </div>
      <div class="collapse sidebar-submenu" id="bookingsSubmenu">
        <a href="room_reservations.php" class="sidebar-subitem">
          <i class="fas fa-bed"></i>
          <span class="sidebar-text">Room Bookings</span>
        </a>
        <a href="tablesReservation.php" class="sidebar-subitem">
          <i class="fas fa-utensils"></i>
          <span class="sidebar-text">Restaurant Reservations</span>
        </a>
        <a href="canceledBooks.php" class="sidebar-subitem">
          <i class="fas fa-times-circle"></i>
          <span class="sidebar-text">Cancelled Books</span>
        </a>
      </div>
      
      <!-- Notifications -->
      <div class="sidebar-item position-relative">
        <i class="fas fa-bell"></i>
        <span class="sidebar-text">Notifications</span>
        <span class="notification-badge-sidebar"></span>
      </div>
    </div>
    
    <!-- User Profile at Bottom -->
    <div class="sidebar-bottom">
      <div class="collapse show" id="sidebarContent">
        <!-- Settings -->
        <div class="sidebar-item">
          <i class="fas fa-cog"></i>
          <span class="sidebar-text">Settings</span>
        </div>
        
        <!-- Theme Toggle -->
        <div class="sidebar-item" id="sidebarThemeToggle">
          <i class="fas fa-moon"></i>
          <span class="sidebar-text">Dark Mode</span>
        </div>
        
        <!-- User Profile -->
        <div class="sidebar-item user-profile">
          <div class="user-avatar">
            <i class="fas fa-user"></i>
          </div>
          <span class="sidebar-text">Profile</span>
        </div>
        
        <!-- Logout -->
        <div class="sidebar-item logout-item" onclick="confirmLogout()">
          <i class="fas fa-sign-out-alt"></i>
          <span class="sidebar-text">Logout</span>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  background: linear-gradient(180deg, #2c2c2c 0%, #1a1a1a 100%);
  border-right: 1px solid #333;
  z-index: 1000;
  transition: width 0.3s ease;
  width: 250px;
}

.sidebar.collapsed {
  width: 60px;
}

.sidebar-content {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 1rem 0;
}

.sidebar-toggle {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.2rem;
  padding: 0.75rem;
  margin: 0 0.5rem 1rem 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar-toggle:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.sidebar-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  margin: 0.25rem 0.5rem;
  border-radius: 8px;
  color: #ccc;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.sidebar-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.sidebar-item.active {
  background-color: #6366f1;
  color: #fff;
}

.sidebar-item i {
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
  margin-right: 0.75rem;
}

.claude-icon {
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  font-weight: bold;
  font-size: 0.8rem;
}

.sidebar-text {
  font-size: 0.9rem;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
}

.sidebar-bottom {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #333;
}

.user-profile {
  margin-bottom: 0;
}

.user-avatar {
  width: 20px;
  height: 20px;
  margin-right: 0.75rem;
}

.user-avatar img {
  width: 100%;
  height: 100%;
}

/* Collapsed state */
.sidebar.collapsed .sidebar-text {
  display: none;
}

.sidebar.collapsed .sidebar-item {
  justify-content: center;
  padding: 0.75rem;
}

.sidebar.collapsed .sidebar-item i,
.sidebar.collapsed .claude-icon,
.sidebar.collapsed .user-avatar {
  margin-right: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar.show {
    transform: translateX(0);
  }
}

/* Dropdown arrows */
.sidebar-arrow {
  font-size: 0.8rem;
  margin-left: auto;
  transition: transform 0.3s ease;
}

.dropdown-toggle[aria-expanded="true"] .sidebar-arrow {
  transform: rotate(90deg);
}

/* Sidebar submenu */
.sidebar-submenu {
  margin-left: 1rem;
  border-left: 2px solid #444;
}

.sidebar-subitem {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  margin: 0.1rem 0;
  color: #bbb;
  text-decoration: none;
  font-size: 0.85rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.sidebar-subitem:hover {
  background-color: rgba(255, 255, 255, 0.08);
  color: #fff;
  text-decoration: none;
}

.sidebar-subitem i {
  font-size: 0.9rem;
  width: 16px;
  text-align: center;
  margin-right: 0.75rem;
}

/* Notification badge for sidebar */
.notification-badge-sidebar {
  position: absolute;
  top: 0.5rem;
  right: 1rem;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 8px;
  height: 8px;
  font-size: 0.6rem;
}

/* Logout item styling */
.logout-item:hover {
  background-color: rgba(220, 53, 69, 0.2) !important;
  color: #dc3545 !important;
}

/* Collapsed state adjustments */
.sidebar.collapsed .sidebar-submenu {
  display: none;
}

.sidebar.collapsed .sidebar-arrow {
  display: none;
}
</style>

<script>
// Toggle sidebar collapse
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.querySelector('.sidebar-toggle');
  
  // Handle collapse/expand
  const sidebarContent = document.getElementById('sidebarContent');
  sidebarContent.addEventListener('hidden.bs.collapse', function () {
    sidebar.classList.add('collapsed');
  });
  
  sidebarContent.addEventListener('shown.bs.collapse', function () {
    sidebar.classList.remove('collapsed');
  });
  
  // Mobile toggle
  if (window.innerWidth <= 768) {
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('show');
    });
  }
  
  // Handle dropdown toggles
  document.querySelectorAll('.sidebar-item.dropdown-toggle').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const target = this.getAttribute('data-bs-target');
      const submenu = document.querySelector(target);
      const arrow = this.querySelector('.sidebar-arrow');
      
      if (submenu) {
        const isCollapsed = !submenu.classList.contains('show');
        
        // Close other submenus
        document.querySelectorAll('.sidebar-submenu.show').forEach(menu => {
          if (menu !== submenu) {
            menu.classList.remove('show');
            const otherArrow = document.querySelector(`[data-bs-target="#${menu.id}"] .sidebar-arrow`);
            if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
          }
        });
        
        // Toggle current submenu
        submenu.classList.toggle('show');
        if (arrow) {
          arrow.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';
        }
      }
    });
  });
  
  // Theme toggle
  const themeToggle = document.getElementById('sidebarThemeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', function() {
      // Add your theme toggle logic here
      const icon = this.querySelector('i');
      const text = this.querySelector('.sidebar-text');
      
      if (icon.classList.contains('fa-moon')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        text.textContent = 'Light Mode';
        // Add dark theme class to body
        document.body.classList.add('dark-theme');
      } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        text.textContent = 'Dark Mode';
        // Remove dark theme class from body
        document.body.classList.remove('dark-theme');
      }
    });
  }
});

// Logout confirmation function
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = 'logout.php';
  }
}
</script>