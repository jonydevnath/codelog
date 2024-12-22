<?php
// Include database connection
include 'config/db.php';

// Initialize variables for error/success messages
$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Validate required fields
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Check if the username or email already exists
        $sql_check = "SELECT user_id FROM users WHERE username = '$username' OR user_email = '$email'";
        $result_check = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Username or email already exists.";
        } else {
            // Insert the new user into the database
            $sql_insert = "INSERT INTO users (username, user_email, user_pass) VALUES ('$username', '$email', '$passwordHash')";

            if (mysqli_query($conn, $sql_insert)) {
                $success = "Sign-up successful! You can now sign in...";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    
    
    <main class="container-fluid center">
  
        <?php 
            if ($error) {
                echo "<article style='color: #ff6347;'>$error</article>";
            }
            elseif($success) {
                echo "<article style='color: #32CD32;'>$success</article>";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'signin.php';
                        }, 3000); // 3 seconds
                    </script>";
            }
        ?>

        <h1>Code Log</h1>

        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" aria-label="Text">
            <input type="email" name="email" placeholder="Email" aria-label="Email" autocomplete="email">
            <input type="password" name="password" placeholder="Password" aria-label="Password">
            <input type="submit" value="Sign up">
        </form>

        <small>Already have an account? <a href="signin.php">Sign in</a></small>


    </main>

</body>

</html>