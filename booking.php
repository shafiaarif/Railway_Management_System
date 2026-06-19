<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $class = $_POST['class'] ?? '';

    // Step 1: Get class_id from seat_class
    $classSql = "SELECT class_id FROM seat_class WHERE class_name = ?";
    $classStmt = $conn->prepare($classSql);
    $classStmt->bind_param("s", $class);
    $classStmt->execute();
    $classResult = $classStmt->get_result();
    if ($classResult->num_rows === 0) {
        echo json_encode([]);
        exit;
    }
    $class_id = $classResult->fetch_assoc()['class_id'];

    // Step 2: Get trains and fare
    $sql = "SELECT DISTINCT 
            t.train_no, 
            t.train_name, 
            t.arrival_time, 
            tf.fare, 
            s.class_id
        FROM train t
        JOIN seats s ON t.train_no = s.train_no
        JOIN class_details cd ON s.class_id = cd.class_id
        JOIN train_fare tf ON tf.train_no = t.train_no AND tf.class_id = s.class_id
        WHERE t.source = ? AND t.destination = ? AND s.class_id = ?";


    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssi", $source, $destination, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $trains = [];
    while ($row = $result->fetch_assoc()) {
        $trains[] = [
            'train_no' => $row['train_no'],
            'train_name' => $row['train_name'],
            'arrival_time' => $row['arrival_time'],
            'fare' => $row['fare'],
            'class_id' => $row['class_id']
        ];
    }

    echo json_encode($trains);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Train Booking</title>
   <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #0a0f14; /* dark background from second theme */
        color: #e0f7ff; /* light cyan text from second theme */
        padding: 30px 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    h2, h3 {
        color: #00e5ff; /* bright cyan from second theme */
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    label {
        display: inline-block;
        margin: 12px 12px 12px 0;
        font-weight: 600;
        color: #00c6ff; /* lighter cyan */
    }

    select, input[type="date"] {
        padding: 8px 12px;
        border-radius: 5px;
        border: 1.8px solid #00e5ff; /* cyan border */
        font-size: 1rem;
        min-width: 160px;
        background: #041018; /* dark card background */
        color: white;
        transition: border-color 0.3s ease;
    }

    select:focus, input[type="date"]:focus {
        outline: none;
        border-color: #0099cc;
        box-shadow: 0 0 8px rgba(0, 229, 255, 0.5);
        background: #082034;
        color: white;
    }

    button {
        background: linear-gradient(to right, #00e5ff, #00c6ff);
        color: black;
        border: none;
        border-radius: 6px;
        padding: 12px 28px;
        font-size: 1rem;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 229, 255, 0.3);
        transition: background 0.3s ease, box-shadow 0.3s ease;
        margin-top: 10px;
    }

    button:hover {
        background: linear-gradient(to right, #0099cc, #00a0e6);
        box-shadow: 0 6px 12px rgba(0, 150, 220, 0.5);
        color: black;
    }

    button:disabled, button[disabled] {
        background: #555;
        cursor: not-allowed;
        box-shadow: none;
        color: #ccc;
    }

    table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin-top: 25px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        border-radius: 10px;
        overflow: hidden;
        background: #041018;
        color: white;
    }

    thead {
        background: linear-gradient(to right, #00e5ff, #00c6ff);
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }

    th, td {
        padding: 14px 20px;
        text-align: center;
        border-bottom: 1px solid #082034;
    }

    tbody tr:hover {
        background-color: #005f73;
        cursor: pointer;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    input[type="radio"] {
        cursor: pointer;
        width: 18px;
        height: 18px;
        accent-color: #00e5ff; /* cyan radio */
    }

    #trainTableSection, #seatContainer {
        display: none;
        margin-top: 30px;
        background: #041018;
        padding: 20px 30px;
        border-radius: 12px;
        
        color: white;
    }

    #seatMessage {
        font-weight: 600;
        margin: 12px 0;
        min-height: 24px;
        color: #00e676; /* bright green */
    }

    #seatMessage.booked {
        color: #ff4444; /* bright red */
    }

    #payNowBtn, #payLaterBtn {
        margin-right: 12px;
        padding: 12px 28px;
        font-weight: 700;
        border-radius: 6px;
     
        background: linear-gradient(to right, #00e5ff, #00c6ff);
        color: black;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    #payNowBtn:hover {
        background: #0099cc;
        box-shadow: 0 8px 22px rgba(0, 150, 220, 0.6);
        color: black;
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
        label, select, input[type="date"], button {
            width: 100%;
            margin-bottom: 15px;
        }

        #payNowBtn, #payLaterBtn {
            width: 48%;
            margin-left: 0;
            margin-bottom: 10px;
        }

        #payNowBtn {
            margin-left: 10%;
        }
    }
</style>

</head>
<body>

<h2>Train Ticket Booking</h2>

<label for="source">Source:</label>
<select id="source">
    <option value="">Select Source</option>
    <?php
    $srcQuery = $conn->query("SELECT DISTINCT source FROM train");
    while ($srcRow = $srcQuery->fetch_assoc()) {
        echo "<option value='{$srcRow['source']}'>{$srcRow['source']}</option>";
    }
    ?>
</select>

