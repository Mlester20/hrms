<!-- Mobile Toggle Button -->
<button class="mobile-toggle" id="mobileToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <img src="../images/final.png" alt="Logo">
            <span class="brand-text" id="brandText">HRMS</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <div class="sidebar-nav">
        <!-- Home -->
        <div class="nav-item">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-home"></i>
                <span class="nav-text">Home</span>
            </a>
        </div>

        <!-- Lists Dropdown -->
        <div class="nav-item nav-dropdown">
            <a href="#" class="nav-link dropdown-toggle collapsed"
               data-bs-toggle="collapse"
               data-bs-target="#listsMenu"
               aria-expanded="false">
                <i class="fas fa-folder"></i>
                <span class="nav-text">Lists</span>
            </a>
            <div class="collapse" id="listsMenu">
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="manageRooms.php">
                        <i class="fas fa-door-closed"></i> Rooms
                    </a>
                    <a class="dropdown-item" href="roomType.php">
                        <i class="fas fa-door-open"></i> Room type
                    </a>
                    <a class="dropdown-item" href="concerns.php">
                        <i class="fas fa-exclamation-circle"></i> Customer's Concern
                    </a>
                    <a class="dropdown-item" href="description.php">
                        <i class="fas fa-book"></i> Description
                    </a>
                    <a class="dropdown-item" href="specialOffers.php">
                        <i class="fas fa-gift"></i> Special Offers
                    </a>
                    <a class="dropdown-item" href="restaurantMenu.php">
                        <i class="fas fa-utensils"></i> Restaurant Menus
                    </a>
                    <a class="dropdown-item" href="manageTables.php">
                        <i class="fas fa-table"></i> Manage Restaurant Tables
                    </a>
                    <a class="dropdown-item" href="manageUsers.php">
                        <i class="fas fa-user-cog"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-item nav-dropdown">
            <a href="#" class="nav-link dropdown-toggle collapsed"
               data-bs-toggle="collapse"
               data-bs-target="#staffMenu"
               aria-expanded="false">
                <i class="fas fa-folder"></i>
                <span class="nav-text">Employees</span>
            </a>
            <div class="collapse" id="staffMenu">
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="shifts.php">
                        <i class="fas fa-clock"></i> Shifts
                    </a>
                    <a class="dropdown-item" href="staffs.php">
                        <i class="fas fa-users"></i> Manage Staffs
                    </a>
                    <a class="dropdown-item" href="employeeTask.php">
                        <i class="fas fa-tasks"></i> Add Employee Task
                    </a>
                </div>
            </div>
        </div>

        <!-- Bookings Dropdown -->
        <div class="nav-item nav-dropdown">
            <a href="#" class="nav-link dropdown-toggle collapsed"
               data-bs-toggle="collapse"
               data-bs-target="#bookingsMenu"
               aria-expanded="false">
                <i class="fas fa-calendar"></i>
                <span class="nav-text">Bookings</span>
            </a>
            <div class="collapse" id="bookingsMenu">
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="room_reservations.php">
                        <i class="fas fa-bed"></i> Room Bookings
                    </a>
                    <a class="dropdown-item" href="tablesReservation.php">
                        <i class="fas fa-utensils"></i> Restaurant Reservations
                    </a>
                    <a class="dropdown-item" href="canceledBooks.php">
                        <i class="fas fa-times-circle"></i> Cancelled Books
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="nav-item">
            <a href="#" class="nav-link position-relative" id="notificationLink">
                <i class="fas fa-bell"></i>
                <span class="nav-text">Notifications</span>
                <span class="notification-badge">3</span>
            </a>
        </div>
        
    </div>

    <!-- User Section -->
    <div class="user-section">
        <div class="nav-item nav-dropdown">
            <a href="#" class="nav-link dropdown-toggle collapsed"
               data-bs-toggle="collapse"
               data-bs-target="#userMenu"
               aria-expanded="false">
                <i class="fas fa-user"></i>
                <span class="nav-text"> <?php echo htmlspecialchars($_SESSION['name']); ?> </span>
            </a>
            <div class="collapse" id="userMenu">
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a class="dropdown-item" href="#" id="themeToggle">
                        <i class="fas fa-moon"></i> <span id="themeText">Dark Mode</span>
                    </a>
                    <a class="dropdown-item" href="logout.php"
                       onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
