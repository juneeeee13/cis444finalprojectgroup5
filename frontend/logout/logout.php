<?php
session_start(); // Continue the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

header("Location: ../login/login.php"); // Redirect to the login page
exit(); // Ensure no further code is executed
?>
