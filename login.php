<?php
session_start();
include 'db.php'; // MySQL connection

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    $role     = $_POST['role'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Step 1: Get user_id and hashed password from user table
    $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE e_mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($userId, $hashedPass);
    $stmt->fetch();

    $isValid = false;

    if ($role === 'admin') {
        // For admin only, compare plain password directly
        $isValid = ($password === $hashedPass);
    } else {
        // Use secure hashed check for passengers
        $isValid = password_verify($password, $hashedPass);
    }

    if ($isValid) {
        if ($role === 'admin') {
            // Check admin table
            $adminCheck = $conn->prepare("SELECT user_id FROM admin WHERE user_id = ?");
            $adminCheck->bind_param("i", $userId);
            $adminCheck->execute();
            $adminCheck->store_result();

            if ($adminCheck->num_rows === 1) {
                $_SESSION['admin_id'] = $userId;
                $_SESSION['admin_email'] = $email;
                header('Location: admin_dashboard.php');
                exit();
            } else {
                $loginError = 'You are not registered as an admin.';
            }

        } elseif ($role === 'passenger') {
            // Check passenger table
            $passengerCheck = $conn->prepare("SELECT user_id FROM passenger WHERE user_id = ?");
            $passengerCheck->bind_param("i", $userId);
            $passengerCheck->execute();
            $passengerCheck->store_result();

            if ($passengerCheck->num_rows === 1) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                header('Location: passenger_dashboard.php');
                exit();
            } else {
                $loginError = 'You are not registered as a passenger.';
            }

        } else {
            $loginError = 'Invalid role selected.';
        }
    } else {
        $loginError = 'Incorrect password.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - Sageline Express</title>

<style>
  :root {
    --primary-dark: #1f1f1f;
    --accent: #00c6ff;
    --input-bg: #2c2c2c;
    --input-border: #444;
    --text-light: #f5f5f5;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
  }

  body {
    background: url('images/login2.jpg') no-repeat center center fixed;
    background-size: cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .login-container {
    background-color: rgba(0, 0, 0, 0.75); /* Dark transparent overlay */
    border: 1px solid #333;
    border-radius: 16px;
    padding: 40px;
    width: 90%;
    max-width: 420px;
    text-align: center;
  }

  h2 {
    color: var(--text-light);
    margin-bottom: 20px;
    font-size: 1.8rem;
  }

  .role-select {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 20px;
  }

  .role-select label {
    color: var(--text-light);
    font-size: 1em;
    cursor: pointer;
  }

  input[type="radio"] {
    accent-color: var(--accent);
    margin-right: 6px;
  }

  input[type="email"],
  input[type="password"] {
    width: 90%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid var(--input-border);
    background: var(--input-bg);
    color: #fff;
    font-size: 1em;
    transition: border-color 0.3s ease;
  }

  input[type="email"]:focus,
  input[type="password"]:focus {
    outline: none;
    border-color: var(--accent);
  }

  .btn {
    width: 65%;
    padding: 12px;
    background: var(--accent);
    color: black;
    border: none;
        border-radius: 25px;
    font-size: 1.2em;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }

  .btn:hover {
    background: #00c6ff;
    transform: translateY(-2px);
  }

  .error {
    color: #ff4d4d;
    background-color: rgba(255, 255, 255, 0.1);
    margin-top: 15px;
    padding: 10px;
    border-radius: 8px;
    font-weight: bold;
  }

  .back-link {
    display: block;
    margin-top: 20px;
    color: var(--accent);
    text-decoration: none;
    font-weight: bold;
  }

  .back-link:hover {
    text-decoration: underline;
  }

  @media (max-width: 500px) {
    .login-container {
      padding: 30px 20px;
    }

    h2 {
      font-size: 1.5em;
    }

    .btn {
      font-size: 0.9em;
    }
  }
</style>


</head>
<body>
  <div class="login-container">
    <h2>Login to Sageline Express</h2>

    <form method="POST" action="">
      <div class="role-select">
        <label>
          <input type="radio" name="role" value="passenger"
           <?= (!isset($_POST['role']) || $_POST['role'] === 'passenger') ? 'checked' : '' ?>>
          Passenger
        </label>
        <label>
          <input type="radio" name="role" value="admin"
           <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'checked' : '' ?>>
          Admin
        </label>
      </div>

      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button class="btn" type="submit">Login</button>
    </form>

    <?php if ($loginError): ?>
      <div class="error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>

    <a class="back-link" href="railway_demo.php">← Back to Home</a>
  </div>
</body>
</html>
