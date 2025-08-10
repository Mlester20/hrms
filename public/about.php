<?php
session_start();
include '../components/config.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> About Us - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>
<body>
    

    <?php include '../components/header.php'; ?>


    <!-- About Us Section -->
    <section class="about-us py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- About Image -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="../images/loginbg.jpg" alt="About Us" class="img-fluid rounded shadow">
                </div>
                <!-- About Content -->
                <div class="col-lg-6">
                    <h5 class="text-uppercase text-primary mb-3">About Us</h5>
                    <h2 class="mb-4">Welcome to Seeds Hotel</h2>
                    <p class="mb-4">
                        At Seeds Hotel, we have been providing exceptional hospitality services for over 20 years. 
                        Our mission is to create unforgettable experiences for our guests, whether they are traveling 
                        for leisure or business. We pride ourselves on offering luxurious accommodations, world-class 
                        amenities, and personalized service.
                    </p>
                    <p class="mb-4">
                        Our team is dedicated to ensuring your stay is comfortable and enjoyable. From our beautifully 
                        designed rooms to our exquisite dining options, every detail is crafted to exceed your expectations.
                    </p>
                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </section>


    <!-- footer -->
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
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>