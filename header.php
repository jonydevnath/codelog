<?php
    // display errors
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    // Include the database connection
    include 'config/db.php';

    session_start(); // Start the session

    // Check if the user is logged in by checking session data
    $isLoggedIn = isset($_SESSION['username']); // True if logged in, false otherwise
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Log</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="container">
        <ul>
            <li><a href="index.php"><h3>Code Log</h3></a></li>
        </ul>
        <ul>
            <li>
                <input type="search" name="search" placeholder="Search" aria-label="Search" />
            </li>
        </ul>
        <ul>
            <?php if (!$isLoggedIn): ?>
                <li><a href="signin.php" class="secondary">Sign In</a></li>
            <?php else: ?>
                <li>
                    <details class="dropdown">
                        <summary>
                            <?php echo explode(' ', $_SESSION['username'])[0]; ?> <!-- Display first name -->
                        </summary>
                        <ul dir="rtl">
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </details>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
