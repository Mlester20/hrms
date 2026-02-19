<?php

require_once '../controllers/tableBookingController.php';
require_once '../middleware/authMiddleware.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - <?php include '../includes/title.php'; ?></title>
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
                    <p class="lead">Select a date, time, and table to enjoy our exquisite dining experience at Seeds Restaurant</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Restaurant Layout -->
                <div class="restaurant-layout-container">
                    <div class="restaurant-layout-header mb-4">
                        <h3><i class="fas fa-map-marker-alt"></i> Restaurant Floor Plan</h3>
                        <p>Select your date and time first, then choose an available table</p>
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

                            <!-- Tables Container - populated by JavaScript -->
                            <div class="tables-container">
                                <?php foreach ($tables as $table): ?>
                                    <div class="table-item <?php echo $table['capacity']; ?>-seater available" 
                                         data-table-id="<?php echo $table['table_id']; ?>" 
                                         data-capacity="<?php echo $table['capacity']; ?>"
                                         data-location="<?php echo $table['location']; ?>"
                                         style="top: <?php echo $table['position_y']; ?>px; left: <?php echo $table['position_x']; ?>px;">
                                        <div class="table-top">
                                            <span class="table-number"><?php echo isset($table['table_number']) ? $table['table_number'] : $table['table_id']; ?></span>
                                        </div>
                                        <div class="table-info">
                                            <span class="text-black mt-2"><?php echo $table['capacity']; ?> seats</span>
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
                    
                    <form id="tableReservationForm" method="POST">
                        <input type="hidden" id="selectedTableId" name="table_id" value="">
                        
                        <div class="mb-3">
                            <label for="reservationDate" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reservationDate" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" required>
                            <small class="text-muted">Select a date to view availability</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="timeSlot" class="form-label">Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="timeSlot" name="time_slot" required>
                            <small class="text-muted">Available hours: 7:00 AM - 9:00 PM</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="guestCount" class="form-label">Number of Guests <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="guestCount" name="guest_count" min="1" max="12" required>
                            <small class="text-muted">Maximum capacity varies by table</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="specialRequests" class="form-label">Special Requests (Optional)</label>
                            <textarea class="form-control" id="specialRequests" name="special_requests" rows="3" placeholder="Allergies, celebrations, accessibility needs, etc."></textarea>
                        </div>
                        
                        <div class="selected-table-info mb-3 d-none">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-check-circle text-success"></i> Selected Table</h5>
                                    <p class="card-text mb-1">Table #<strong><span id="displayTableId"></span></strong></p>
                                    <p class="card-text mb-1">Capacity: <strong><span id="displayTableCapacity"></span> persons</strong></p>
                                    <p class="card-text mb-0">Location: <strong><span id="displayTableLocation"></span></strong></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="reserveButton" disabled>
                                <i class="fas fa-calendar-check me-2"></i>Reserve Table
                            </button>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>How to book:</strong>
                            <ol class="mb-0 mt-2 ps-3">
                                <li>Select your preferred date and time</li>
                                <li>Click on an available table (green)</li>
                                <li>Fill in guest details and submit</li>
                            </ol>
                        </div>
                    </form>
                </div>

                <!-- Availability Calendar -->
                <div class="availability-calendar mt-4">
                    <h3><i class="far fa-clock"></i> Time Slot Availability</h3>
                    <p class="text-muted small">Showing availability for selected date</p>
                    <div class="calendar-container">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="availabilityTable">
                                <!-- Populated dynamically by JavaScript -->
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Loading availability...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-sync-alt"></i> Availability updates automatically
                    </small>
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