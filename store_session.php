<?php
session_start();

// Debug: log received POST values
error_log(print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['train_no', 'class_id', 'seat_no', 'travel_date'];

    // Check all values are present
    foreach ($required as $key) {
        if (!isset($_POST[$key]) || empty($_POST[$key])) {
            echo "Missing: $key";
            exit;
        }
    }

    // Store in session
    $_SESSION['train_no'] = $_POST['train_no'];
    $_SESSION['class_id'] = $_POST['class_id'];
    $_SESSION['seat_id'] = $_POST['seat_no'];
    $_SESSION['travel_date'] = $_POST['travel_date'];

    // You may set fare later dynamically, or add it here if available
    // $_SESSION['fare'] = calculateFare(...);

    echo 'success';
} else {
    echo 'Invalid request';
}
?>
