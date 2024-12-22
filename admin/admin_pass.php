<?php include_once("header.php") ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin_admin.php");
    exit();
}

// Initialize error and success messages
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve user input
    $currentPassword = trim($_POST['password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match.";
    } else {
        // Fetch the user's current password hash from the database
        $adminId = $_SESSION['admin_id'];
        $sql = "SELECT pwd FROM admin_users WHERE admin_id = $adminId";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $hashedPassword = $user['pwd'];

            // Verify the current password
            if ($currentPassword == $hashedPassword) {

                // Update the password in the database
                $updateSql = "UPDATE admin_users SET pwd = '$newPassword' WHERE admin_id = $adminId";
                if (mysqli_query($conn, $updateSql)) {
                    $success = "Password updated successfully.";
                } else {
                    $error = "Error updating the password. Please try again.";
                }
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>

        <article class="right">
            <?php 
                // Display error or success messages
                if ($error) {
                    echo "<span style='color: #ff6347;'>$error</span>";
                }
                if ($success) {
                    echo "<span style='color: #32CD32;'>$success</span>";
                }
            ?>
        </article>

        <article class="right">
            <h3>Change Password</h3>
            <form action="" method="post">
                <label>
                    Current Password
                    <input type="password" name="password" placeholder="Password" aria-label="Password">
                </label>

                <label>
                    New Password
                    <input type="password" name="new_password" placeholder="New Password"
                        aria-label="New Password">
                </label>

                <label>
                    Confirm Password
                    <input type="password" name="confirm_password" placeholder="Confirm Password"
                        aria-label="Confirm Password">
                </label>

                <input type="submit" value="Update Password" />
            </form>

        </article>

<?php include_once("footer.php") ?>
