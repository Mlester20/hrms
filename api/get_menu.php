<?php

header('Content-Type: application/json');
include '../components/config.php';

// Get all menu items
$query = "SELECT * FROM restaurant_menu ORDER BY menu_id DESC LIMIT 3";
$result = mysqli_query($con, $query);

$menu_items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($item = mysqli_fetch_assoc($result)) {
        // Add each menu item to the array
        $menu_items[] = [
            'menu_id' => $item['menu_id'],
            'menu_name' => $item['menu_name'],
            'menu_description' => $item['menu_description'],
            'image' => $item['image'],
            'price' => $item['price']
        ];
    }
}

// Return JSON response
echo json_encode($menu_items);