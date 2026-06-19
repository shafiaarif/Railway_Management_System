<?php
include 'db.php';

$success = '';
$error = '';

$stationsResult = $conn->query("SELECT station_id, s_name FROM station");
$stations = [];
while ($row = $stationsResult->fetch_assoc()) {
    $stations[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $distance = $_POST['distance'] !== "" ? (int)$_POST['distance'] : null;
    $start_station_id = $_POST['start_station_id'] !== "" ? (int)$_POST['start_station_id'] : null;
    $end_station_id = $_POST['end_station_id'] !== "" ? (int)$_POST['end_station_id'] : null;

    $stmt = $conn->prepare("INSERT INTO route (distance, start_station_id, end_station_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $distance, $start_station_id, $end_station_id);

    if ($stmt->execute()) {
        $success = "✅ Route added successfully!";
    } else {
        $error = "❌ Failed to add route: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Route</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Orbitron', sans-serif;
      height: 100%;
      background: linear-gradient(to top right, #0f2027, #203a43, #2c5364);
      color: #fff;
    }

    .split {
      display: flex;
      height: 100vh;
    }

    .left-panel {
      flex: 1;
      background: url('images/routs.jpg') no-repeat center center;
      background-size: cover;
    }

    .right-panel {
      flex: 1;
      background: rgba(0, 0, 0, 0.75);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .glass-form {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      padding: 40px;
      width: 80%;
      max-width: 450px;
   
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #00e5ff;
    }

    label {
      display: block;
      margin-top: 15px;
      font-size: 14px;
      letter-spacing: 1px;
    }

    input, select {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      margin-top: 5px;
      background-color: #111;
      color: #0ff;
      font-size: 14px;
      box-shadow: inset 0 0 8px #00e5ff;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      font-size: 16px;
      background: linear-gradient(to right, #00e5ff, #00c6ff);
      border: none;
      color: #000;
      font-weight: bold;
      border-radius: 25px;
      cursor: pointer;
      transition: 0.3s;
      text-transform: uppercase;
    }

    button:hover {
      opacity: 0.85;
    }

    .message {
      margin-top: 20px;
      text-align: center;
      font-weight: bold;
    }

    .success { color: #a5ffb4; }
    .error { color: #ffb3b3; }

    .back-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #00e5ff;
      color: #000;
      padding: 8px 15px;
      border-radius: 8px;
      font-size: 13px;
      text-decoration: none;
      font-weight: bold;
    
    }

    .back-btn:hover {
      background: #00c6ff;
    }

    @media (max-width: 768px) {
      .split {
        flex-direction: column;
      }
      .left-panel, .right-panel {
        flex: unset;
        height: 50vh;
      }
    }
  </style>
</head>
<body>

<div class="split">
  <div class="left-panel"></div>

  <div class="right-panel">
    <a href="admin_dashboard.php" class="back-btn">⬅ Dashboard</a>
    <div class="glass-form">
      <h2>Add New Route</h2>

      <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <label for="distance">Distance (km):</label>
        <input type="number" name="distance" id="distance" required placeholder="Enter distance">

        <label for="start_station_id">Start Station:</label>
        <select name="start_station_id" id="start_station_id" required>
          <option value="">-- Select Start Station --</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?= $station['station_id'] ?>">
              <?= $station['station_id'] ?> - <?= htmlspecialchars($station['s_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="end_station_id">End Station:</label>
        <select name="end_station_id" id="end_station_id" required>
          <option value="">-- Select End Station --</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?= $station['station_id'] ?>">
              <?= $station['station_id'] ?> - <?= htmlspecialchars($station['s_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit">Add Route</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
