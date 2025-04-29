<?php
session_start();
include '../components/config.php';

// Check if user is not logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard - <?php include '../components/title.php'; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/customAdminHeader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <!-- header admin component -->
    <?php include '../components/staffsHeader.php'; ?>

    <div class="container mt-5">
        <?php
        $staff_id = $_SESSION['staff_id'];

        // Optional: Fetch staff name/position
        $staff_query = "SELECT name, position FROM staffs WHERE staff_id = ?";
        $stmt_staff = $con->prepare($staff_query);
        $stmt_staff->bind_param("i", $staff_id);
        $stmt_staff->execute();
        $staff_result = $stmt_staff->get_result();
        $staff_data = $staff_result->fetch_assoc();

        

        // Fetch shift data
        $query = "SELECT start_time, end_time, date_start, date_end 
                  FROM shifts 
                  WHERE staff_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h4 class='text-center text-muted'>Your Shift Schedule</h4>";
            echo "<div class='table-responsive mt-3'>";
            echo "<table class='table table-hover table-bordered'>";
            echo "<thead class='table-dark text-center'>
                    <tr>
                        <th>Date Start</th>
                        <th>Date End</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                  </thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                // Convert times to 12-hour format
                $start_time = (new DateTime($row['start_time']))->format('h:i A');
                $end_time = (new DateTime($row['end_time']))->format('h:i A');

                echo "<tr class='text-center'>
                        <td>" . htmlspecialchars($row['date_start']) . "</td>
                        <td>" . htmlspecialchars($row['date_end']) . "</td>
                        <td>" . htmlspecialchars($start_time) . "</td>
                        <td>" . htmlspecialchars($end_time) . "</td>
                      </tr>";
            }

            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<p class='text-center text-muted'>No shift schedule found.</p>";
        }
        ?>
    </div>

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>