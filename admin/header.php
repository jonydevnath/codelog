<?php
    // display errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start(); // Start the session

    // Include the database connection
    include '../config/db.php';
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Log</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <main class="container" style="overflow: hidden;">

        <article class="left">
            <a href="allpost.php" style="text-decoration: none;">
                <h3>Code Log Admin</h3>
            </a>

            <a href="allpost.php" class="custom-link active">All Post</a><br>
            <a href="admin_pass.php" class="custom-link">Change Password</a><br>
            <a href="logout.php" class="custom-link">Logout</a>

            <br><br>

            <small>
                <a href="../index.php" target="_blank" style="text-decoration: none;">
                    Visit CodeLog
                </a>
            </small>

        </article>