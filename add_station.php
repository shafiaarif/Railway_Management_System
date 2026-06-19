<?php
include 'db.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $s_name = $_POST['s_name'];
    $station_code = $_POST['station_code'];
    $city = $_POST['city'];

    $stmt = $conn->prepare("INSERT INTO station ( s_name, station_code, city) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $s_name, $station_code, $city);

    if ($stmt->execute()) {
        $success = "✅ Station added successfully!";
    } else {
        $error = "❌ Failed to add station: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Station</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
    }

    .split-container {
      display: flex;
      height: 100vh;
    }

    .left-side {
      flex: 1;
      background: url('images/station.jpg') no-repeat center center;
      background-size: cover;
    }

    .right-side {
      flex: 1;
      background-color: #111;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 50px;
    }

    .form-title {
      font-size: 28px;
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-size: 14px;
      color: #ccc;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: none;
      border-radius: 8px;
      background-color: #222;
      color: white;
      font-size: 14px;
    }

    input:focus {
      outline: 2px solid #26c6da;
    }

    button {
      margin-top: 25px;
      padding: 12px;
      background-color: #26c6da;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #26c6da;
    }

    .message {
      margin-top: 20px;
      font-weight: bold;
    }

    .success {
      color: #b9fbc0;
    }

    .error {
      color: #ffb3b3;
    }

    .back-btn {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #00e5ff;
      font-size: 14px;
    }

    .back-btn:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .split-container {
        flex-direction: column;
      }

      .left-side, .right-side {
        flex: unset;
        width: 100%;
        height: 50vh;
      }
    }
  </style>
</head>
<body>

<div class="split-container">
  <div class="left-side"></div>

  <div class="right-side">
    <div class="form-title">Add Station Info</div>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

      <label for="s_name">Station Name</label>
      <input type="text" name="s_name" id="s_name" required>

      <label for="station_code">Station Code</label>
      <input type="text" name="station_code" id="station_code" required>

      <label for="city">City</label>
      <input type="text" name="city" id="city" required>

      <button type="submit">Submit</button>
    </form>

    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
