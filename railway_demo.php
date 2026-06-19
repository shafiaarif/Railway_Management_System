<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sageline Express</title>
<style>
  :root {
    --primary-color: #0ff;
    --hover-bg: #111;
    --text-color: #fff;
    --link-bg: #1a1a1a;
    --overlay-bg: #000;
    --button-bg: #00e5ff;
    --button-hover: #00bcd4;
    --shadow-color: rgba(0, 0, 0, 0.5);
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Orbitron', sans-serif;
  }

  body {
    background: url('images/yellowtrain.jpg') no-repeat center center fixed;
    background-size: cover;
    color: var(--text-color);
    display: flex;
    min-height: 100vh;
    font-size: 16px;
  }

  .sidebar {
    background-color: var(--overlay-bg);
    width: 240px;
    padding: 30px;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100%;
    box-shadow: 2px 0 12px var(--shadow-color);
    border-radius: 0 12px 12px 0;
  }

  .sidebar h2 {
    font-size: 1.8em;
    margin-bottom: 35px;
    color: var(--primary-color);
    text-align: center;
    font-weight: 600;
    letter-spacing: 1px;
  }

  .nav-link {
    padding: 12px 16px;
    margin-bottom: 16px;
    text-decoration: none;
    color: var(--text-color);
    background-color: var(--link-bg);
    border-left: 4px solid transparent;
    border-radius: 8px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    font-size: 1.1em;
    font-weight: 500;
  }

  .nav-link:hover {
    background-color: var(--hover-bg);
    border-left: 4px solid var(--primary-color);
    color: var(--primary-color);
    transform: translateX(4px);
  }

  .nav-link i {
    margin-right: 12px;
    font-size: 1.4em;
  }

  .main-content {
    flex-grow: 1;
    padding: 50px;
    margin-left: 240px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-radius: 12px;
    margin-top: 30px;
    margin-bottom: 30px;
  }

  .title {
    font-size: 3em;
    font-weight: bold;
    color: var(--primary-color);
    padding: 20px 30px;
    text-align: center;
    background-color: #111;
    border-radius: 10px;
    margin-bottom: 40px;
    width: 100%;
    max-width: 800px;
    box-shadow: 0 0 12px #000;
  }

  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
</style>

</head>
<body>

  <div class="sidebar">
    <h2>Dashboard</h2>
    <a class="nav-link" href="register_passenger.php"><i class="fas fa-user-plus"></i>Register</a>
    <a class="nav-link" href="login.php"><i class="fas fa-user-plus"></i>Login</a>
    <a class="nav-link" href="train_timings.php"><i class="fas fa-book"></i>Train Timings</a>
    <!-- <a class="nav-link" href="login.php"><i class="fas fa-ticket-alt"></i>Tickets</a> -->
    <a class="nav-link" href="about_us.html"><i class="fas fa-info-circle"></i>About Us</a>
    <!-- <a class="nav-link" href="#"><i class="fas fa-history"></i>Travel History</a> -->

  </div>

  <div class="main-content">
    <div class="title">Sageline Express</div>
  </div>

</body>
</html>
