<?php

require_once '../controllers/fetchDescription.php';
require_once '../controllers/fetchSpecialOffers.php';
require_once '../middleware/authMiddleware.php';
requireLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home | <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="../css/animation.css">
    <link rel="stylesheet" href="../css/reviews.css">
</head>
<body>
    
    <?php include '../components/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content fade-in" id="heroContent">
            <h1>Explore! Discover! Live!</h1>
            <p>The best hotel for your family!</p>
            <button class="book-now-btn">BOOK OUR ROOMS</button>
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
                        <!-- <div class="ceo-signature">
                            <div class="ceo-img">
                                <img src="../images/me.jpg" alt="CEO">
                            </div>
                            <div class="signature-details">
                                <h4>Mark Lester Raguindin</h4>
                                <p>Ceo</p>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-images">
                        <div class="img-top">
                            <img src="../images/loginbg.jpg" alt="Hotel View" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Restaurant Section -->
    <section class="restaurant-section py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h5>EXQUISITE DINING EXPERIENCE</h5>
                <h2>Our Restaurant</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="restaurant-content fade-in-left">
                        <h3>Seed's Hotel & Restaurant</h3>
                        <p class="restaurant-tagline">Taste the difference in every bite</p>
                        <p class="restaurant-description">
                            Indulge in a culinary journey at Seed's Hotel & Restaurant, where our expert chefs create masterpieces using locally-sourced ingredients. Our menu features a perfect blend of international cuisine and local flavors, ensuring a memorable dining experience for our guests.
                        </p>
                        <div class="restaurant-features">
                            <div class="feature-item">
                                <i class="fas fa-utensils"></i>
                                <span>Fine Dining</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-glass-cheers"></i>
                                <span>Premium Bar</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-leaf"></i>
                                <span>Organic Ingredients</span>
                            </div>
                        </div>
                        <div class="restaurant-hours mt-4">
                            <h4>Opening Hours</h4>
                            <ul class="list-unstyled">
                                <li><strong>Breakfast:</strong> 6:30 AM - 10:30 AM</li>
                                <li><strong>Lunch:</strong> 12:00 PM - 2:30 PM</li>
                                <li><strong>Dinner:</strong> 6:00 PM - 10:30 PM</li>
                            </ul>
                        </div>
                        <button class="btn btn-primary mt-3">
                            <a href="menu.php" class="text-decoration-none text-reset">View Menu</a>
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="restaurant-images">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="image-container rounded overflow-hidden">
                                    <img src="../images/interior.jpg" alt="Restaurant Interior" class="img-fluid restaurant-img">
                                    <div class="image-overlay">
                                        <span>Restaurant Interior</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="image-container rounded overflow-hidden">
                                    <img src="../images/dish.jpg" alt="Signature Dish" class="img-fluid restaurant-img">
                                    <div class="image-overlay">
                                        <span>Signature Dish</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="image-container rounded overflow-hidden">
                                    <img src="../images/restaurant.jpg" alt="Restaurant View" class="img-fluid restaurant-img">
                                    <div class="image-overlay">
                                        <span>Restaurant View</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Special Offers Carousel -->
            <div class="special-offers mt-5">
                <h3 class="text-center mb-4">Special Offers</h3>

                <?php if (!empty($offers)): ?>
                    <div id="specialOffersCarousel" class="carousel slide" data-bs-ride="carousel">

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <?php foreach ($offers as $key => $offer): ?>
                                <button
                                    type="button"
                                    data-bs-target="#specialOffersCarousel"
                                    data-bs-slide-to="<?= $key ?>"
                                    class="<?= $key === 0 ? 'active' : '' ?>"
                                    <?= $key === 0 ? 'aria-current="true"' : '' ?>
                                    aria-label="Slide <?= $key + 1 ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Carousel Items -->
                        <div class="carousel-inner">
                            <?php foreach ($offers as $key => $offer): ?>
                                <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <img src="../uploads/<?= htmlspecialchars($offer['image']) ?>"
                                                class="d-block w-100 rounded"
                                                alt="<?= htmlspecialchars($offer['title']) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="offer-content p-4">
                                                <h4><?= htmlspecialchars($offer['title']) ?></h4>
                                                <p><?= htmlspecialchars($offer['description']) ?></p>
                                                <p class="offer-price">
                                                    ₱<?= number_format($offer['price'], 2) ?>
                                                </p>
                                                <a href="restaurantTableBooking.php"
                                                class="btn btn-primary btn-lg mt-3">
                                                    <i class="fas fa-utensils"></i> Book a Table
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Controls (only if more than 1) -->
                        <?php if (count($offers) > 1): ?>
                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#specialOffersCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>

                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#specialOffersCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>

                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        No special offers available at the moment.
                    </div>
                <?php endif; ?>
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

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="card-title text-center text-muted">Our Delicious Menu</h1>
                <p class="lead">Explore our carefully crafted dishes made with the freshest ingredients</p>
            </div>
        </div>

        
        <!-- Menu content will be loaded here -->
        <div id="menuContainer" class="row g-4">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer Section -->
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
    <script src="../js/Menus.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>