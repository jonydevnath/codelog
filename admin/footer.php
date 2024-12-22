<?php
    // Check if the user is logged in
    if (!isset($_SESSION['admin_id'])) {
        header("Location: signin_admin.php");
        exit();
    }
?>

</main>

<footer class="container">
    <small>&copy; 2024 <a href="../index.php">CodeLog.</a> All rights reserved.</small>
</footer>

<script src="scripts.js"></script>
</body>

</html>