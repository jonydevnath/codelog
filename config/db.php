<?php
// Database configuration
$host = 'localhost';  // Database host
$db = 'codeLog';  // Database name
$user = 'root';  // Database user
$pass = '';

// Establish connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
