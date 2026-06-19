<?php
// Database connection
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; 
$dbname = "railway"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unique sources and destinations from the train table
$sourceQuery = "SELECT DISTINCT source FROM train";
$destinationQuery = "SELECT DISTINCT destination FROM train";

$sourceResult = $conn->query($sourceQuery);
$destinationResult = $conn->query($destinationQuery);

// Handle search request
$from = isset($_POST['fromStation']) ? $_POST['fromStation'] : '';
$to = isset($_POST['toStation']) ? $_POST['toStation'] : '';
$trainData = '';

if ($from && $to && $from != $to) {
    $key = $from . '-' . $to;
    // Query to get train data based on the source and destination
    $trainQuery = "SELECT train_no,train_name, arrival_time,Distance FROM train WHERE source = '$from' AND destination = '$to'";
    $trainResult = $conn->query($trainQuery);
    
    if ($trainResult && $trainResult->num_rows > 0) {
        $trainData = [];
        while($row = $trainResult->fetch_assoc()) {
            $trainData[] = $row;
        }
    } else {
        $trainData = 'No trains found for the selected stations.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Train Timings</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #0a0a0a;
    color: #00e5ff;
    padding: 40px;
  }

  .search-bar {
    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: center;
    background-color: #121212;
    padding: 25px 20px;
    border-radius: 12px;
    flex-wrap: wrap;
  }

  select, button {
    padding: 12px 16px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    outline: none;
    transition: 0.3s;
  }

  select {
    width: 200px;
    background-color: #1a1a1a;
    color: white;
    border: 1px solid #00e5ff;
    box-shadow: 0 0 10px #00e5ff33;
  }

  button {
    background: linear-gradient(to right, #00e5ff, #00c6ff);
    color: #000;
    font-weight: bold;
    text-transform: uppercase;
    box-shadow: 0 0 10px #00e5ff55;
    cursor: pointer;
  }

  button:hover {
    opacity: 0.85;
  }

  #output {
    margin-top: 30px;
    background: #121212;
    padding: 25px;
    border-radius: 12px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }

  th, td {
    padding: 12px 10px;
    text-align: center;
    color: #fff;
    border: 1px solid #2a2a2a;
  }

  th {
    background-color: #00c6ff;
    color: #000;
    font-weight: bold;
    text-transform: uppercase;
  }

  td {
    background-color: #1e1e1e;
  }

  .btn.hover {
    background-color: #222;
    color: #00e5ff;
  }

  .btn.secondary:hover {
    background-color: #111;
  }
</style>

</head>
<body>

  <h2 style="text-align:center;">Train Timings</h2>

  <div class="search-bar">
    <form method="POST" action="train_timings.php">
      <select name="fromStation">
        <option value="">From</option>
        <?php
        if ($sourceResult->num_rows > 0) {
            while($row = $sourceResult->fetch_assoc()) {
                echo "<option value='" . $row['source'] . "'" . ($from == $row['source'] ? ' selected' : '') . ">" . $row['source'] . "</option>";
            }
        }
        ?>
      </select>

      <select name="toStation">
        <option value="">To</option>
        <?php
        if ($destinationResult->num_rows > 0) {
            while($row = $destinationResult->fetch_assoc()) {
                echo "<option value='" . $row['destination'] . "'" . ($to == $row['destination'] ? ' selected' : '') . ">" . $row['destination'] . "</option>";
            }
        }
        ?>
      </select>

      <button type="submit">Search</button>
    </form>
  </div>

  <div id="output">
    <?php if (!empty($trainData)): ?>
        <?php if (is_array($trainData)): ?>
            <h3>Available Trains from <?php echo $from; ?> to <?php echo $to; ?></h3>
            <table>
                <tr>
                    <th>Train No</th>
                    <th>Train Name</th>
                    <th>Arrival Time</th>
                    <th>Distance</th>
                </tr>
                <?php foreach ($trainData as $train): ?>
                    <tr>
                        <td><?php echo $train['train_no']; ?></td>
                        <td><?php echo $train['train_name']; ?></td>
                        <td><?php echo $train['arrival_time']; ?></td>
                        <td><?php echo $train['Distance']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><?php echo $trainData; ?></p>
        <?php endif; ?>
    <?php endif; ?>
  </div>
<div style="text-align:center; margin-top: 200px;">
  <a href="railway_demo.php" class="btn stylish-btn" aria-label="Back to Dashboard">Back to Dashboard</a>
</div>

<style>
  .stylish-btn {
      background: linear-gradient(to right, #00e5ff, #00c6ff);
    color: black; /* Neon cyan for contrast */
    padding: 14px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 12px;
    transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
    display: inline-block;
    text-decoration: none;
    text-align: center;
    margin: 20px auto;
    max-width: 100%;
    white-space: nowrap;
  }

  .stylish-btn:hover,
  .stylish-btn:focus {
   box-shadow: 0 0 10px #00e5ff55;
    transform: translateY(-3px);
    outline: none;
  }

  .stylish-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0, 255, 245, 0.2);
  }

  .btn-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
  }
</style>

</body>
</html>

<?php
// Close the database connection
$conn->close();

?>
