<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "railway");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // Get and sanitize input
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $age = (int) $_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $street = $conn->real_escape_string($_POST['street']);
    $city = $conn->real_escape_string($_POST['city']);
    $zipcode = $conn->real_escape_string($_POST['zipcode']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // Step 1: Insert into users table
    $check_query = "SELECT user_id FROM user WHERE E_mail = '$email'";
    $check_result = $conn->query($check_query);
    
if ($check_result->num_rows > 0) {
    echo "<script>alert('This email is already registered.'); window.history.back();</script>";
    exit();
}

    $sql_user = "INSERT INTO user (first_name, last_name, E_mail, password) VALUES ('$first_name', '$last_name', '$email', '$password')";
    if ($conn->query($sql_user) === TRUE) {
        $user_id = $conn->insert_id;

        // Step 2: Insert into passengers table
        $sql_passenger = "INSERT INTO passenger (user_id, age, gender, street, city, zip_code)
                          VALUES ($user_id, $age, '$gender', '$street', '$city', '$zipcode')";

        // Step 3: Insert into phone_numbers table
        $sql_phone = "INSERT INTO phonenumber (user_id, phone_no) VALUES ($user_id, '$phone')";

        if ($conn->query($sql_passenger) === TRUE && $conn->query($sql_phone) === TRUE) {
            echo "<script>
           if (confirm('Registration successful! Click OK to go to the Dashboard.')) {
           window.location.href = 'railway_demo.php';
           } else {
          window.location.href = 'railway_demo.php';
           }
          </script>";

            
        } else {
            echo "Error saving passenger or phone details: " . $conn->error;
        }
    } else {
        echo "Error creating user: " . $conn->error;
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Passenger Registration - Sageline Express</title>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
  }

  body {
   background: url('images/login2.jpg') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    padding: 40px 20px; /* padding added for top visibility */
    display: flex;
    justify-content: center;
    align-items: flex-start; /* align form at top instead of center */
    overflow-y: auto; /* enable vertical scroll if needed */
    color: #fff;
  }

  .form-container {
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 16px;
    padding: 40px 30px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  h2 {
    margin-bottom: 25px;
    color: #ffffff;
    text-shadow: 0 0 10px #000;
    font-size: 1.8rem;
  }

  form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 15px;
  }

  form input,
  form select {
    flex: 1 1 48%;
    padding: 12px 15px;
    border-radius: 8px;
    border: none;
    font-size: 1em;
    background: rgba(255, 255, 255, 0.1);
    color: #f1f1f1;
    transition: background 0.3s ease, border 0.3s ease;
  }

  form input:focus,
  form select:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid #e74c3c;
  }

 form button {
  width: 100%;
  max-width: 310px; /* optional, to avoid full-width on large screens */
  margin: 10px auto 0;
  display: block;
  padding: 12px;
  background: linear-gradient(135deg, #00c6ff, #00c6ff);
  color: black;
  border: none;
  border-radius: 25px;
  font-size: 1.2em;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
}


  form button:hover {
    background: linear-gradient(135deg, #00c6ff, #00c6ff);
    transform: translateY(-2px);
  }

  .back-link {
    display: block;
    margin-top: 15px;
    color: #00c6ff;
    text-decoration: none;
    font-size: 0.9em;
    transition: opacity 0.3s ease;
  }

  .back-link:hover {
    text-decoration: underline;
    opacity: 1;
  }

  @media (max-width: 600px) {
    form input,
    form select {
      flex: 1 1 100%;
    }
  }
</style>




</head>
<body>
  <div class="form-container">
    <h2>Passenger Registration</h2>
    <form method="POST" action="">
      <input type="text" name="first_name" placeholder="First Name" required><br>
      <input type="text" name="last_name" placeholder="Last Name" required><br>
      <input type="email" name="email" id="email" placeholder="Email" required>
      <span id="email-feedback" style="color: red; font-size: 0.9em;"></span>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="number" name="age" placeholder="Age" min="1" max="100" required><br>
      <input type="tel" name="phone" placeholder="Phone Number" pattern="[0-9]{11}" maxlength="11" required>


      <select name="gender" required>
        <option value="">Select Gender</option>
        <option>Male</option>
        <option>Female</option>
      </select><br>

      <input type="text" name="street" placeholder="Street" required><br>
      <input type="text" name="city" placeholder="City" required><br>
      <input type="text" name="zipcode" placeholder="Zip Code" required><br>

      <button type="submit">Submit</button>
    </form>
    <a class="back-link" href="railway_demo.php">← Back to Home</a>
  </div>
</body>
</html>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#email').on('blur', function() {
        var email = $(this).val().trim();
        if (email !== '') {
            $.ajax({
                url: 'check_email.php',
                type: 'POST',
                data: { email: email },
                success: function(response) {
                    if (response === 'exists') {
                        $('#email-feedback').text('This email is already registered.');
                    } else {
                        $('#email-feedback').text('');
                    }
                }
            });
        } else {
            $('#email-feedback').text('');
        }
    });
});
</script>

