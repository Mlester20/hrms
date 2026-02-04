<?php

    class usersModel{

        public function getAllUsers($con){
            try{
                // Pagination settings
                $records_per_page = 5;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $records_per_page;

                // Get total number of users
                $total_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
                $total_result = mysqli_query($con, $total_query);
                $total_rows = mysqli_fetch_assoc($total_result)['total'];
                $total_pages = ceil($total_rows / $records_per_page);

                // Fetch users with pagination
                $query = "SELECT u.user_id, u.name, u.address, u.email, u.role, u.phone, 
                        (SELECT COUNT(*) FROM table_reservations r WHERE r.user_id = u.user_id) as reservation_count 
                        FROM users u WHERE u.role = 'user'
                        LIMIT $offset, $records_per_page";
                $result = mysqli_query($con, $query);

                // Check if query was successful
                if(!$result) {
                    die("Query failed: " . mysqli_error($con));
                }

                // Store users in an array
                $users = [];
                while($row = mysqli_fetch_assoc($result)) {
                    $users[] = $row;
                }
                return [
                    'users' => $users,
                    'total_pages' => $total_pages,
                    'current_page' => $page
                ];
            }catch( Exception $e ){
                throw new Exception("Error fetching users: " . $e->getMessage(), 500);
            }
        }
    }

?>