<!---Nilanshu Basnet 104346575--->
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
	<link rel="stylesheet" type="text/css" href="style/admin_style.css">
   
</head>
<body>
	<img src="asset/Cabsonline.png" alt="CabsOnline Logo" class="title-image">
    <h2>Admin Dashboard</h2>

    <?php
    // Database connection
    $dbConnect = mysqli_connect("localhost", "root", "", "cabs_online");

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    // Display welcome message and check admin status
    session_start();
    if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: login.php");
        exit();
    }
    echo "<p>Welcome, " . $_SESSION['name'] . " (Admin)!</p>";

    // Logout link
    echo '<p><a href="logout.php">Logout</a></p>';

    // Handle List All Pick-up Requests
    // Handle List All Pick-up Requests
	if (isset($_GET['list_all'])) {
		// Query to fetch unassigned booking requests within 3 hours from now
		$query = "SELECT 
			booking_number AS `Booking Reference Number`,
			customer_email AS `Customer Email`,
			passenger_name AS `Passenger Name`,
			passenger_phone AS `Passenger Phone`,
			CONCAT(
				CASE
					WHEN pickup_unit IS NOT NULL AND pickup_unit <> '' THEN CONCAT(pickup_unit, '/', pickup_street_number)
					ELSE pickup_street_number
				END,
				' ',
				pickup_street_name,
				', ',
				pickup_suburb
			) AS `Pick-up Address`,
			destination_suburb AS `Destination Suburb`,
			pickup_datetime AS `Pick-up Date/Time`
		FROM booking
		WHERE 
			status = 'unassigned'
			AND pickup_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 HOUR)";

    $result = mysqli_query($dbConnect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h3>Pick-up Requests Within 3 Hours</h3>";
        echo "<table class='styled-table' border='1'>"; // Added border for better visibility
        echo "<thead>";
        echo "<tr>";
        echo "<th>Booking Ref Number</th>";
        echo "<th>Customer Email</th>";
        echo "<th>Passenger Name</th>";
        echo "<th>Passenger Phone</th>";
        echo "<th>Pick-up Address</th>";
        echo "<th>Destination Suburb</th>";
        echo "<th>Pick-up Date/Time</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Booking Reference Number']}</td>";
            echo "<td>{$row['Customer Email']}</td>";
            echo "<td>{$row['Passenger Name']}</td>";
            echo "<td>{$row['Passenger Phone']}</td>";
            echo "<td>{$row['Pick-up Address']}</td>";
            echo "<td>{$row['Destination Suburb']}</td>";
            echo "<td>{$row['Pick-up Date/Time']}</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No unassigned pick-up requests within 3 hours from now.</p>";
    }
}


    // Handle List All Assigned Booking Requests
    if (isset($_GET['list_assigned'])) {
        // Query to fetch assigned booking requests
        $queryAssigned = "SELECT 
			booking_number, 
			customer_email, 
			passenger_name, 
			passenger_phone, 
			CONCAT(
				CASE
					WHEN pickup_unit IS NOT NULL AND pickup_unit <> '' THEN CONCAT(pickup_unit, '/', pickup_street_number)
					ELSE pickup_street_number
				END,
				' ',
				pickup_street_name,
				', ',
				pickup_suburb
			) AS pickup_address,
			destination_suburb, 
			pickup_datetime
		FROM booking
		WHERE status = 'assigned'
		ORDER BY pickup_datetime ASC";

        $resultAssigned = mysqli_query($dbConnect, $queryAssigned);

        if (mysqli_num_rows($resultAssigned) > 0) {
            echo "<h3>Assigned Booking Requests</h3>";
            echo "<table class='styled-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Booking Ref Number</th>";
            echo "<th>Customer Email</th>";
            echo "<th>Passenger Name</th>";
            echo "<th>Passenger Phone</th>";
            echo "<th>Pick-up Address</th>";
            echo "<th>Destination Suburb</th>";
            echo "<th>Pick-up Date/Time</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_assoc($resultAssigned)) {
                echo "<tr>";
                echo "<td>{$row['booking_number']}</td>";
                echo "<td>{$row['customer_email']}</td>";
                echo "<td>{$row['passenger_name']}</td>";
                echo "<td>{$row['passenger_phone']}</td>";
                echo "<td>{$row['pickup_address']}</td>";
                echo "<td>{$row['destination_suburb']}</td>";
                echo "<td>{$row['pickup_datetime']}</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No assigned pick-up requests found.</p>";
        }
    }

    // Handle Assign Taxi for Booking Request
    if (isset($_GET['assign_taxi'])) {
        $booking_number = mysqli_real_escape_string($dbConnect, $_GET['booking_number']);

        // Update query to assign taxi
        $updateQuery = "UPDATE booking SET status = 'assigned' WHERE booking_number = '$booking_number' AND status = 'unassigned'";
        $result = mysqli_query($dbConnect, $updateQuery);

        if (mysqli_affected_rows($dbConnect) > 0) {
            echo "<p>The booking request $booking_number has been properly assigned.</p>";
        } else {
            echo "<p>Error: No unassigned booking request matched the given reference number or it may have already been assigned.</p>";
        }
    }

    // Handle Search for Booking by Reference Number
	if (isset($_GET['search_booking'])) {
		$searchBookingNumber = mysqli_real_escape_string($dbConnect, $_GET['search_booking_number']);

		// Query to check assignment status of the booking
		$querySearch = "SELECT booking_number, customer_email, passenger_name, passenger_phone, pickup_suburb, destination_suburb, pickup_datetime, status
						FROM booking
						WHERE booking_number = '$searchBookingNumber'";

		$resultSearch = mysqli_query($dbConnect, $querySearch);

		if (mysqli_num_rows($resultSearch) > 0) {
			echo "<h3>Assigned Booking Requests</h3>";
			echo "<table class='styled-table'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>Booking Ref Number</th>";
			echo "<th>Customer Email</th>";
			echo "<th>Passenger Name</th>";
			echo "<th>Passenger Phone</th>";
			echo "<th>Pick-up Address</th>";
			echo "<th>Destination Suburb</th>";
			echo "<th>Pick-up Date/Time</th>";
			echo "<th>Status</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			while ($row = mysqli_fetch_assoc($resultSearch)) {
				echo "<tr>";
				echo "<td>{$row['booking_number']}</td>";
				echo "<td>{$row['customer_email']}</td>";
				echo "<td>{$row['passenger_name']}</td>";
				echo "<td>{$row['passenger_phone']}</td>";
				echo "<td>{$row['pickup_suburb']}</td>";
				echo "<td>{$row['destination_suburb']}</td>";
				echo "<td>{$row['pickup_datetime']}</td>";
				echo "<td>{$row['status']}</td>";
				echo "</tr>";
			}

			echo "</tbody>";
			echo "</table>";
		} else {
			echo "<p>No booking found with the reference number '$searchBookingNumber'.</p>";
		}
	}

    ?>

    <h3>Admin Dashboard Functions</h3>
    <form method="GET" action="admin.php">
        <input class="action-button" type="submit" name="list_all" value="List All Pick-up Requests">
    </form>

    <h3>Assign Taxi for a Booking Request</h3>
    <form method="GET" action="admin.php">
        <label for="booking_number">Booking Reference Number:</label>
        <input class="input" type="text" id="booking_number" name="booking_number" required>
        <input class="smallaction-button" type="submit" name="assign_taxi" value="Update">
    </form>

    <h3>List All Assigned Booking Requests</h3>
    <form method="GET" action="admin.php">
        <input class="action-button" type="submit" name="list_assigned" value="List All Assigned Requests">
    </form>

    <h3>Search for Booking by Reference Number</h3>
    <form method="GET" action="admin.php">
        <label for="search_booking_number">Booking Reference Number:</label>
        <input class="input" type="text" id="search_booking_number" name="search_booking_number" required>
        <input class="smallaction-button" type="submit" name="search_booking" value="Search">
    </form>

</body>
</html>
