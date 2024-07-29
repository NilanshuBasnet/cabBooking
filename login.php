<!---Nilanshu Basnet 104346575--->
<!DOCTYPE html>
<html>
<head>
    <title>Login to CabsOnline</title> <!-- Page title -->
	<link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
<div class="main-container">
    <?php
    // Attempt to connect to the MySQL database server
    $dbConnect = @mysqli_connect("localhost", "root", "");

    if (!$dbConnect) {
        die("<p class='errormsg'>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_connect_errno() . ": " . mysqli_connect_error() . "</p>");
    }
    //echo "<p>Successfully connected to the database server.</p>";

    // Database name
    $dbName = "cabs_online";

    // Check if the database exists
    $checkDbQuery = "SHOW DATABASES LIKE '$dbName'";
    $result = mysqli_query($dbConnect, $checkDbQuery);

    if (mysqli_num_rows($result) == 0) {
		// Redirect to login page after registration
        header("Location: register.php");
        exit();
        die("<p class='errormsg'>The '$dbName' database does not exist. Please visit register.php to create the database + admin user automatically and return to this page for Admin login or register new user through Register page.</p><br><a href='register.php' class='bottom-link'>Register here</a>");
    }

    // Select the database
    mysqli_select_db($dbConnect, $dbName);

    // Check if the customer table exists
    $tableName = "customer";
    $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
    $result = mysqli_query($dbConnect, $checkTableQuery);

    if (mysqli_num_rows($result) == 0) {
        die("<p class='errormsg'>The '$tableName' table does not exist. Please contact your administrator.</p>");
    }

    // Check if email and password are provided via GET method
    if (isset($_GET['email']) && isset($_GET['password'])) {
        $email = mysqli_real_escape_string($dbConnect, $_GET['email']);
        $password = mysqli_real_escape_string($dbConnect, $_GET['password']);

        // Check if email and password are not empty
        if (!empty($email) && !empty($password)) {
            // Query to fetch user details based on email and password
            $query = "SELECT * FROM customer WHERE email='$email' AND password='$password'";
            $result = mysqli_query($dbConnect, $query);

            if (!$result) {
                die("<p class='errormsg'>Query error: " . mysqli_error($dbConnect) . "</p>");
            }

            // Check if user exists
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);

                // Start session and store user details
                session_start();
                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['is_admin'] = $row['is_admin'];

                // Redirect to appropriate page based on admin status
                if ($_SESSION['is_admin'] == 1) {
                    header("Location: admin.php");
                } else {
                    header("Location: booking.php");
                }
                exit();
            } else {
                echo '<p class="errormsg">Invalid email or password. Please try again.</p>';
            }
        } else {
            echo "<p class='errormsg'>Email and password are required. Please enter both fields.</p>";
        }
    }

    // The HTML form for login
    echo '
	
        <img src="asset/Cabsonline.png" alt="CabsOnline Logo" class="title-image">
        <form method="GET" action="login.php">
            <input type="email" class="input" id="email" name="email" placeholder="Email" required><br><br>
            <input type="password" class="input" id="password" name="password" placeholder="Password" required><br><br>
            <input type="submit" value="Login" class="action-button">
        </form>
        <p class="bottom-text">Not registered? <a href="register.php" class="bottom-link">Register here</a></p>';

    // Close the database connection
    mysqli_close($dbConnect);
    ?>
</div>
</body>
</html>
