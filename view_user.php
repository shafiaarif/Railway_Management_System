<?php
include 'db.php';

// Fetch all users excluding admins and their passenger info
$query = "
SELECT u.user_id, u.first_name, u.last_name, u.e_mail, 
       p.gender, p.age, p.street, p.city, p.zip_code
FROM user u
LEFT JOIN passenger p ON u.user_id = p.user_id
WHERE u.user_id NOT IN (SELECT user_id FROM admin)
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View All Users</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('images/train.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 40px;
      color: #333;
    }

    .container {
      backdrop-filter: blur(10px);
      background: #000;
      padding: 35px;
      border-radius: 18px;
      max-width: 1200px;
      margin: auto;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: white;
      font-size: 2em;
    }

    a.back-btn {
      display: inline-block;
      padding: 12px 25px;
background-color: #00e5ff;


      color: black;
      text-decoration: none;
      border-radius: 8px;
      margin: 20px;
      transition: background-color 0.3s ease;
      font-weight: bold;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    a.back-btn:hover {
      background-color:00e5ff;
    }

    .styled-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 0.95em;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .styled-table thead tr {
      background-color:  #00e5ff;
      color: black;
      text-align: left;
    }

    .styled-table th,
    .styled-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
    }

    .styled-table tbody tr:hover {
      background-color: #f1f7ff;
    }

    @media (max-width: 768px) {
      .styled-table th, .styled-table td {
        padding: 10px 8px;
        font-size: 0.85em;
      }
    }
  </style>
</head>
<body>

<a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

<div class="container">
  <h2>All Users Information</h2>
  <div style="overflow-x:auto;">
    <table class="styled-table">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Gender</th>
          <th>Age</th>
          <th>Street</th>
          <th>City</th>
          <th>Zip Code</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['e_mail']) ?></td>
            <td><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['age'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['street'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['city'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['zip_code'] ?? 'N/A') ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
