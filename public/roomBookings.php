<?php
session_start();
include '../components/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customBooking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/clientNavbar.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title text-center text-muted">Room Booking</h2>
                <p class="section-subtitle mb-4 mt-4 text-center">Find and book the perfect room for your stay</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="booking-search-container mb-5">
            <form id="bookingSearchForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="check_in_date" class="form-label">Check-in Date</label>
                        <input type="text" class="form-control date-picker" id="check_in_date" name="check_in_date" placeholder="Select date">
                    </div>
                    <div class="col-md-3">
                        <label for="check_out_date" class="form-label">Check-out Date</label>
                        <input type="text" class="form-control date-picker" id="check_out_date" name="check_out_date" placeholder="Select date">
                    </div>
                    <div class="col-md-3">
                        <label for="room_type" class="form-label">Room Type</label>
                        <select class="form-select" id="room_type" name="room_type">
                            <option value="">All Room Types</option>
                            <?php
                            // Fetch all room types from database
                            $query = "SELECT * FROM room_type";
                            $result = mysqli_query($con, $query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Search Available Rooms</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Available Rooms Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="text-center">Available Rooms</h3>
                <div class="booking-status-message"></div>
            </div>
        </div>

        <div class="row" id="available-rooms-container">
            <!-- Rooms will be loaded here dynamically -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading available rooms...</p>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center" id="bookingModalLabel">Book a Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        <input type="hidden" id="room_id" name="room_id">
                        <input type="hidden" id="modal_check_in_date" name="check_in_date">
                        <input type="hidden" id="modal_check_out_date" name="check_out_date">
                        <input type="hidden" id="total_price" name="total_price">
                        
                        <div class="booking-summary mb-4">
                            <h5 class="text-center">Booking Summary</h5>
                            <div class="room-details p-3">
                                <div class="room-title mb-2"></div>
                                <div class="room-type mb-2"></div>
                                <div class="booking-dates mb-2"></div>
                                <div class="nights-count mb-2"></div>
                                <div class="room-price mb-2"></div>
                                <div class="total-price-display fw-bold"></div>
                            </div>
                        </div>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="user-info-container">
                            <h6>Personal Information</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="special_requests" class="form-label">Special Requests</label>
                            <textarea class="form-control" id="special_requests" name="special_requests" rows="3"></textarea>
                        </div>
                        
                        <div class="payment-options mb-3">
                            <h6>Payment Method</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_later" value="pay_later" checked>
                                <label class="form-check-label" for="payment_later">
                                    Pay at Property
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_online" value="pay_online">
                                <label class="form-check-label" for="payment_online">
                                    Pay Online (Credit Card)
                                </label>
                            </div>
                        </div>
                        
                        <div id="payment_details" class="d-none">
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number">
                                </div>
                                <div class="col-md-4">
                                    <label for="card_expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                                </div>
                                <div class="col-md-4">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="card_cvv" name="card_cvv">
                                </div>
                                <div class="col-md-4">
                                    <label for="card_name" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a data-bs-toggle="collapse" href="#termsContent" role="button" aria-expanded="false" aria-controls="termsContent">terms and conditions</a>
                            </label>

                            <div class="collapse mt-3" id="termsContent">
                                <div class="card card-body" style="max-height: 300px; overflow-y: auto;">
                                    <h5>Terms and Conditions for Booking and Payment</h5>
                                    <p><strong>1. Booking Confirmation:</strong> All reservations are subject to availability. A booking is confirmed only upon valid payment and confirmation email/receipt.</p>
                                    <p><strong>2. Payment Policy:</strong> A 50% deposit is required to secure a reservation. Full payment is required before check-in or upon arrival. Payment methods accepted: [Credit/Debit Card, Bank Transfer, GCash, Cash].</p>
                                    <p><strong>3. Cancellation and Refund Policy:</strong> Cancel at least 48 hours in advance for a full refund. Less than 48 hours = forfeit deposit. No-shows = no refund. Refunds process in 7–14 business days.</p>
                                    <p><strong>4. Modification of Bookings:</strong> Changes allowed subject to availability. Price differences must be settled before new booking is confirmed.</p>
                                    <p><strong>5. Force Majeure:</strong> We are not liable for service disruption due to natural disasters, government orders, or force majeure.</p>
                                    <p><strong>6. Guest Responsibility:</strong> Guests must provide accurate details and follow hotel/restaurant rules. We reserve the right to refuse service for violations.</p>
                                    <p><strong>7. Agreement:</strong> By placing a reservation, you agree to these terms and conditions.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitBooking">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Confirmation Modal -->
    <div class="modal fade" id="bookingConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Confirmed!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h6>Thank you for your booking!</h6>
                    <p>Your booking has been successfully confirmed. A confirmation email will be sent to your email address.</p>
                    <div class="booking-reference mb-3">
                        <strong>Booking Reference:</strong> <span id="booking_reference"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../js/roomReservation.js"></script>
</body>
</html>