<?php
session_start();
include '../components/connection.php';

/* =========================
   ADD DESCRIPTION
========================= */
if (isset($_POST['save'])) {

    $db = new Database();
    $con = $db->getConnection();
    $stmt = null;

    try {
        $description = isset($_POST['description'])
            ? trim($_POST['description'])
            : '';

        if (empty($description)) {
            throw new Exception('Description cannot be empty.');
        }

        $stmt = $con->prepare(
            "INSERT INTO description (description_name) VALUES (?)"
        );

        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $con->error);
        }

        $stmt->bind_param('s', $description);

        if ($stmt->execute()) {
            echo "<script>
                alert('Description Added Successfully!');
                window.location.href='../admin/description.php';
            </script>";
        } else {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    } finally {
        if ($stmt) $stmt->close();
        $db->closeConnection();
    }
}


/* =========================
   UPDATE DESCRIPTION
========================= */
if (isset($_POST['updateDescription'])) {

    $db = new Database();
    $con = $db->getConnection();
    $stmt = null;

    try {
        $id = isset($_POST['description_id'])
            ? intval($_POST['description_id'])
            : 0;

        $description = isset($_POST['description_name'])
            ? trim($_POST['description_name'])
            : '';

        if ($id <= 0 || empty($description)) {
            throw new Exception('Invalid input.');
        }

        $stmt = $con->prepare(
            "UPDATE description
             SET description_name = ?
             WHERE description_id = ?"
        );

        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $con->error);
        }

        $stmt->bind_param('si', $description, $id);

        if ($stmt->execute()) {
            echo "<script>
                alert('Description Updated Successfully!');
                window.location.href='../admin/description.php';
            </script>";
        } else {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    } finally {
        if ($stmt) $stmt->close();
        $db->closeConnection();
    }
}
?>
