<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <img src="../images/remove.png" alt="" class="me-3" style="height: 50px;">
        <a class="navbar-brand" id="navbarTitle" href="home.php">
            Hotel & Restaurant Management System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" aria-current="page" href="home.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars('../uploads/' . basename($_SESSION['profile'])); ?>" 
                            alt="Profile" 
                            class="rounded-circle" 
                            style="width: 32px; height: 32px; object-fit: cover;">
                        <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
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

<style>
/* Add these styles to fix the alignment issue */
.navbar-nav .nav-item {
    display: flex;
    align-items: center;
}

.nav-link {
    display: flex;
    align-items: center;
}

/* Keep your existing styles */
.nav-link img.rounded-circle {
    border: 2px solid transparent;
    transition: all 0.3s ease;
    margin-right: 8px; /* Add some spacing between image and text */
}

.nav-link:hover img.rounded-circle {
    border-color: #007bff;
    transform: scale(1.1);
}

/* Dropdown menu styling */
.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>