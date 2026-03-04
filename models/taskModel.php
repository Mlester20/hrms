<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class taskModel extends BaseModel {
        protected $tasks = 'tasks';
        protected $staffs = 'staffs';

        public function getAllTasks(){
            try {
                $query = "SELECT t.*, s.name as staff_name 
                FROM {$this->tasks} t 
                LEFT JOIN {$this->staffs} s ON t.staff_id = s.staff_id 
                ORDER BY t.created_at DESC";
                $stmt = $this->con->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $tasks = [];
                while ($row = $result->fetch_assoc()) {
                    $tasks[] = $row;
                }
    
                $stmt->close();
                return $tasks;
            } catch (Exception $e) {
                throw new Exception("Error fetching tasks: " . $e->getMessage());
            }
        }

        public function addTask($staff_id, $title, $deadline, $status ,$description){
            try {
                $query = "INSERT INTO {$this->tasks} (staff_id, title, deadline, status, description, created_at) 
                          VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("issss", $staff_id, $title, $deadline, $status, $description);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                throw new Exception("Error adding task: " . $e->getMessage());
            }
        }

        public function updateTask($task_id, $staff_id, $title, $deadline, $status ,$description){
            try {
                $query = "UPDATE {$this->tasks} 
                          SET staff_id = ?, title = ?, deadline = ?, status = ?, description = ? 
                          WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("issssi", $staff_id, $title, $deadline, $status, $description, $task_id);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                throw new Exception("Error updating task: " . $e->getMessage());
            }
        }

        public function deleteTask($task_id){
            try {
                $query = "DELETE FROM {$this->tasks} WHERE id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $task_id);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                throw new Exception("Error deleting task: " . $e->getMessage());
            }
        }

    }

?>