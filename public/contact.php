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
    <title> Contact Us - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/buttonAnimation.css">
    <style>
        .btn-pulse {
            animation: pulse 1.5s infinite;
            position: relative;
            overflow: hidden;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }

        /* Button transition */
        .contact-us button[type="submit"] {
            transition: all 0.3s ease;
        }

        /* Success state */
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            transform: scale(1.05);
        }

        /* Optional ripple effect */
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
        }

        @keyframes ripple {
            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <!-- Contact Us Section -->
    <section class="contact-us py-5">
        <div class="container">
            <h2 class="text-center mb-4 mt-4 card-title text-muted">Contact Us</h2>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <img src="../images/loginbg.jpg" alt="Contact Us" class="img-fluid rounded shadow mb-4">
                </div>
                <div class="col-lg-6">
                    <form action="../controllers/sendConcern.php" method="POST" class="p-4 border rounded shadow">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter your subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" rows="5" class="form-control" placeholder="Enter your message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send <i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="../js/imageSwiper.js"></script>
    <script src="../js/bannerSwipper.js"></script>
    <script src="../js/booking.js"></script>
    <script src="../js/animation.js"></script>
    <script src="../js/contactFormAnimation.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>