<?php
/**
 * components/checkAvailability.php
 *
 * Requires the following variables from the controller:
 *  - $today, $tomorrow
 *  - $f_check_in, $f_check_out, $f_room_type
 *  - $roomTypes        array of room type rows
 *  - $availableRooms   array of available room rows (populated after search)
 *  - $searchPerformed  bool
 *  - $availError       string (validation error message)
 */

// Night count helper (used in results)
$nights = 0;
if (!empty($f_check_in) && !empty($f_check_out)) {
    $nights = (int)((strtotime($f_check_out) - strtotime($f_check_in)) / 86400);
}
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     CHECK AVAILABILITY SECTION
════════════════════════════════════════════════════════════════════════════ -->
<section class="availability-section py-5" id="checkAvailability">
    <div class="container">

        <!-- Section heading -->
        <div class="text-center mb-4">
            <h5 class="text-uppercase text-muted letter-spacing-2">Plan Your Stay</h5>
            <h2 class="fw-bold">Check Room Availability</h2>
            <p class="text-muted">Select your dates and find the perfect room for you.</p>
        </div>

        <!-- ── Search Card ────────────────────────────────────────────────── -->
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-body p-4">
                <form method="GET" action="" id="availabilityForm" novalidate>
                    <div class="row g-3 align-items-end">

                        <!-- Check-in -->
                        <div class="col-12 col-md-3">
                            <label for="check_in" class="form-label fw-semibold">
                                <i class="fas fa-calendar-check text-primary me-1"></i> Check-In
                            </label>
                            <input
                                type="date"
                                id="check_in"
                                name="check_in"
                                class="form-control form-control-lg"
                                min="<?= htmlspecialchars($today) ?>"
                                value="<?= htmlspecialchars($f_check_in) ?>"
                                required
                            >
                        </div>

                        <!-- Check-out -->
                        <div class="col-12 col-md-3">
                            <label for="check_out" class="form-label fw-semibold">
                                <i class="fas fa-calendar-times text-danger me-1"></i> Check-Out
                            </label>
                            <input
                                type="date"
                                id="check_out"
                                name="check_out"
                                class="form-control form-control-lg"
                                min="<?= htmlspecialchars($tomorrow) ?>"
                                value="<?= htmlspecialchars($f_check_out) ?>"
                                required
                            >
                        </div>

                        <!-- Room type filter -->
                        <div class="col-12 col-md-3">
                            <label for="room_type" class="form-label fw-semibold">
                                <i class="fas fa-bed text-secondary me-1"></i> Room Type
                            </label>
                            <select id="room_type" name="room_type" class="form-select form-select-lg">
                                <option value="">All Types</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option
                                        value="<?= htmlspecialchars($type['room_type_id']) ?>"
                                        <?= $f_room_type == $type['room_type_id'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars(ucfirst($type['room_type_id'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 col-md-3 d-grid">
                            <button
                                type="submit"
                                name="check_availability"
                                class="btn btn-primary btn-lg rounded-3"
                            >
                                <i class="fas fa-search me-2"></i> Search Rooms
                            </button>
                        </div>

                    </div><!-- /.row -->
                </form>
            </div>
        </div>

        <!-- ── Validation Error ───────────────────────────────────────────── -->
        <?php if (!empty($availError)): ?>
            <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div><?= htmlspecialchars($availError) ?></div>
            </div>
        <?php endif; ?>

        <!-- ── Results ───────────────────────────────────────────────────── -->
        <?php if ($searchPerformed): ?>

            <!-- Results header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0 fw-semibold">
                    <?php if (!empty($availableRooms)): ?>
                        <span class="badge bg-success me-2"><?= count($availableRooms) ?></span>
                        Room<?= count($availableRooms) !== 1 ? 's' : '' ?> Available
                    <?php else: ?>
                        <span class="text-danger">No Rooms Available</span>
                    <?php endif; ?>
                </h4>
                <span class="text-muted small">
                    <i class="fas fa-moon me-1"></i>
                    <?= $nights ?> night<?= $nights !== 1 ? 's' : '' ?> &nbsp;|&nbsp;
                    <i class="fas fa-calendar me-1"></i>
                    <?= date('M d, Y', strtotime($f_check_in)) ?>
                    &rarr; <?= date('M d, Y', strtotime($f_check_out)) ?>
                </span>
            </div>

            <?php if (!empty($availableRooms)): ?>

                <div class="row g-4">
                    <?php foreach ($availableRooms as $room): ?>
                        <?php
                            $room_id      = $room['id']           ?? '';
                            $room_title   = $room['title']         ?? 'Untitled Room';
                            $room_type    = $room['room_type_id']  ?? 'Standard';
                            $room_image   = $room['image']         ?? '';
                            $room_price   = $room['price']         ?? 0;
                            $room_inc     = $room['includes']      ?? '';
                            $total        = $room_price * $nights;
                            $includes     = !empty($room_inc) ? explode(',', $room_inc) : [];
                        ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 room-card overflow-hidden">

                                <!-- Room Image -->
                                <div class="room-img-wrapper position-relative overflow-hidden" style="height:220px;">
                                    <?php if (!empty($room_image)): ?>
                                        <img
                                            src="../uploads/<?= htmlspecialchars($room_image) ?>"
                                            alt="<?= htmlspecialchars($room_title) ?>"
                                            class="w-100 h-100"
                                            style="object-fit:cover; transition: transform .4s ease;"
                                            onerror="this.src='../images/loginbg.jpg'"
                                        >
                                    <?php else: ?>
                                        <img
                                            src="../images/loginbg.jpg"
                                            alt="No image available"
                                            class="w-100 h-100"
                                            style="object-fit:cover;"
                                        >
                                    <?php endif; ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-primary rounded-pill fs-6 px-3 py-2">
                                        ₱<?= number_format($room_price, 2) ?>/night
                                    </span>
                                </div>

                                <div class="card-body d-flex flex-column gap-2">

                                    <!-- Title & type -->
                                    <div>
                                        <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($room_title) ?></h5>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-tag me-1 text-muted"></i>
                                            <?= htmlspecialchars(ucfirst($room_type)) ?>
                                        </span>
                                    </div>

                                    <!-- Includes -->
                                    <?php if (!empty($includes)): ?>
                                        <ul class="list-unstyled mb-0 d-flex flex-wrap gap-2 mt-1">
                                            <?php foreach ($includes as $item): ?>
                                                <li class="text-muted small">
                                                    <i class="fas fa-check-circle text-success me-1"></i>
                                                    <?= htmlspecialchars(trim($item)) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <!-- Price summary -->
                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0 text-muted small">Total for <?= $nights ?> night<?= $nights !== 1 ? 's' : '' ?></p>
                                                <p class="mb-0 fw-bold fs-5 text-primary">₱<?= number_format($total, 2) ?></p>
                                            </div>
                                            <a
                                                href="roomBookings.php?room_id=<?= urlencode($room_id) ?>&check_in=<?= urlencode($f_check_in) ?>&check_out=<?= urlencode($f_check_out) ?>"
                                                class="btn btn-primary rounded-pill px-4"
                                            >
                                                Book Now <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>

                                </div><!-- /.card-body -->
                            </div><!-- /.card -->
                        </div>
                    <?php endforeach; ?>
                </div><!-- /.row -->

            <?php else: ?>

                <!-- Empty state -->
                <div class="text-center py-5">
                    <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No rooms available for the selected dates.</h4>
                    <p class="text-muted mb-4">Try adjusting your dates or removing the room type filter.</p>
                    <a href="#checkAvailability" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-redo me-2"></i> Try Different Dates
                    </a>
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div><!-- /.container -->
</section>
