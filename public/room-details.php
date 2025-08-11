<?php
session_start();
include '../components/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get room id from URL
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$room = null;
if ($room_id > 0) {
    $sql = "SELECT r.*, rt.title as type_title, rt.detail as type_detail
            FROM rooms r
            LEFT JOIN room_type rt ON r.room_type_id = rt.id
            WHERE r.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
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
    <link rel="stylesheet" href="../css/animation.css">
    <link rel="stylesheet" href="../css/reviews.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-5 d-flex flex-column align-items-center">
        <h2 class="text-center mb-4">Room Details</h2>

        <?php if ($room): ?>
            <div class="card shadow p-4" style="max-width: 700px; width: 100%;">
                <div class="card-body text-center">
                    <h3 class="card-title mb-3"><?php echo htmlspecialchars($room['title'] ?? 'Unnamed Room'); ?></h3>

                    <?php if (!empty($room['images'])): ?>
                        <div class="images-container mb-4">
                            <?php
                                $images_string = $room['images'];
                                $images_string = str_replace(['[', ']', '"'], '', $images_string);
                                $images = explode(',', $images_string);
                                $first_image = trim($images[0]);
                                if (!empty($first_image)):
                            ?>
                                <img src="../uploads/<?php echo $first_image; ?>" alt="Room Image" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row text-start">
                        <div class="col-md-6 mb-2">
                            <strong>Price:</strong> ₱<?php echo htmlspecialchars($room['price'] ?? '0'); ?> per night
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Type:</strong> <?php echo htmlspecialchars($room['type_title'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Includes:</strong><br>
                            <?php echo nl2br(htmlspecialchars($room['includes'] ?? '')); ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Details:</strong><br>
                            <?php echo nl2br(htmlspecialchars($room['type_detail'] ?? '')); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-danger">Room not found.</p>
        <?php endif; ?>
    </div>



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