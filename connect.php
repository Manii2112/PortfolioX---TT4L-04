<?php
$servername = "localhost";
$username = "root";  // default username for XAMPP
$password = "";      // default password is empty
$dbname = "mmu_portfoliox_db";  // make sure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

