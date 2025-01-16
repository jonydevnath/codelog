<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin_admin.php"); // Redirect to the sign-in page if not logged in
    exit();
}

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the sign-in page
header("Location: signin_admin.php");
exit();
?>
