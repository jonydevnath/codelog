<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: signin_admin.php"); // Redirect to sign-in page
exit();
?>