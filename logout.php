<?php
//Nilanshu Basnet 104346575
// Start session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
