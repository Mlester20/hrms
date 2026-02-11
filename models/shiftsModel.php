<?php

    class shiftsModel{
        public function getAllShifts($con){
            try{
                // Fetch all shifts with staff details
                $query = "SELECT shifts.*, staffs.name, staffs.position 
                        FROM shifts 
                        INNER JOIN staffs ON shifts.staff_id = staffs.staff_id";
                $result = $con->query($query);

                $staffQuery = "SELECT staff_id, name FROM staffs";
                $staffResult = $con->query($staffQuery);
                return [
                    "shifts" => $result,
                    "staffs" => $staffResult
                ];
            }catch(Exception $e){
                throw new Exception("Error fetching shifts: " . $e->getMessage());
            }
        }

        public function addShifts($con, $staff_id, $start_time, $end_time, $date_start, $date_end, $status){
            try{
                $query = "INSERT INTO shifts (staff_id, start_time, end_time, date_start, date_end, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $con->prepare($query);
                $stmt->bind_param("isssss", $staff_id, $start_time, $end_time, $date_start, $date_end, $status);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error adding shift: " . $e->getMessage());
            }
        }

        public function updateShift($con, $shift_id, $staff_id, $start_time, $end_time, $date_start, $date_end, $status){
            try{
                $query = "UPDATE shifts SET staff_id = ?, start_time = ?, end_time = ?, date_start = ?, date_end = ?, status = ? WHERE shift_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("isssssi", $staff_id, $start_time, $end_time, $date_start, $date_end, $status, $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error updating shift: " . $e->getMessage());
            }
        }

        public function deleteShift($con, $shift_id){
            try{
                $query = "DELETE FROM shifts WHERE shift_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error deleting shift: " . $e->getMessage());
            }
        }

        public function markShiftAsDone($con, $shift_id){
            try{
                $query = "UPDATE shifts SET status = 'done' WHERE shift_id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error marking shift as done: " . $e->getMessage());
            }
        }

    }

?>