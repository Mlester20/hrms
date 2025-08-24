    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-light: #ecf0f1;
            --hover-color: #34495e;
            --notification-color: #f39c12;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        .custom-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--hover-color) 100%);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-md);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .custom-navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .navbar-brand {
            color: var(--text-light) !important;
            font-weight: 700;
            font-size: 1.4rem;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .navbar-brand:hover {
            color: var(--secondary-color) !important;
            transform: translateY(-1px);
        }

        .logo-image {
            height: 45px;
            width: auto;
            margin-right: 0.75rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .logo-image:hover {
            transform: rotate(5deg) scale(1.05);
        }

        .navbar-toggler {
            border: none;
            padding: 0.4rem;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            transition: var(--transition);
        }

        .navbar-toggler:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }

        .navbar-toggler-icon {
            background-image: none;
            width: 24px;
            height: 24px;
            position: relative;
        }

        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after,
        .navbar-toggler-icon {
            transition: var(--transition);
        }

        .navbar-toggler-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--text-light);
            border-radius: 2px;
        }

        .navbar-toggler-icon::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--text-light);
            border-radius: 2px;
        }

        .navbar-toggler-icon {
            background: var(--text-light);
            height: 3px !important;
            border-radius: 2px;
        }

        .navbar-nav .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
            padding: 0.6rem 1rem !important;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-nav .nav-link i {
            margin-right: 0.4rem;
            font-size: 1.1em;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--secondary-color);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .dropdown-menu {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            padding: 0.6rem 1.2rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            color: var(--primary-color);
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            width: 16px;
            text-align: center;
        }

        .dropdown-item:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateX(4px);
        }

        .notification-container {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1040;
        }

        .notification-dropdown.show {
            display: block;
            animation: fadeInUp 0.3s ease;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .navbar-nav {
                background: rgba(0,0,0,0.1);
                border-radius: 12px;
                padding: 1rem;
                margin-top: 1rem;
            }

            .navbar-nav .nav-link {
                margin: 0.2rem 0;
                padding: 0.8rem 1rem !important;
            }

            .dropdown-menu {
                position: static !important;
                transform: none !important;
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
                background: rgba(255,255,255,0.1);
                margin: 0.5rem 0;
            }

            .dropdown-item {
                color: var(--text-light);
            }

            .dropdown-item:hover {
                background: rgba(255,255,255,0.2);
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .logo-image {
                height: 35px;
            }

            .notification-dropdown {
                width: 280px;
                right: -50px;
            }
        }

        /* Loading states */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid var(--text-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <nav class="navbar navbar-expand-lg custom-navbar" role="navigation">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php" aria-label="Hotel & Restaurant Management System Home">
                <img src="https://via.placeholder.com/45x45/3498db/ffffff?text=HRMS" 
                     alt="HRMS Logo" 
                     class="logo-image" 
                     loading="lazy">
                <span id="navbarTitle">Hotel & Restaurant Management System</span>
            </a>
            
            <button class="navbar-toggler" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" role="menubar">
                    <li class="nav-item" role="none">
                        <a class="nav-link active" 
                           href="home.php" 
                           role="menuitem"
                           aria-current="page">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Home</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown" role="none">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           id="bookingsDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false"
                           aria-haspopup="true">
                            <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                            <span>New Booking</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="bookingsDropdown" role="menu">
                            <li role="none">
                                <a class="dropdown-item" href="restaurantTableBooking.php" role="menuitem">
                                    <i class="fas fa-utensils" aria-hidden="true"></i>
                                    Book a Table
                                </a>
                            </li>
                            <li role="none">
                                <a class="dropdown-item" href="roomBookings.php" role="menuitem">
                                    <i class="fas fa-bed" aria-hidden="true"></i>
                                    Book a Room
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown" role="none">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           id="myBookingsDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false"
                           aria-haspopup="true">
                            <i class="fas fa-list" aria-hidden="true"></i>
                            <span>My Bookings</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="myBookingsDropdown" role="menu">
                            <li role="none">
                                <a class="dropdown-item" href="bookings.php" role="menuitem">
                                    <i class="fas fa-bed" aria-hidden="true"></i>
                                    Room Bookings
                                </a>
                            </li>
                            <li role="none">
                                <a class="dropdown-item" href="tableBookings.php" role="menuitem">
                                    <i class="fas fa-utensils" aria-hidden="true"></i>
                                    Table Bookings
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item notification-container" role="none">
                        <a class="nav-link" 
                           href="#" 
                           id="notificationLink"
                           role="button"
                           aria-label="Notifications"
                           aria-expanded="false">
                            <i class="fas fa-bell" aria-hidden="true"></i>
                            <span class="d-lg-none">Notifications</span>
                            <span class="notification-badge" id="notificationCount">3</span>
                        </a>
                        <div class="notification-dropdown" id="notificationDropdown" role="menu">
                            <div class="notification-item" role="menuitem">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Booking Confirmed</div>
                                        <small class="text-muted">Room 101 for Jan 15-17</small>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item" role="menuitem">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Payment Reminder</div>
                                        <small class="text-muted">Due in 2 days</small>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item" role="menuitem">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-gift text-info me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Special Offer</div>
                                        <small class="text-muted">20% off weekend stays</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center p-2 border-top">
                                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown" role="none">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           id="userDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false"
                           aria-haspopup="true">
                            <div class="user-avatar">JD</div>
                            <span>John Doe</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" role="menu">
                            <li role="none">
                                <a class="dropdown-item" href="profile.php" role="menuitem">
                                    <i class="fas fa-user-cog" aria-hidden="true"></i>
                                    Profile Settings
                                </a>
                            </li>
                            <li role="none">
                                <a class="dropdown-item" href="reviews.php" role="menuitem">
                                    <i class="fas fa-star" aria-hidden="true"></i>
                                    My Reviews
                                </a>
                            </li>
                            <li role="none">
                                <hr class="dropdown-divider">
                            </li>
                            <li role="none">
                                <a class="dropdown-item text-danger" 
                                   href="logout.php" 
                                   role="menuitem"
                                   onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>