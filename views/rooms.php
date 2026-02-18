<?php

require_once '../controllers/fetchRooms.php';
require_once '../middleware/authMiddleware.php';
requireLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> All Rooms | <?php require_once '../includes/title.php'; ?></title>
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
    
    <?php require_once '../components/header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Our Rooms</h2>
        <!-- FILTER BUTTONS -->
        <div class="text-center mb-4">
            <a href="rooms.php" class="btn btn-outline-dark m-1">All</a>
            <?php while($type = mysqli_fetch_assoc($roomTypes)): ?>
                <a href="?type=<?php echo $type['id']; ?>" 
                class="btn btn-outline-primary m-1">
                <?php echo $type['title']; ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($rooms) > 0): ?>
                <?php while($room = mysqli_fetch_assoc($rooms)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <?php 
                                $images = json_decode($room['images'], true);
                            ?>
                            <img src="../uploads/<?php echo $images[0]; ?>" 
                                class="card-img-top" 
                                style="height: 220px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo $room['title']; ?>
                            </h5>

                            <p class="badge bg-secondary">
                                <?php echo $room['room_type_title']; ?>
                            </p>

                            <p class="card-text">
                                <?php echo substr($room['detail'], 0, 80); ?>...
                            </p>

                            <p class="card-text">
                                <strong>Includes:</strong><br>
                                <?php echo $room['includes']; ?>
                            </p>

                            <h5 class="text-primary mb-3">
                                ₱<?php echo number_format($room['price'], 2); ?>
                            </h5>

                            <!-- PUSH BUTTON TO BOTTOM -->
                            <div class="mt-auto">
                                <a href="roomBookings.php?room_id=<?php echo $room['id']; ?>" 
                                class="btn btn-success w-100">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Book Room Now
                                </a>
                            </div>

                        </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No rooms available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <?php require '../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>