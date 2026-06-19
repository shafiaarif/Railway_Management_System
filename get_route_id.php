<?php
include 'db.php';

if (isset($_GET['source']) && isset($_GET['destination'])) {
    $source = $_GET['source'];
    $destination = $_GET['destination'];

    // Convert station names to IDs
    $get_ids = $conn->prepare("SELECT station_id, s_name FROM station WHERE s_name IN (?, ?)");
    $get_ids->bind_param("ss", $source, $destination);
    $get_ids->execute();
    $result = $get_ids->get_result();

    $start_station_id = null;
    $end_station_id = null;
    while ($row = $result->fetch_assoc()) {
        if ($row['s_name'] == $source) {
            $start_station_id = $row['station_id'];
        } else if ($row['s_name'] == $destination) {
            $end_station_id = $row['station_id'];
        }
    }
    $get_ids->close();

    if ($start_station_id && $end_station_id) {
        // Now get the route_id
        $stmt = $conn->prepare("SELECT route_id FROM route WHERE start_station_id = ? AND end_station_id = ?");
        $stmt->bind_param("ii", $start_station_id, $end_station_id);
        $stmt->execute();
        $stmt->bind_result($route_id);

        if ($stmt->fetch()) {
            echo $route_id;
        } else {
            echo ""; // No matching route found
        }

        $stmt->close();
    } else {
        echo ""; // Station IDs not found
    }
}
?>
