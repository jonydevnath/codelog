<?php

// Include the database connection
include '../config/db.php';

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
        $sql = "SELECT admin_id, email, pwd FROM admin_users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            // User found, fetch the user data
            $admin_user = mysqli_fetch_assoc($result);

            // Verify the password using password_verify
            if ($password == $admin_user['pwd']) {
                // Password is correct, start a session and store user data
                session_start();
                $_SESSION['admin_id'] = $admin_user['admin_id']; // Fixed session assignment
                $_SESSION['email'] = $admin_user['email'];

                // Redirect to a dashboard or home page after successful login
                header("Location: allpost.php");
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
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <main class="container-fluid center">

        <?php 
            if (!empty($error)) {
                echo "<article style='color: #ff6347;'>$error</article>";
            }
        ?>

        <h1>Code Log Admin</h1>

        <form action="" method="post">
            <label>
                Email
                <input type="email" name="email" placeholder="Email" aria-label="Email" autocomplete="email" required>
            </label>
            <label>
                Password
                <input type="password" name="password" placeholder="Password" aria-label="Password" required>
            </label>
            <input type="submit" value="Sign in">
        </form>

    </main>

</body>

</html>
