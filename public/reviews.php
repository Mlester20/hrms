<?php
session_start();
include '../components/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $user_id = $_SESSION['user_id'];
        $review_text = mysqli_real_escape_string($con, $_POST['review_text']);
        $rating = $_POST['rating'];
        
        $query = "INSERT INTO reviews (user_id, review_text, rating) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "isi", $user_id, $review_text, $rating);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Review submitted successfully!";
        } else {
            $_SESSION['error'] = "Error submitting review.";
        }
        header('Location: reviews.php');
        exit();
    }
}

$user_id = $_SESSION['user_id'];

$query = "SELECT r.*, u.name FROM reviews r 
          JOIN users u ON r.user_id = u.user_id 
          WHERE r.user_id = $user_id 
          ORDER BY r.created_at DESC";
// Add these lines to execute the query
$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - <?php include '../components/title.php'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/modal.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-5">
        <!-- Button to trigger modal -->
        <div class="text-end mb-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reviewModal">
                Write a Review
            </button>
        </div>

        <!-- Review Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" name="rating" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="review_text" class="form-label">Your Review</label>
                                <textarea class="form-control" name="review_text" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Reviews -->
        <div class="card">
            <h3 class="card-title mt-4 text-center text-muted">My Reviews</h3>
            <hr>
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td class="text-warning">
                                            <?php for ($i = 0; $i < $row['rating']; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                                        <td><?php echo date('F d, Y', strtotime($row['created_at'])); ?></td>
                                        <!-- <td>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                                                <a href="edit_review.php?id=<?php echo $row['review_id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="delete_review.php?id=<?php echo $row['review_id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this review?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td> -->
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/fetchClientNotifications.js"></script>
</body>
</html>
