<?php
include 'db.php';
session_start();
$today = date('Y-m-d');

// 1. Get parameters from URL first
$train_no = $_GET['train_no'] ?? null;
$class_id = $_GET['class_id'] ?? null;
$seat_no = $_GET['seat_no'] ?? null;
$travel_date = $_GET['travel_date'] ?? null;
$fare = $_GET['fare'] ?? 0;

if (!$train_no || !$class_id || !$seat_no || !$travel_date) {
    echo "Missing booking details.";
    exit;
}

// 2. Fetch seat_id
$seatQuery = "SELECT seat_id FROM seats WHERE seat_no = ? AND class_id = ? AND train_no = ?";
$stmtSeat = $conn->prepare($seatQuery);
$stmtSeat->bind_param("sii", $seat_no, $class_id, $train_no);
$stmtSeat->execute();
$stmtSeat->bind_result($seat_id);
$stmtSeat->fetch();
$stmtSeat->close();

if (!$seat_id) {
    die("Invalid seat selection. Seat ID not found.");
}

// 3. Check user session
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payType = $_POST['payType'] ?? '';
    $paymentStatus = "Paid";

    // Insert booking
    $bookingSQL = "INSERT INTO booking (booking_date, fare, seat_id, travel_date, user_id)
                   VALUES (?, ?, ?, ?, ?)";
    $stmtBooking = $conn->prepare($bookingSQL);
    $stmtBooking->bind_param("sdisi", $today, $fare, $seat_id, $travel_date, $user_id);

    if ($stmtBooking->execute()) {
        $booking_id = $stmtBooking->insert_id;

        // Insert payment
        $paymentSQL = "INSERT INTO payment (amount_paid, booking_id, payment_status, pay_date, pay_type, train_no)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmtPayment = $conn->prepare($paymentSQL);
        $stmtPayment->bind_param("disssi", $fare, $booking_id, $paymentStatus, $today, $payType, $train_no);

        if ($stmtPayment->execute()) {
            // Update seat availability
            $updateSeatSQL = "UPDATE seats SET seat_availability = 0 WHERE seat_id = ?";
            $stmtUpdateSeat = $conn->prepare($updateSeatSQL);
            $stmtUpdateSeat->bind_param("i", $seat_id);

            if ($stmtUpdateSeat->execute()) {
                $stmtUpdateSeat->close();

                // Insert into ticket table
                $ticketSQL = "INSERT INTO ticket (booking_id, class_id, seat_no, train_no)
                              VALUES (?, ?, ?, ?)";
                $stmtTicket = $conn->prepare($ticketSQL);
                $stmtTicket->bind_param("iisi", $booking_id, $class_id, $seat_no, $train_no);
                $stmtTicket->execute();
                $stmtTicket->close();

                // Get seat_class name
                $className = '';
                $classQuery = "SELECT class_name FROM seat_class WHERE class_id = ?";
                $stmtClass = $conn->prepare($classQuery);
                $stmtClass->bind_param("i", $class_id);
                $stmtClass->execute();
                $stmtClass->bind_result($className);
                $stmtClass->fetch();
                $stmtClass->close();

                // Insert into travel_history
                $historySQL = "INSERT INTO travel_history (fare, fare_paid, seat_class, travel_date, user_id)
                               VALUES (?, ?, ?, ?, ?)";
                $stmtHistory = $conn->prepare($historySQL);
                $stmtHistory->bind_param("ddssi", $fare, $fare, $className, $travel_date, $user_id);
                $stmtHistory->execute();
                $stmtHistory->close();

                echo "<script>
                        alert('Payment successful! Booking confirmed.');
                        window.location.href='view_recent_ticket.php';
                      </script>";
                exit;
            } else {
                echo "Payment succeeded, but failed to update seat availability: " . $stmtUpdateSeat->error;
                $stmtUpdateSeat->close();
            }
        } else {
            echo "Error in payment: " . $stmtPayment->error;
        }
        $stmtPayment->close();
    } else {
        echo "Error in booking: " . $stmtBooking->error;
    }
    $stmtBooking->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Payment Page</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
  }
  h2 {
    text-align: center;
    color: #333;
  }
  .amount {
    font-size: 1.5em;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    color: #222;
  }
  label {
    display: block;
    margin: 15px 0 6px;
    font-weight: 600;
  }
  select, input[type="text"], input[type="number"] {
    width: 100%;
    padding: 8px 10px;
    font-size: 1em;
    border: 1.5px solid #aaa;
    border-radius: 5px;
    box-sizing: border-box;
  }
  select:focus, input:focus {
    border-color: #0078d7;
    outline: none;
  }
  button {
    margin-top: 25px;
    width: 100%;
    padding: 12px;
    background-color: #0078d7;
    color: white;
    font-size: 1.1em;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #005fa3;
  }
</style>
</head>
<body>

<h2>Payment</h2>

<div class="amount">Amount to be Paid: <span>$<?php echo number_format($fare, 2); ?></span></div>

<form method="post" onsubmit="return validateAndSubmit();">
  <label for="payType">Pay Type:</label>
  <select name="payType" id="payType" onchange="togglePaymentFields()" required>
    <option value="">-- Select Payment Method --</option>
    <option value="creditCard">Credit Card</option>
    <option value="mobileBanking">Mobile Banking</option>
  </select>

  <div id="creditCardFields" style="display:none;">
    <label for="accountNumber">Account Number:</label>
    <input type="text" id="accountNumber" name="accountNumber" placeholder="Enter your card number" maxlength="16" />

    <label for="cvv">CVV:</label>
    <input type="number" id="cvv" name="cvv" placeholder="3-digit CVV" maxlength="3" />
  </div>

  <div id="mobileBankingFields" style="display:none;">
    <label for="phoneNumber">Phone Number:</label>
    <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Enter your phone number" maxlength="15" />
  </div>

  <button type="submit">Proceed to Pay</button>
</form>

<script>
  function togglePaymentFields() {
    const payType = document.getElementById('payType').value;
    document.getElementById('creditCardFields').style.display = payType === 'creditCard' ? 'block' : 'none';
    document.getElementById('mobileBankingFields').style.display = payType === 'mobileBanking' ? 'block' : 'none';
  }

  function validateAndSubmit() {
    const payType = document.getElementById('payType').value;
    if (!payType) {
      alert('Please select a payment method.');
      return false;
    }

    if (payType === 'creditCard') {
      const accNum = document.getElementById('accountNumber').value.trim();
      const cvv = document.getElementById('cvv').value.trim();
      if (!accNum || accNum.length < 13 || accNum.length > 16 || isNaN(accNum)) {
        alert('Please enter a valid card number (13-16 digits).');
        return false;
      }
      if (!cvv || cvv.length !== 3 || isNaN(cvv)) {
        alert('Please enter a valid 3-digit CVV.');
        return false;
      }
    } else if (payType === 'mobileBanking') {
      const phone = document.getElementById('phoneNumber').value.trim();
      if (!phone || phone.length == 11 || isNaN(phone)) {
        alert('Please enter a valid phone number.');
        return false;
      }
    }

    return true;
  }
</script>

</body>
</html>
