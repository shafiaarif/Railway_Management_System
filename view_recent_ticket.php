<?php
session_start();
require 'db.php'; // your database connection

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    $_SESSION['error'] = "Please log in first.";
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

// Fetch only the most recent ticket
$ticketQuery = $conn->prepare("
    SELECT b.booking_id, b.fare, b.travel_date, s.seat_no, s.train_no, t.source, t.destination, t.train_name
    FROM booking b
    JOIN seats s ON b.seat_id = s.seat_id
    JOIN train t ON s.train_no = t.train_no
    WHERE b.user_id = ?
    ORDER BY b.booking_id DESC
    LIMIT 1
");
$ticketQuery->bind_param("i", $_SESSION['user_id']);
$ticketQuery->execute();
$ticketResult = $ticketQuery->get_result();

$ticket = $ticketResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Ticket - Sageline Express</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('images/yellowtrain.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: #333;
    }
    h1 {
      color: white;
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      font-size: 2.5em;
    }
    .ticket {
      background-color: rgba(255, 255, 255, 0.95);
      border-left: 5px solid #5a2e5a;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      padding: 20px;
      border-radius: 10px;
      max-width: 500px;
      width: 100%;
      transition: transform 0.2s ease;
    }
    .ticket:hover {
      transform: scale(1.02);
    }
    .ticket h3 {
      margin-top: 0;
      color: #5a2e5a;
    }
    .ticket p {
      margin: 6px 0;
      color: #333;
    }
    .btn {
      background-color: #6d6e8d;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      font-size: 1em;
      cursor: pointer;
      margin-top: 20px;
    }
    .btn:hover {
      background-color: #4a4768;
    }
    .btn.secondary {
      background-color: #999;
      margin-top: 40px;
      text-decoration: none;
      display: inline-block;
      padding: 10px 25px;
      border-radius: 6px;
      color: white;
    }
    .btn.secondary:hover {
      background-color: #666;
    }
  </style>
</head>
<body>

  <h1>Your Most Recent Ticket</h1>

  <?php if ($ticket): ?>
    <div class="ticket">
      <h3>Ticket #<?= htmlspecialchars($ticket['booking_id']) ?></h3>
      <p><strong>Passenger:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
      <p><strong>Train:</strong> <?= htmlspecialchars($ticket['train_name']) ?> (<?= htmlspecialchars($ticket['train_no']) ?>)</p>
      <p><strong>From:</strong> <?= htmlspecialchars($ticket['source']) ?></p>
      <p><strong>To:</strong> <?= htmlspecialchars($ticket['destination']) ?></p>
      <p><strong>Travel Date:</strong> <?= htmlspecialchars($ticket['travel_date']) ?></p>
      <p><strong>Seat No:</strong> <?= htmlspecialchars($ticket['seat_no']) ?></p>
      <p><strong>Fare:</strong> Rs. <?= htmlspecialchars($ticket['fare']) ?></p>

      <form method="post" action="download_ticket.php">
        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['booking_id']) ?>">
        <button class="btn">Download Ticket</button>
      </form>
    </div>
  <?php else: ?>
    <p style="color: white; font-size: 1.3em;">No tickets found. Please book your ticket first.</p>
  <?php endif; ?>

  <a href="passenger_dashboard.php" class="btn secondary">← Dashboard</a>

</body>
</html>
