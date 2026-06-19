<?php
session_start();
require 'db.php'; // database connection

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    $_SESSION['error'] = "Invalid credentials. Please log in.";
    header("Location: login.php");
    exit();
}

// Get user info from session email
$email = $_SESSION['user_email'];
$userQuery = $conn->prepare("SELECT user_id, first_name, last_name FROM user WHERE e_mail = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    die("User not found.");
}

$_SESSION['user_id'] = $userData['user_id'];
$_SESSION['user_name'] = $userData['first_name'] . ' ' . $userData['last_name'];

// Fetch user's tickets with details
$ticketQuery = $conn->prepare("
    SELECT b.booking_id, b.fare, b.travel_date, s.seat_no, s.train_no, t.source, t.destination, t.train_name
    FROM booking b
    JOIN seats s ON b.seat_id = s.seat_id
    JOIN train t ON s.train_no = t.train_no
    WHERE b.user_id = ?
");
$ticketQuery->bind_param("i", $_SESSION['user_id']);
$ticketQuery->execute();
$ticketResult = $ticketQuery->get_result();

$tickets = [];
while ($row = $ticketResult->fetch_assoc()) {
    $tickets[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Tickets - Sageline Express</title>
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
    color: white;
    background-color: #0a0f14;
  }

  h1 {
    font-size: 2.5rem;
    background: linear-gradient(to right, #00e5ff, #0099cc);
    color: white;
    padding: 1rem 2rem;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 229, 255, 0.4);
    margin-bottom: 2rem;
  }

  .tickets-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
    width: 100%;
    max-width: 1100px;
  }

  .ticket {
    background: #041018; /* very dark navy/black */
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 6px 18px rgba(0, 229, 255, 0.4);
    border-left: 8px solid #00e5ff;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .ticket:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 229, 255, 0.6);
  }

  .ticket h3 {
    margin-top: 0;
    font-size: 1.5rem;
    color: white;
  }

  .ticket p {
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
    background: #00e5ff;
    color: black;
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
    background: #0099cc;
  }
</style>

</head>
<body>

  <h1>Your Tickets</h1>

  <div class="tickets-container">
    <?php foreach ($tickets as $index => $ticket): ?>
      <div class="ticket">
        <h3>Ticket #<?= $index + 1 ?></h3>
        <p><strong>Passenger:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <p><strong>Train:</strong> <?= htmlspecialchars($ticket['train_name']) ?> (<?= $ticket['train_no'] ?>)</p>
        <p><strong>From:</strong> <?= htmlspecialchars($ticket['source']) ?></p>
        <p><strong>To:</strong> <?= htmlspecialchars($ticket['destination']) ?></p>
        <p><strong>Travel Date:</strong> <?= htmlspecialchars($ticket['travel_date']) ?></p>
        <p><strong>Seat No:</strong> <?= htmlspecialchars($ticket['seat_no']) ?></p>
        <p><strong>Fare:</strong> $ <?= htmlspecialchars($ticket['fare']) ?></p>

        <form method="post" action="download_ticket.php">
          <input type="hidden" name="ticket_id" value="<?= $ticket['booking_id'] ?>">
          <button class="btn">Download Ticket</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
<a href="passenger_dashboard.php" class="btn secondary">← Dashboard</a>
</body>
</html>
