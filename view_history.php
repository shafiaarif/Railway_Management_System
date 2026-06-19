<?php
// Database connection details - update these as per your config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "railway";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session and get logged-in user ID or fallback
session_start();
$userId = $_SESSION['user_id'] ?? 24; // Change 24 to your default or handle login

// Prepare the query
$sql = "
SELECT 
    th.history_id,
    th.fare,
    th.fare_paid,
    th.seat_class,
    th.travel_date, 
    t.train_name, 
    t.train_no, 
    t.source AS source_name, 
    t.destination AS destination_name 
FROM travel_history th
JOIN booking b ON th.user_id = b.user_id AND th.travel_date = b.travel_date
JOIN seats s ON b.seat_id = s.seat_id
JOIN train t ON s.train_no = t.train_no
WHERE th.user_id = ?
ORDER BY th.travel_date DESC
LIMIT 25;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Travel History</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  body {
    margin: 0;
    padding: 2rem;
    font-family: 'Poppins', sans-serif;
    background: url('images/yellowtrain.jpg') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    backdrop-filter: blur(3px);
    color: white; /* light cyan for text */
    background-color: #0a0f14; /* fallback dark background */
  }

  h1 {
    font-size: 2.5rem;
    background: linear-gradient(to right, #00e5ff, #0099cc); /* bright cyan gradient */
    color: white;
    padding: 1rem 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 229, 255, 0.4); /* cyan shadow */
    margin-bottom: 2rem;
  }

  .history-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
    width: 100%;
    max-width: 1100px;
  }

.history-card {
  background: #041018; /* very dark navy/black */
  border-radius: 15px;
  padding: 20px;
  box-shadow: 0 6px 18px rgba(0, 229, 255, 0.4);
  border-left: 8px solid #00e5ff;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}


  .history-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 229, 255, 0.6);
  }

  .history-card h3 {
    margin-top: 0;
    font-size: 1.5rem;
    color: white; /* medium cyan */
  }

.history-card p {
  margin: 6px 0;
  font-size: 1rem;
  background: linear-gradient(90deg, #00e5ff, #ffffff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-fill-color: transparent;
}

  .btn {
    margin-top: 40px;
    background: #00e5ff; /* bright cyan */
    color: #002f3f; /* dark text for contrast */
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    text-decoration: none;
    cursor: pointer;
    display: inline-block;
    transition: background 0.3s ease;
  }

  .btn:hover {
    background: #0099cc; /* darker cyan */
  }
</style>

</head>
<body>

<h1>Your Travel History</h1>

<?php if ($result->num_rows > 0): ?>
  <div class="history-container">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="history-card" tabindex="0" aria-label="Travel history card for train <?= htmlspecialchars($row['train_name']) ?>">
        <h3><?= htmlspecialchars($row['train_name']) ?> <small>(<?= htmlspecialchars($row['train_no']) ?>)</small></h3>
        <p><strong>From:</strong> <?= htmlspecialchars($row['source_name']) ?></p>
        <p><strong>To:</strong> <?= htmlspecialchars($row['destination_name']) ?></p>
        <p><strong>Seat Class:</strong> <?= htmlspecialchars($row['seat_class']) ?></p>
        <p><strong>Fare:</strong> $<?= htmlspecialchars($row['fare']) ?></p>
        <p><strong>Fare Paid:</strong> $<?= htmlspecialchars($row['fare_paid']) ?></p>
        <p><strong>Travel Date:</strong> <?= htmlspecialchars($row['travel_date']) ?></p>
        <p><strong>History ID:</strong> <?= htmlspecialchars($row['history_id']) ?></p>
      </div>
    <?php endwhile; ?>
  </div>
<?php else: ?>
  <p style="margin-top: 80px; font-size: 1.5rem; font-weight: 600; color: #ff3333; text-align: center;">
    No travel history found.
  </p>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
<div style="text-align:center; margin-top: 40px;">
  <a href="passenger_dashboard.php" class="btn" aria-label="Back to Dashboard">Back to Dashboard</a>
</div>
</body>
</html>
