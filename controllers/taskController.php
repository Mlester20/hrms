<?php
session_start();
include '../components/config.php';

// Add Task
if (isset($_POST['addTask'])) {
    $staff_id = $con->real_escape_string($_POST['staff_id']);
    $title = $con->real_escape_string($_POST['title']);
    $description = $con->real_escape_string($_POST['description']);
    $deadline = $con->real_escape_string($_POST['deadline']);
    $status = $con->real_escape_string($_POST['status']);

    $query = "INSERT INTO tasks (staff_id, title, description, deadline, status, created_at, updated_at) 
              VALUES ('$staff_id', '$title', '$description', '$deadline', '$status', NOW(), NOW())";

    if ($con->query($query)) {
        $_SESSION['success'] = "Task added successfully";
    } else {
        $_SESSION['error'] = "Error adding task: " . $con->error;
    }

    header('Location: ../admin/employeeTask.php');
    exit();
}

// Update Task
if (isset($_POST['updateTask'])) {
    $task_id = $con->real_escape_string($_POST['task_id']);
    $staff_id = $con->real_escape_string($_POST['staff_id']);
    $title = $con->real_escape_string($_POST['title']);
    $description = $con->real_escape_string($_POST['description']);
    $deadline = $con->real_escape_string($_POST['deadline']);
    $status = $con->real_escape_string($_POST['status']);

    $query = "UPDATE tasks 
              SET staff_id = '$staff_id', 
                  title = '$title', 
                  description = '$description', 
                  deadline = '$deadline', 
                  status = '$status', 
                  updated_at = NOW() 
              WHERE id = '$task_id'";

    if ($con->query($query)) {
        $_SESSION['success'] = "Task updated successfully";
    } else {
        $_SESSION['error'] = "Error updating task: " . $con->error;
    }

    header('Location: ../admin/employeeTask.php');
    exit();
}

// Delete Task
if (isset($_GET['deleteTask'])) {
    $task_id = $con->real_escape_string($_GET['deleteTask']);

    $query = "DELETE FROM tasks WHERE id = '$task_id'";

    if ($con->query($query)) {
        $_SESSION['success'] = "Task deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting task: " . $con->error;
    }

    header('Location: ../admin/employeeTask.php');
    exit();
}