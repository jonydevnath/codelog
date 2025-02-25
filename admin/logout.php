<?php
session_start(); // Start or resume session

if (isset($_SESSION['admin_id'])) {
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session
}

// Redirect to the sign-in page
header("Location: signin_admin.php");
exit();
?>
