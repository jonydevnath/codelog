<?php
session_start(); // Start or resume session

if (isset($_SESSION['user_id'])) {
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session
}

// Redirect to the sign-in page
header("Location: signin.php");
exit();
?>
