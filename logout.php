<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // Redirect to index page
exit(); // Ensure no further code is executed
?>
