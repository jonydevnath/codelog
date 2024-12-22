<?php include_once("header.php"); ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin_admin.php");
    exit();
}

// Initialize variables for error/success messages
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the title and post content
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $postContent = isset($_POST['post']) ? trim($_POST['post']) : '';

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory to save the uploaded images

        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . time() . '_' . $imageName; // Unique filename to avoid conflicts

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $error = "Error uploading the image.";
            $imagePath = ''; // Reset image path if upload fails
        }
    }

    // Insert the post into the database
    if (!empty($title) && !empty($postContent)) {
        $query = "INSERT INTO posts (title, img, post, created_at) VALUES ('$title', '$imagePath', '$postContent', NOW())";

        if (mysqli_query($conn, $query)) {
            $success = "Post created successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Title and post content are required.";
    }
}

// Close the database connection
mysqli_close($conn);

?>

    <?php 
        if ($error) {
            echo "<article style='color: #ff6347; width: 73%; float: right;'>$error</article>";
        }
        elseif($success) {
            echo "<article style='color: #32CD32; width: 73%; float: right;'>$success</article>";                        
        }
    ?>

    <article class="right">
        <h3>Create Post</h3>

        <form action="" method="post" enctype="multipart/form-data">
            <label>
                Title
                <input type="text" name="title" placeholder="Title" aria-label="Title" required>
            </label>

            <label>
                Upload Image
                <input type="file" name="image" accept="image/*">
            </label>

            <label>
                Write a post
                <textarea name="post" placeholder="Write what you are thinking..." aria-label="Write post" style="height: 500px;" required></textarea>
            </label>

            <input type="submit" value="Post" />
        </form>
    </article>

<?php include_once("footer.php") ?>
