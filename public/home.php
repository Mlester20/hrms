<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$descriptions = [];
$query = "SELECT description_id, description_name FROM description"; 
$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $descriptions[] = $row;
    }
} else {
    echo "Error fetching descriptions: " . mysqli_error($con);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>
<body>
    
    <?php include '../components/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content" id="heroContent">
            <h1>Explore! Discover! Live!</h1>
            <p>The best hotel for your family!</p>
            <button class="book-now-btn">BOOK OUR ROOMS</button>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="booking-form-container">
        <div class="booking-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Check in</label>
                    <div class="input-with-icon">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="form-group">
                    <label>Check out</label>
                    <div class="input-with-icon">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="form-group">
                    <label>Guests</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" value="2 Persons">
                    </div>
                </div>
                <div class="form-group">
                    <label>Beds</label>
                    <div class="counter-input">
                        <span class="counter-btn">-</span>
                        <span class="counter-value">1</span>
                        <span class="counter-btn">+</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Baths</label>
                    <div class="counter-input">
                        <span class="counter-btn">-</span>
                        <span class="counter-value">1</span>
                        <span class="counter-btn">+</span>
                    </div>
                </div>
                <div class="form-group search-btn-container">
                    <button class="search-btn"><i class="fas fa-search"></i> Search</button>
                </div>
            </div>
        </div>
    </section>

    <!-- About Hotel Section -->
    <section class="about-hotel-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h5>WELCOME TO SEEDS HOTEL</h5>
                        <h2>Our Hotel has been present for over 20 years.</h2>
                        <p class="main-subtitle">We make the best for all our customers.</p>
                        <p class="about-description">
                            <?php 
                            if (!empty($descriptions)) {
                                foreach ($descriptions as $description) {
                                    echo  htmlspecialchars($description['description_name']) . "<br>";
                                }
                            } else {
                                echo "No descriptions available.";
                            }
                            ?>
                        </p>
                        <div class="ceo-signature">
                            <div class="ceo-img">
                                <img src="../images/me.jpg" alt="CEO">
                            </div>
                            <div class="signature-details">
                                <h4>Mark Lester Raguindin</h4>
                                <p>Software Developer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-images">
                        <div class="img-top">
                            <img src="../images/loginbg.jpg" alt="Hotel View" class="img-fluid">
                        </div>
                        <div class="img-bottom">
                            <img src="../images/loginbg.jpg" alt="Hotel Service" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Favorite Rooms Section -->
    <section class="favorite-rooms-section">
        <div class="container">
            <div class="section-title text-center">
                <h5>THE BEST IN YOUR FAMILY OR FRIENDS</h5>
                <h2>Our Gallery</h2>
            </div>
            <div class="room-gallery">
                <div class="row" id="room-gallery-container">
                    <!-- Dynamic content will be injected here -->
                </div>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!-- custom js scripts -->
    <script src="../js/imageSwiper.js"></script>
    <script src="../js/bannerSwipper.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/animation.js"></script>
</body>
</html>