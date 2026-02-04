<?php
require_once '../components/connection.php';
require_once '../models/taskModel.php';

$taskModel = new taskModel();

    // Handle Add Task
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addTask'])) {
        try {
            $staff_id = $_POST['staff_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $deadline = $_POST['deadline'];
            $status = $_POST['status'];

            $taskModel->addTask($con, $staff_id, $title, $deadline, $status, $description);
            $_SESSION['success'] = 'Task added successfully!';
            header('Location: ../admin/employeeTask.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error adding task: ' . $e->getMessage();
            header('Location: ../admin/employeeTask.php');
            exit();
        }
    }

    // Handle Update Task
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateTask'])) {
        try {
            $task_id = $_POST['task_id'];
            $staff_id = $_POST['staff_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $deadline = $_POST['deadline'];
            $status = $_POST['status'];

            $taskModel->updateTask($con, $task_id, $staff_id, $title, $deadline, $status, $description);
            $_SESSION['success'] = 'Task updated successfully!';
            header('Location: ../admin/employeeTask.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error updating task: ' . $e->getMessage();
            header('Location: ../admin/employeeTask.php');
            exit();
        }
    }

    // Handle Delete Task
    if (isset($_GET['deleteTask'])) {
        try {
            $task_id = $_GET['deleteTask'];
            $taskModel->deleteTask($con, $task_id);
            $_SESSION['success'] = 'Task deleted successfully!';
            header('Location: ../admin/employeeTask.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting task: ' . $e->getMessage();
            header('Location: ../admin/employeeTask.php');
            exit();
        }
    }



// Get all tasks and staffs for the page
$tasks = $taskModel->getAllTasks($con);

// Get all staff for dropdown
$query = "SELECT staff_id, name FROM staffs ORDER BY name ASC";
$staff_result = $con->query($query);

?>