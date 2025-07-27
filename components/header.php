<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <img src="../images/final.png" alt="" class="me-3" style="height: 50px;">
        <a class="navbar-brand" id="navbarTitle" href="home.php">
            Hotel & Restaurant Management System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="home.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="bookingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-book"></i> Bookings
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="bookingsDropdown">
                        <li><a class="dropdown-item" href="restaurantTableBooking.php"> <i class="fas fa-table"></i> Book a Table</a></li>
                        <li><a class="dropdown-item" href="roomBookings.php"><i class="fas fa-bed"></i> Book a Room</a></li>  
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="bookingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-book"></i> My Bookings
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="bookingsDropdown">
                        <li><a class="dropdown-item" href="bookings.php"><i class="fas fa-bed"></i> My Room Bookings</a></li>
                        <li><a class="dropdown-item" href="tableBookings.php"><i class="fas fa-table"></i> My Table Bookings</a></li>
                        <!-- <li><a class="dropdown-item" href="#">Cancelled Bookings</a></li> -->
                    </ul>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link position-relative" aria-current="page" href="#" id="notificationLink">
                        <i class="fas fa-bell"></i> Notifications
                        <span class="notification-badge"></span>
                    </a>
                    <div class="notification-dropdown"></div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a href="reviews.php" class="dropdown-item"><i class="fas fa-comment me-1"></i>Reviews</a></li>
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