<?php
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to the sign-in page if not logged in
    exit();
}

session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: signin.php"); // Redirect to sign-in page
exit();
?>