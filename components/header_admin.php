<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <img src="../images/remove.png" alt="" class="me-3" style="height: 50px;">
        <a class="navbar-brand" id="navbarTitle" href="dashboard.php">
            Hotel & Restaurant Management System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="dashboard.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <!-- dropdown for entries -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="entriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-folder"></i> Entries
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="entriesDropdown">
                        <li><a class="dropdown-item" href="manageRooms.php"><i class="fas fa-door-closed"></i> Rooms</a></li>
                        <li><a class="dropdown-item" href="roomType.php"><i class="fas fa-door-open"></i> Room type</a></li>
                        <li><a class="dropdown-item" href="staffs.php"><i class="fas fa-users"></i> Manage Staffs</a></li>
                        <li><a class="dropdown-item" href="shifts.php"><i class="fas fa-clock"></i> Shifts</a></li>
                        <li><a class="dropdown-item" href="banners.php"><i class="fas fa-users"></i> Banners</a></li>
                        <li><a class="dropdown-item" href="description.php"><i class="fas fa-book"></i> Description</a></li>
                        <li><a class="dropdown-item" href="specialOffers.php"><i class="fas fa-users"></i> Special Offers</a></li>
                        <li><a class="dropdown-item" href="restaurantMenu.php"><i class="fas fa-utensils"></i> Restaurant Menus</a></li>
                    </ul>
                </li>

                <!-- dropdown for reservations -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="entriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar"></i> Bookings
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="entriesDropdown">
                        <li><a class="dropdown-item" href="room_reservations.php"><i class="fas fa-door-open"></i> Room Bookings</a></li>
                        <li><a class="dropdown-item" href="canceledBooks.php"><i class="fas fa-utensils"></i> Cancelled Books</a></li>
                        <li><a class="dropdown-item" href="tablesReservation.php"><i class="fas fa-utensils"></i> Restaurant Reservations</a></li>

                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-cog"></i> Settings</a></li>
                        
                        <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Function to update the navbar title based on screen size
    function updateNavbarTitle() {
        const navbarTitle = document.getElementById('navbarTitle');
        if (window.innerWidth <= 768) {
            navbarTitle.textContent = 'HRMS'; // Mobile view title
        } else {
            navbarTitle.textContent = 'Hotel & Restaurant Management System'; // Desktop view title
        }
    }
    updateNavbarTitle();

    window.addEventListener('resize', updateNavbarTitle);
</script>