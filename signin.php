<?php
// Start session
session_start();

// Include the database connection
include 'config/db.php';

// Initialize variables for error/success messages
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve user input
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Check if the email and password are provided
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Query to check if the user exists with the provided email
        $sql = "SELECT user_id, user_pass, username FROM users WHERE user_email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // User found, fetch the user data
            $user = mysqli_fetch_assoc($result);

            // Verify the password using password_verify
            if (password_verify($password, $user['user_pass'])) {
                // Password is correct, start a session and store user data
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email;

                // Redirect to a dashboard or home page after successful login
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <main class="container-fluid center">

        <?php 
            if (!empty($error)) {
                echo "<article style='color: #ff6347;'>$error</article>";
            }
        ?>

        <h1>Code Log</h1>

        <form action="" method="post">
            <input type="email" name="email" placeholder="Email" aria-label="Email" autocomplete="email">
            <input type="password" name="password" placeholder="Password" aria-label="Password">
            <input type="submit" value="Sign in">
        </form>

        <small>No account? <a href="signup.php">Create one</a></small>

    </main>

</body>

</html>