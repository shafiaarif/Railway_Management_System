<!-- <?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_no = $_POST['train_no'];
    $class_id = $_POST['class_id'];
    $seat_no = $_POST['seat_no'];
    $travel_date = $_POST['travel_date'];
    $booking_date = date('Y-m-d');
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    // Insert into booking table
    $insert = $conn->prepare("INSERT INTO booking (train_no, class_id, seat_no, travel_date, booking_date, user_id, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $insert->bind_param("siissi", $train_no, $class_id, $seat_no, $travel_date, $booking_date, $user_id);

    if ($insert->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $conn->error]);
    }
}
?>
<script>
function payLaterClicked() {
    const seatNo = document.getElementById("seatDropdown").value;
    const travelDate = document.getElementById("travelDate").value;

    if (!seatNo || !travelDate) {
        alert("Please select a seat and enter a travel date.");
        return;
    }

    const formData = new URLSearchParams();
    formData.append('train_no', selectedTrainNo);
    formData.append('class_id', selectedClassId);
    formData.append('seat_no', seatNo);
    formData.append('travel_date', travelDate);

    fetch('insert_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById("bookingNotice").style.display = "block";
        } else {
            alert("Booking failed: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script> -->



<?php
include('db.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_no = $_POST['train_no'] ?? '';
    $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
    $seat_no = $_POST['seat_no'] ?? '';
    $travel_date = $_POST['travel_date'] ?? '';
    $booking_date = date('Y-m-d');
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    if (!$train_no || !$class_id || !$seat_no || !$travel_date) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $insert = $conn->prepare("INSERT INTO booking (train_no, class_id, seat_no, travel_date, booking_date, user_id, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    if (!$insert) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $insert->bind_param("sisssi", $train_no, $class_id, $seat_no, $travel_date, $booking_date, $user_id);

    if ($insert->execute()) {
        $booking_id = $insert->insert_id;
        echo json_encode(['status' => 'success', 'booking_id' => $booking_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking failed: ' . $insert->error]);
    }

    $insert->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
