<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_no = $_POST['train_no'] ?? '';
    $class_id = $_POST['class_id'] ?? '';

    $stmt = $conn->prepare("SELECT seat_no FROM seats WHERE train_no = ? AND class_id = ? AND seat_availability = 1");
    $stmt->bind_param("si", $train_no, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $seats = [];
    while ($row = $result->fetch_assoc()) {
        $seats[] = $row['seat_no'];
    }

    echo json_encode($seats);
}
?>

