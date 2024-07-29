<!---Nilanshu Basnet 104346575--->
<!DOCTYPE html>
<html>
<head>
    <title>Booking Page</title> <!-- Page title -->
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <style>
        body {
            height: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="main-container">
    <img src="asset/Cabsonline.png" alt="CabsOnline Logo" class="title-image">

    <?php
    // Function to generate random alphanumeric string
    function generateRandomString($length = 4) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	
	// Function to validate phone number
    function validatePhoneNumber($phone) {
        return preg_match('/^\d{10}$/', $phone);
    }

    // Check if session is started, redirect to login if not logged in
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: login.php");
        exit();
    }

    // Display welcome message
    $name = ucfirst($_SESSION['name']);  // Capitalize the first letter
	echo "<p class='testing'>Welcome, {$name}!</p>";

    // Link to logout
    echo '<p><a href="logout.php">Logout</a></p>';

    // Initialize variables to hold form data and errors
    $passengerName = $passengerPhone = $pickupUnitNumber = $pickupStreetNumber = $pickupStreetName = $pickupSuburb = $destinationSuburb = $pickupDate = $pickupTime = '';
    $errors = [];

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['submit'])) {
        // Retrieve form data
        $passengerName = $_GET['passenger_name'] ?? '';
        $passengerPhone = $_GET['passenger_phone'] ?? '';
        $pickupUnitNumber = $_GET['pickup_unit'] ?? '';
        $pickupStreetNumber = $_GET['pickup_street_number'] ?? '';
        $pickupStreetName = $_GET['pickup_street_name'] ?? '';
        $pickupSuburb = $_GET['pickup_suburb'] ?? '';
        $destinationSuburb = $_GET['destination_suburb'] ?? '';
        $pickupDate = $_GET['pickup_date'] ?? '';
        $pickupTime = $_GET['pickup_time'] ?? '';

        // Validate input items
        if (empty($passengerName) || empty($passengerPhone) || empty($pickupStreetNumber) || empty($pickupStreetName) || empty($pickupSuburb) || empty($destinationSuburb) || empty($pickupDate) || empty($pickupTime)) {
            $errors[] = "All input items except unit number must be provided.";
        } elseif (!validatePhoneNumber($passengerPhone)) {
            $errors[] = "Passenger phone number must be exactly 10 digits.";
        } else {
            // Connect to database
            $dbConnect = @mysqli_connect("localhost", "root", "", "cabs_online");

            if (!$dbConnect) {
                die("<p>Unable to connect to the database server.</p>");
            }

            // Retrieve current date and time from the database
            $currentDateTimeQuery = "SELECT NOW() AS current_datetime";
            $currentDateTimeResult = mysqli_query($dbConnect, $currentDateTimeQuery);
            if ($currentDateTimeRow = mysqli_fetch_assoc($currentDateTimeResult)) {
                $currentDateTime = new DateTime($currentDateTimeRow['current_datetime']);
            } else {
                die("<p>Unable to retrieve current date and time.</p>");
            }

            // Validate pickup date/time
            $bookingDateTime = new DateTime("$pickupDate $pickupTime");
            $interval = $currentDateTime->diff($bookingDateTime);
            
            // Check if pickup date/time is at least 40 minutes in the future
            if ($interval->format('%R') == '-' || $interval->format('%i') < 40) {
                $errors[] = "Pickup date/time must be at least 40 minutes after the current time.";
            }

            // If no errors, proceed to save booking
            if (empty($errors)) {
                // Generate unique booking reference number
                do {
                    $bookingReferenceNumber = $bookingDateTime->format('dmHi') . generateRandomString();
                    $checkQuery = "SELECT * FROM booking WHERE booking_number='$bookingReferenceNumber'";
                    $checkResult = mysqli_query($dbConnect, $checkQuery);
                } while (mysqli_num_rows($checkResult) > 0);

                // Prepare data for insertion
                $status = 'unassigned';

                // Insert into database
                $insertQuery = "INSERT INTO booking (booking_number, customer_email, passenger_name, passenger_phone, pickup_unit, pickup_street_number, pickup_street_name, pickup_suburb, destination_suburb, pickup_datetime, booking_datetime, status)
                                VALUES ('$bookingReferenceNumber', '{$_SESSION['email']}', '$passengerName', '$passengerPhone', '$pickupUnitNumber', '$pickupStreetNumber', '$pickupStreetName', '$pickupSuburb', '$destinationSuburb', '$pickupDate $pickupTime', NOW(), '$status')";
                $result = mysqli_query($dbConnect, $insertQuery);

                if ($result) {
                    // Display confirmation
                    echo "<p>Thank you! Your booking reference number is <b> $bookingReferenceNumber. </b>We will pick up the passengers in front of your provided address at<b> $pickupTime </b>on<b> $pickupDate.</b></p>";
					 
					/*Prepare email details
                    $to = $_SESSION['email'];
                    $subject = "Your booking request with CabsOnline!";
                    $message = "Dear {$name},\n\nThanks for booking with CabsOnline! Your booking reference number is $bookingReferenceNumber. We will pick up the passengers in front of your provided address at $pickupTime on $pickupDate.\n\nBest regards,\nCabsOnline Team";
                    $headers = "From: booking@cabsonline.com.au\r\n";
                    $headers .= "Reply-To: booking@cabsonline.com.au\r\n";
                    
                    // Send confirmation email
                    $emailSent = mail($to, $subject, $message, $headers, "-r 1234567@student.swin.edu.au");

                    if ($emailSent) {
                        echo "<p>A confirmation email has been sent to <b>$to</b>.</p>";
                    } else {
                        echo "<p>Error sending confirmation email.</p>";
                    }*/
					
                } else {
                    echo "<p class='errormsg'>Failed to process booking. Please try again later.</p>";
                }

                // Close database connection
                mysqli_close($dbConnect);
            }
        }
    }
    ?>

    <!-- Booking form -->
    <h3>Book a Cab</h3>
    <form method="GET" action="booking.php">
        <input class="input" placeholder="Passenger Name" type="text" id="passenger_name" name="passenger_name" value="<?php echo htmlspecialchars($passengerName); ?>" required><br><br>
        <input class="input" placeholder="Passenger Phone" type="tel" id="passenger_phone" name="passenger_phone" value="<?php echo htmlspecialchars($passengerPhone); ?>" required><br><br>
        
        <label>Pickup Address</label><br>
        <input class="input" placeholder="Unit Number" type="text" id="pickup_unit" name="pickup_unit" value="<?php echo htmlspecialchars($pickupUnitNumber); ?>"><br><br>
        <input class="input" placeholder="Street Number" type="text" id="pickup_street_number" name="pickup_street_number" value="<?php echo htmlspecialchars($pickupStreetNumber); ?>" required><br><br>
        <input class="input" placeholder="Street Name" type="text" id="pickup_street_name" name="pickup_street_name" value="<?php echo htmlspecialchars($pickupStreetName); ?>" required><br><br>
        <input class="input" placeholder="Suburb" type="text" id="pickup_suburb" name="pickup_suburb" value="<?php echo htmlspecialchars($pickupSuburb); ?>" required><br><br>
        <input class="input" placeholder="Destination Suburb" type="text" id="destination_suburb" name="destination_suburb" value="<?php echo htmlspecialchars($destinationSuburb); ?>" required><br><br>
        
        <label for="pickup_date">Pickup Date:</label>
        <input class="input" type="date" id="pickup_date" name="pickup_date" value="<?php echo htmlspecialchars($pickupDate); ?>" required><br><br>
        
        <label for="pickup_time">Pickup Time:</label>
        <input class="input" type="time" id="pickup_time" name="pickup_time" value="<?php echo htmlspecialchars($pickupTime); ?>" required><br><br>
        
        <input type="submit" name="submit" value="Book" class="action-button">
    </form>

    <?php
    // Display errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='errormsg'>$error</p>";
        }
    }
    ?>
</div>
</body>
</html>
