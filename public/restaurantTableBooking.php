<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch all tables from the database
$tables = [];
$query = "SELECT * FROM restaurant_tables"; 
$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tables[] = $row;
    }
} else {
    echo "Error fetching tables: " . mysqli_error($con);
}

// Get current date
$currentDate = date('Y-m-d');

// Fetch today's reservations
$reservations = [];
$reservationQuery = "SELECT * FROM table_reservations WHERE reservation_date = '$currentDate'";
$reservationResult = mysqli_query($con, $reservationQuery);

if ($reservationResult) {
    while ($row = mysqli_fetch_assoc($reservationResult)) {
        $reservations[$row['table_id']][$row['time_slot']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/tableBooking.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="booking-header text-center mb-5">
                    <h2 class="text-muted">Reserve Your Table</h2>
                    <p class="lead">Select a table and time to enjoy our exquisite dining experience at Seeds Restaurant</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Restaurant Layout -->
                <div class="restaurant-layout-container">
                    <div class="restaurant-layout-header mb-4">
                        <h3><i class="fas fa-map-marker-alt"></i> Restaurant Floor Plan</h3>
                        <p>Select a table to make a reservation</p>
                    </div>
                    
                    <div class="restaurant-layout">
                        <!-- Restaurant Legend -->
                        <div class="table-legend mb-3">
                            <div class="legend-item">
                                <div class="legend-color available"></div>
                                <span>Available</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color selected"></div>
                                <span>Selected</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color reserved"></div>
                                <span>Reserved</span>
                            </div>
                        </div>

                        <!-- Restaurant Layout Area -->
                        <div class="layout-area">
                            <!-- Restaurant Entrance -->
                            <div class="restaurant-entrance">
                                <i class="fas fa-door-open"></i>
                                <span>Entrance</span>
                            </div>

                            <!-- Tables -->
                            <div class="tables-container">
                                <?php foreach ($tables as $table): ?>
                                    <div class="table-item <?php echo $table['capacity']; ?>-seater" 
                                         data-table-id="<?php echo $table['table_id']; ?>" 
                                         data-capacity="<?php echo $table['capacity']; ?>"
                                         data-location="<?php echo $table['location']; ?>"
                                         style="top: <?php echo $table['position_y']; ?>px; left: <?php echo $table['position_x']; ?>px;">
                                        <div class="table-top">
                                            <span class="table-number"><?php echo $table['table_id']; ?></span>
                                        </div>
                                        <div class="table-info">
                                            <span><?php echo $table['capacity']; ?> seats</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Restaurant Features -->
                                <div class="restaurant-feature bar" style="top: 100px; left: 50px;">
                                    <i class="fas fa-glass-martini-alt"></i>
                                    <span>Bar</span>
                                </div>
                                
                                <div class="restaurant-feature kitchen" style="top: 200px; right: 50px;">
                                    <i class="fas fa-utensils"></i>
                                    <span>Kitchen</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Reservation Form -->
                <div class="reservation-form">
                    <h3><i class="far fa-calendar-check"></i> Make a Reservation</h3>
                    
                    <form id="tableReservationForm" action="process_table_reservation.php" method="POST">
                        <input type="hidden" id="selectedTableId" name="table_id" value="">
                        
                        <div class="mb-3">
                            <label for="reservationDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="reservationDate" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="timeSlot" class="form-label">Time</label>
                            <!-- <select class="form-select" id="timeSlot" name="time_slot" required>
                                <option value="">Select time</option>
                                <option value="07:00">07:00 AM</option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="21:00">9:00 PM</option>
                            </select> -->
                            <input type="time" class="form-control" id="timeSlot" name="time_slot" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="guestCount" class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" id="guestCount" name="guest_count" min="1" max="12" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="specialRequests" class="form-label">Special Requests (Optional)</label>
                            <textarea class="form-control" id="specialRequests" name="special_requests" rows="3"></textarea>
                        </div>
                        
                        <div class="selected-table-info mb-3 d-none">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Selected Table</h5>
                                    <p class="card-text">Table #<span id="displayTableId"></span></p>
                                    <p class="card-text">Capacity: <span id="displayTableCapacity"></span> persons</p>
                                    <p class="card-text">Location: <span id="displayTableLocation"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="reserveButton" disabled>Reserve Now</button>
                        </div>
                    </form>
                </div>

                <!-- Availability Calendar -->
                <div class="availability-calendar mt-4">
                    <h3><i class="far fa-clock"></i> Today's Availability</h3>
                    <div class="calendar-container">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="availabilityTable">
                                <tr>
                                    <td>11:00 AM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>12:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>1:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>2:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>6:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>7:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                                <tr>
                                    <td>8:00 PM</td>
                                    <td class="text-warning">Few Tables Left</td>
                                </tr>
                                <tr>
                                    <td>9:00 PM</td>
                                    <td class="text-success">Available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="../js/tableBooking.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>