<label for="destination">Destination:</label>
<select id="destination">
    <option value="">Select Destination</option>
    <?php
    $dstQuery = $conn->query("SELECT DISTINCT destination FROM train");
    while ($dstRow = $dstQuery->fetch_assoc()) {
        echo "<option value='{$dstRow['destination']}'>{$dstRow['destination']}</option>";
    }
    ?>
</select>

<label for="classType">Class:</label>
<select id="classType">
    <option value="">Select Class</option>
    <?php
    $classQuery = $conn->query("SELECT class_name FROM seat_class");
    while ($classRow = $classQuery->fetch_assoc()) {
        echo "<option value='{$classRow['class_name']}'>{$classRow['class_name']}</option>";
    }
    ?>
</select>
<br><br>
<button id="searchBtn">Search Trains</button>

<div id="trainTableSection" style="display:none;">
    <h3>Available Trains</h3>
    <table>
        <thead>
            <tr>
                <th>Train No</th>
                <th>Train Name</th>
                <th>Arrival</th>
                <th>Fare</th>
                <th>Select</th>
            </tr>
        </thead>
        <tbody id="trainTableBody"></tbody>
    </table>
</div>

<div id="seatContainer" style="display:none;">
    <h3>Select Seat</h3>
    <label for="seatDropdown">Seat No:</label>
    <select id="seatDropdown" onchange="checkSeatStatus()"></select>
    <div id="seatMessage"></div>

    <p><b>Booking Date:</b> <span id="bookingDate"></span></p>
    <label for="travelDate">Travel Date:</label>
    <input type="date" id="travelDate">
    <br><br>
    <button id="payNowBtn">Proceed to Payment</button>
    
</div>
<div style="text-align:center; margin-top: 40px;">
  <a href="passenger_dashboard.php" class="btn stylish-btn" aria-label="Back to Dashboard">Back to Dashboard</a>
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
  }
  .stylish-btn:hover,
  .stylish-btn:focus {
       background: linear-gradient(to right, #00e5ff, #00c6ff);
    color: black; /* Neon cyan for contrast */
    transform: translateY(-3px);
    outline: none;
  }
  .stylish-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(82, 92, 151, 0.4);
  }
</style>

<script>
    let selectedTrainNo = '';
    let selectedClassId = '';
    let selectedFare = 0;

    document.getElementById("searchBtn").addEventListener("click", function () {
        const src = document.getElementById("source").value;
        const dst = document.getElementById("destination").value;
        const cls = document.getElementById("classType").value;

        if (!src || !dst || !cls) {
            alert("Please select Source, Destination and Class.");
            return;
        }

        fetch('booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `source=${encodeURIComponent(src)}&destination=${encodeURIComponent(dst)}&class=${encodeURIComponent(cls)}`
        })
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("trainTableBody");
            tbody.innerHTML = "";
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5">No trains found.</td></tr>`;
            } else {
                data.forEach(train => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${train.train_no}</td>
                        <td>${train.train_name}</td>
                        <td>${train.arrival_time}</td>
                        <td>${train.fare}</td>
                        <td><button onclick="selectTrain('${train.train_no}', ${train.class_id}, ${train.fare})">Select</button></td>
                    `;
                    tbody.appendChild(row);
                });
            }
            document.getElementById("trainTableSection").style.display = "block";
            document.getElementById("seatContainer").style.display = "none";
        })
        .catch(err => {
            console.error("Error fetching trains:", err);
        });
    });

    function selectTrain(trainNo, classId, fare) {
        selectedTrainNo = trainNo;
        selectedClassId = classId;
        selectedFare = fare;
        const seatDropdown = document.getElementById("seatDropdown");
        const bookingDate = document.getElementById("bookingDate");
        const seatMessage = document.getElementById("seatMessage");

        fetch('gets_seat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `train_no=${encodeURIComponent(trainNo)}&class_id=${encodeURIComponent(classId)}`
        })
        .then(response => response.json())
        .then(seats => {
            seatDropdown.innerHTML = '';
            if (seats.length === 0) {
                seatDropdown.innerHTML = `<option disabled>No available seats</option>`;
                seatMessage.textContent = "No seats available.";
                seatMessage.classList.add("booked");
            } else {
                seats.forEach(seat => {
                    const option = document.createElement("option");
                    option.value = seat;
                    option.text = seat;
                    seatDropdown.appendChild(option);
                });
                seatMessage.textContent = "";
                seatMessage.classList.remove("booked");
            }

            document.getElementById("seatContainer").style.display = "block";
            const today = new Date().toISOString().split('T')[0];
            bookingDate.textContent = today;
        })
        .catch(err => {
            console.error("Error loading seats:", err);
        });
    }

    document.getElementById("payNowBtn").addEventListener("click", function () {
        const seatNo = document.getElementById("seatDropdown").value;
        const travelDate = document.getElementById("travelDate").value;

        if (!seatNo || !travelDate) {
            alert("Please select a seat and travel date before proceeding to payment.");
            return;
        }

        // Redirect to payment page
        window.location.href = `payment.php?train_no=${selectedTrainNo}&class_id=${selectedClassId}&seat_no=${seatNo}&fare=${selectedFare}&travel_date=${travelDate}`;
    });

  
</script>

</body>
</html>
