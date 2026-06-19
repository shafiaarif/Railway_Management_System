<?php
include 'db.php';
$stations = [];
$station_query = $conn->query("SELECT s_name FROM station");
while ($row = $station_query->fetch_assoc()) {
    $stations[] = $row['s_name'];
}

$classes = [];
$class_query = $conn->query("SELECT class_id, class_name FROM seat_class");
while ($row = $class_query->fetch_assoc()) 
    $classes[] = $row;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arrival_time = $_POST['arrival_time'];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];
    $route_id = $_POST['route_id'];
    $source = $_POST['source'];
    $train_name = $_POST['train_name'];
    $class_id = $_POST['class_id'];
    $seat_prefix = strtoupper($_POST['seat_prefix']); // Ensure it's uppercase
    $fare = $_POST['fare'];



    $stmt = $conn->prepare("INSERT INTO train (arrival_time, destination, distance, route_id, source, train_name, class_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiissi", $arrival_time, $destination, $distance, $route_id, $source, $train_name, $class_id);


    if ($stmt->execute()) {
        $train_no = $conn->insert_id; // Get the newly inserted train's ID

        // Now insert 30 seats
        $insert_seat = $conn->prepare("INSERT INTO seats (seat_no, seat_availability, class_id, train_no) VALUES (?, 1, ?, ?)");
        
        for ($i = 1; $i <= 30; $i++) {
            $seat_no = $seat_prefix . $i;
            $insert_seat->bind_param("sii", $seat_no, $class_id, $train_no);
            $insert_seat->execute();
        }

        $insert_seat->close();

        $success = "Train and 30 seats added successfully!";
    } else {
        $error = "Error adding train: " . $stmt->error;
    }

    $stmt->close();
     $stmt = $conn->prepare("INSERT INTO train_fare(class_id, train_no, fare ) VALUES (?, ?, ?)");
    $stmt->bind_param("iii",  $class_id , $train_no, $fare);

    if ($stmt->execute()) {
    // Optional success message or logging
    } else {
        $error = "Error inserting fare: " . $stmt->error;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Train</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: url('images/train.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 40px;
        color: #eee;
        background-color: #000;
    }

    .container {
        background: rgba(20, 20, 20, 0.95);
        padding: 35px;
        border-radius: 16px;
        max-width: 600px;
        margin: auto;
    }

    h2 {
        text-align: center;
        color: #00e5ff;
        margin-bottom: 25px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-top: 15px;
        color: #ccc;
    }

    input, textarea, select.dropdown {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border: 1px solid #444;
        border-radius: 10px;
        background-color: #111;
        color: #fff;
    }

    input::placeholder,
    textarea::placeholder {
        color: #888;
    }

    button {
        margin-top: 25px;
        width: 100%;
        padding: 14px;
        font-size: 16px;
        background-color: #00e5ff; /* Cyan */
        color: #000; /* Black text */
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    button:hover {
        background-color: #00bcd4;
    }

    .message {
        margin-top: 15px;
        text-align: center;
        font-weight: bold;
    }

    .success {
        color: #00e676;
    }

    .error {
        color: #ff5252;
    }

    a.back-btn {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        color: #000;
        background-color: #00e5ff;
        padding: 10px 20px;
        border-radius: 8px;
    }

    a.back-btn:hover {
        background-color: #00bcd4;
    }

    select.dropdown:focus {
        outline: 2px solid #00e5ff;
        background-color: #1a1a1a;
    }
</style>

</head>
<body>

<a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

<div class="container">
    <h2>Add New Train</h2>

    <?php if (isset($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="train_name">Train Name:</label>
        <input type="text" name="train_name" id="train_name" required>

       <label for="source">Source:</label>
<select name="source" id="source" required class="dropdown">
    <option value="">-- Select Source Station --</option>
    <?php foreach ($stations as $s_name): ?>
        <option value="<?= htmlspecialchars($s_name) ?>"><?= htmlspecialchars($s_name) ?></option>
    <?php endforeach; ?>
</select>

<label for="destination">Destination:</label>
<select name="destination" id="destination" required class="dropdown">
    <option value="">-- Select Destination Station --</option>
    <?php foreach ($stations as $s_name): ?>
        <option value="<?= htmlspecialchars($s_name) ?>"><?= htmlspecialchars($s_name) ?></option>
    <?php endforeach; ?>
</select>

<label for="route_id">Route ID:</label>
<input type="number" name="route_id" id="route_id" readonly required>


        <label for="arrival_time">Arrival Time (HH:MM:SS):</label>
        <input type="time" name="arrival_time" id="arrival_time" required>

        <label for="distance">Distance (km):</label>
        <input type="number" name="distance" id="distance" required>


        <label for="class_id">Class:</label>
        <select name="class_id" id="class_id" required>
    <option value="">-- Select Class --</option>
    <?php foreach ($classes as $class): ?>
        <option value="<?= $class['class_id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
    <?php endforeach; ?>
</select>


        <label for="seat_prefix">Seat Prefix (e.g., A, B, C):</label>
        <input type="text" name="seat_prefix" id="seat_prefix" maxlength="1" required>

        <label for="fare">Fare (Dollars):</label>
<input type="number" name="fare" id="fare" required min="0" step="1">


        <button type="submit">Add Train</button>
    </form>
</div>

<script>
document.getElementById("source").addEventListener("change", fetchRouteId);
document.getElementById("destination").addEventListener("change", fetchRouteId);

function fetchRouteId() {
    const source = document.getElementById("source").value;
    const destination = document.getElementById("destination").value;

    if (source && destination) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `get_route_id.php?source=${encodeURIComponent(source)}&destination=${encodeURIComponent(destination)}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById("route_id").value = xhr.responseText || "";
            }
        };
        xhr.send();
    }
}
</script>

</body>
</html>
