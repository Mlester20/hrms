<?php

    class BaseModel{
        protected $con;

        public function __construct($con){
            $this->con = $con;
        }
    }

    class shiftsModel extends BaseModel{
        protected $shifts = 'shifts';
        protected $staffs = 'staffs';

        public function getAllShifts(){
            try{
                // Fetch all shifts with staff details
                $query = "SELECT {$this->shifts}.*, {$this->staffs}.name, {$this->staffs}.position 
                        FROM {$this->shifts} 
                        INNER JOIN {$this->staffs} ON {$this->shifts}.staff_id = {$this->staffs}.staff_id";
                $result = $this->con->query($query);

                $staffQuery = "SELECT staff_id, name FROM {$this->staffs}";
                $staffResult = $this->con->query($staffQuery);
                return [
                    "shifts" => $result,
                    "staffs" => $staffResult
                ];
            }catch(Exception $e){
                throw new Exception("Error fetching shifts: " . $e->getMessage());
            }
        }

        public function addShifts($staff_id, $start_time, $end_time, $date_start, $date_end, $status){
            try{
                $query = "INSERT INTO {$this->shifts} (staff_id, start_time, end_time, date_start, date_end, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("isssss", $staff_id, $start_time, $end_time, $date_start, $date_end, $status);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error adding shift: " . $e->getMessage());
            }
        }

        public function updateShift($shift_id, $staff_id, $start_time, $end_time, $date_start, $date_end, $status){
            try{
                $query = "UPDATE {$this->shifts} SET staff_id = ?, start_time = ?, end_time = ?, date_start = ?, date_end = ?, status = ? WHERE shift_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("isssssi", $staff_id, $start_time, $end_time, $date_start, $date_end, $status, $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error updating shift: " . $e->getMessage());
            }
        }

        public function deleteShift($shift_id){
            try{
                $query = "DELETE FROM {$this->shifts} WHERE shift_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error deleting shift: " . $e->getMessage());
            }
        }

        public function markShiftAsDone($shift_id){
            try{
                $query = "UPDATE {$this->shifts} SET status = 'done' WHERE shift_id = ?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("i", $shift_id);
                return $stmt->execute();
            }catch(Exception $e){
                throw new Exception("Error marking shift as done: " . $e->getMessage());
            }
        }

    }

?>