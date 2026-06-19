<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_no = $_POST['train_no'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $seat_no = $_POST['seat_no'] ?? '';

    if (!$train_no || !$class_id || !$seat_no) {
        http_response_code(400);
        echo "Missing parameters";
        exit;
    }

    // Update seat availability to 0 (unavailable)
    $sql = "UPDATE seats SET availability = 0 WHERE train_no = ? AND class_id = ? AND seat_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $train_no, $class_id, $seat_no);
    
    if ($stmt->execute()) {
        echo "Seat availability updated";
    } else {
        http_response_code(500);
        echo "Failed to update seat availability";
    }
}
?>
