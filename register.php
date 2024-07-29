<!---Nilanshu Basnet 104346575--->
<!DOCTYPE html>
<html>
<head>
    <title>Register to CabsOnline</title>
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
<div class="main-container">
    <?php
    session_start();
    
    // Attempt to connect to the MySQL database server
    $dbConnect = @mysqli_connect("localhost", "root", "")
        or die("<p class='errormsg'>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_connect_errno() . ": " . mysqli_connect_error() . "</p>");
    //echo "<p>Successfully connected to the database server.</p>";

    // Database name
    $dbName = "cabs_online";

    // Check if the database exists, create it if it doesn't
    $checkDbQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'";
    $result = mysqli_query($dbConnect, $checkDbQuery);

    if (mysqli_num_rows($result) == 0) {
        $createDbQuery = "CREATE DATABASE $dbName";
        if (mysqli_query($dbConnect, $createDbQuery)) {
            echo "<p class='successmsg'>Database '$dbName' created successfully.</p>";
        } else {
            die("<p class='errormsg'>Unable to create the database '$dbName'.</p>" . "<p>Error code " . mysqli_errno($dbConnect) . ": " . mysqli_error($dbConnect) . "</p>");
        }
    }

    // Select the database
    mysqli_select_db($dbConnect, $dbName)
        or die("<p class='errormsg'>Unable to select the database '$dbName'.</p>" . "<p>Error code " . mysqli_errno($dbConnect) . ": " . mysqli_error($dbConnect) . "</p>");
    //echo "<p>Successfully opened the database '$dbName'.</p>";

    // Create the customer table if it doesn't exist
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS customer (
        email VARCHAR(50) NOT NULL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        is_admin BOOLEAN NOT NULL DEFAULT 0
    )";
    mysqli_query($dbConnect, $createTableQuery) or die("<p class='errormsg'>Unable to create the table.</p>" . "<p>Error code " . mysqli_errno($dbConnect) . ": " . mysqli_error($dbConnect) . "</p>");
    //echo "<p>Successfully ensured the customer table exists.</p>";
	$createBookingTableQuery = "
	CREATE TABLE IF NOT EXISTS booking (
		booking_number VARCHAR(50) NOT NULL PRIMARY KEY,
		customer_email VARCHAR(50) NOT NULL,
		passenger_name VARCHAR(100) NOT NULL,
		passenger_phone VARCHAR(15) NOT NULL,
		pickup_unit VARCHAR(10),
		pickup_street_number VARCHAR(10),
		pickup_street_name VARCHAR(100),
		pickup_suburb VARCHAR(100),
		destination_suburb VARCHAR(100),
		pickup_datetime DATETIME NOT NULL,
		booking_datetime DATETIME NOT NULL,
		status VARCHAR(50) NOT NULL,
		FOREIGN KEY (customer_email) REFERENCES customer(email) ON DELETE CASCADE
	)";
	if (!$dbConnect->query($createBookingTableQuery)) {
		die("<p class='errormsg'>Unable to create the booking table.</p>" . "<p>Error code " . $dbConnect->errno . ": " . $dbConnect->error . "</p>");
	}
    // Check if default admin exists, if not, insert default admin
    $defaultAdminEmail = 'admin@cabsonline.com';
    $defaultAdminQuery = "SELECT * FROM customer WHERE email='$defaultAdminEmail'";
    $result = mysqli_query($dbConnect, $defaultAdminQuery);
    if (mysqli_num_rows($result) == 0) {
        $defaultAdminName = 'Admin';
        $defaultAdminPassword = 'admin'; // Plain text password (not recommended)
        $defaultAdminPhone = '1234567890';
        $defaultAdminIsAdmin = 1;
        $insertDefaultAdminQuery = "
        INSERT INTO customer (email, name, password, phone, is_admin) 
        VALUES ('$defaultAdminEmail', '$defaultAdminName', '$defaultAdminPassword', '$defaultAdminPhone', $defaultAdminIsAdmin)";
        mysqli_query($dbConnect, $insertDefaultAdminQuery) or die("<p>Unable to insert the default admin data.</p>" . "<p>Error code " . mysqli_errno($dbConnect) . ": " . mysqli_error($dbConnect) . "</p>");
        echo "<p class='successmsg'>Successfully added the default admin user.</p>";
    }
	// Check if the booking table is empty
	$checkQuery = "SELECT COUNT(*) AS count FROM booking";
	$result = mysqli_query($dbConnect, $checkQuery);

	// Error handling for the query
	if (!$result) {
		die("Error executing query: " . mysqli_error($dbConnect));
	}

	$row = mysqli_fetch_assoc($result);

	if ($row['count'] == 0) {
		// Table is empty, insert data
		$dummydata = "
		INSERT INTO booking (
			booking_number, customer_email, passenger_name, passenger_phone, pickup_unit,
			pickup_street_number, pickup_street_name, pickup_suburb, destination_suburb,
			pickup_datetime, booking_datetime, status
		) VALUES
		('12345678ABCD', 'admin@cabsonline.com', 'John Doe', '5551234567', '10A', '123', 'Main St', 'Downtown', 'Uptown', '2024-07-19 08:30:00', '2024-07-18 15:00:00', 'assigned'),
		('23456789EFGH', 'admin@cabsonline.com', 'Jane Smith', '5552345678', '5B', '456', 'Elm St', 'Midtown', 'City Center', '2024-07-19 09:00:00', '2024-07-18 16:00:00', 'unassigned'),
		('34567890IJKL', 'admin@cabsonline.com', 'Alice Johnson', '5553456789', '12C', '789', 'Maple Ave', 'Old Town', 'New City', '2024-07-19 10:15:00', '2024-07-18 17:30:00', 'assigned'),
		('45678901MNOP', 'admin@cabsonline.com', 'Bob Brown', '5554567890', '3D', '101', 'Oak St', 'Suburbia', 'Airport', '2024-07-19 11:00:00', '2024-07-18 18:45:00', 'unassigned'),
		('56789012QRST', 'admin@cabsonline.com', 'Carol White', '5555678901', '8E', '202', 'Pine St', 'Greenfield', 'Mall', '2024-07-19 13:30:00', '2024-07-18 19:00:00', 'assigned')";

		if (mysqli_query($dbConnect, $dummydata)) {
			echo "<p class='successmsg'>New Dummy records created successfully</p>";
		} else {
			echo "Error: " . $dummydata . "<br>" . mysqli_error($dbConnect);
		}
	}

    // The HTML form for registering
    echo '
	
		<h2>Register to CabsOnline</h2>
		<form method="GET" action="register.php">
			<input class="input" placeholder="Name" type="text" id="name" name="name" required><br><br>
			<input class="input" placeholder="Password" type="password" id="password" name="password" required><br><br>
			<input class="input" placeholder="Confirm Password" type="password" " id="confirm_password" name="confirm_password" required><br><br>
			<input class="input" placeholder="Email" type="email" id="email" name="email" required><br><br>
			<input class="input" placeholder="Phone" type="tel" id="phone" name="phone" required><br><br>
			<label for="is_admin">New Admin:</label>
			<input type="checkbox" id="is_admin" name="is_admin"><br><br>
			<input type="submit" value="Register" class="action-button">
		</form>
		<p class="bottom-text">Already registered? <a href="login.php" class="bottom-link">Login here</a></p>';

    // Insert user data if form is submitted
    if (isset($_GET['name']) && isset($_GET['password']) && isset($_GET['confirm_password']) && isset($_GET['email']) && isset($_GET['phone'])) {
        $name = mysqli_real_escape_string($dbConnect, $_GET['name']);
        $password = mysqli_real_escape_string($dbConnect, $_GET['password']);
        $confirm_password = mysqli_real_escape_string($dbConnect, $_GET['confirm_password']);
        $email = mysqli_real_escape_string($dbConnect, $_GET['email']);
        $phone = mysqli_real_escape_string($dbConnect, $_GET['phone']);
        $is_admin = isset($_GET['is_admin']) ? 1 : 0;

        // Check if all fields are filled
        if (!empty($name) && !empty($password) && !empty($confirm_password) && !empty($email) && !empty($phone)) {
            // Check if passwords match
            if ($password === $confirm_password) {
                // Validate phone number format (10 digits)
                if (preg_match('/^[0-9]{10}$/', $phone)) {
                    // Check if email is unique
                    $emailQuery = "SELECT * FROM customer WHERE email='$email'";
                    $result = mysqli_query($dbConnect, $emailQuery);

                    if (mysqli_num_rows($result) == 0) {
                        // Check if registering as admin
                        if ($is_admin == 1) {
                            // Ensure a logged-in admin is performing the registration
                            if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
                                die("<p class='errormsg'>Error! Only an admin can register another admin.</p>");
                            }
                        }

                        // Insert data into the database
                        $insertQuery = "INSERT INTO customer (email, name, password, phone, is_admin) VALUES ('$email', '$name', '$password', '$phone', $is_admin)";
                        mysqli_query($dbConnect, $insertQuery) or die("<p>Unable to insert the data.</p>" . "<p>Error code " . mysqli_errno($dbConnect) . ": " . mysqli_error($dbConnect) . "</p>");
                        echo "<p class='successmsg'>New record added successfully.</p>";

                        // Redirect to login page after registration
                        header("Location: login.php");
                        exit();
                    } else {
                        echo "<p class='errormsg'>Error! Email address is already in use.</p>";
                    }
                } else {
                    echo "<p class='errormsg'>Error! Phone number must be 10 digits.</p>";
                }
            } else {
                echo "<p class='errormsg'>Error! Passwords do not match.</p>";
            }
        } else {
            echo "<p class='errormsg'>Error! All fields are required. Please fill in all fields.</p>";
        }
    }

    // Close the database connection
    mysqli_close($dbConnect);
    ?>
</div>
</body>
</html>
