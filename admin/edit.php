<?php
include_once("header.php"); 

// Function to handle form submission
function handleFormSubmission($conn, $postId) {
    global $title, $postContent, $imagePath, $error, $success;

    // Check if the form is submitted via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize title and post content
        $title = htmlspecialchars($_POST['title']);
        $postContent = htmlspecialchars($_POST['post']);
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/"; // Directory where the file will be uploaded
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if the uploaded file is an actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "File is not an image.";
            }
        }

        // Update the post in the database
        if (!empty($title) && !empty($postContent)) {
            // Prepare the SQL query to prevent SQL injection
            $stmt = mysqli_prepare($conn, "UPDATE posts SET title = ?, img = ?, post = ? WHERE post_id = ?");

            if ($stmt) {
                // Bind the parameters
                mysqli_stmt_bind_param($stmt, "sssi", $title, $imagePath, $postContent, $postId);

                // Execute the query
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Post updated successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }

                // Close the prepared statement
                mysqli_stmt_close($stmt);
            } else {
                $error = "Failed to prepare the SQL query.";
            }
        } else {
            $error = "Title and post content are required.";
        }
    }
}

// Fetch the post data if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $postId = (int) $_GET['id']; // Sanitize the post ID to be an integer

    // Fetch the current post data from the database
    $result = mysqli_query($conn, "SELECT * FROM posts WHERE post_id = $postId LIMIT 1");

    if (mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_assoc($result);
        $title = $post['title'];
        $postContent = $post['post'];
        $imagePath = $post['img'];
    } else {
        $error = "Post not found.";
    }

    // Call the function to handle form submission (after checking for GET['id'])
    handleFormSubmission($conn, $postId);
} else {
    $error = "No post ID provided.";
}

// Close the database connection
mysqli_close($conn);
?>

    <!-- Display success or error messages -->
    <?php 
        if ($error) {
            echo "<article style='color: #ff6347; width: 73%; float: right;'>$error</article>";
        }
        elseif($success) {
            echo "<article style='color: #32CD32; width: 73%; float: right;'>$success</article>";                        
        }
    ?>

<article class="right">
    <h3>Edit Post</h3>

    <!-- Form to update the post -->
    <form action="" method="post" enctype="multipart/form-data">
        <label>
            Title
            <input type="text" name="title" placeholder="Title" aria-label="Title" value="<?php echo htmlspecialchars($title); ?>" required>
        </label>

        <label>
            Upload Image
            <input type="file" name="image" aria-label="Upload Image">
            <!-- If an image is uploaded, show the image preview -->
            <?php if ($imagePath): ?>
                <p><strong>Uploaded Image:</strong><br>
                <img src="<?php echo $imagePath; ?>" alt="Uploaded Image" style="max-width: 200px;"></p>
            <?php endif; ?>
        </label>

        <label>
            Write a post
            <textarea name="post" placeholder="Write what you are thinking..." aria-label="Write post" style="height: 500px;" required><?php echo htmlspecialchars($postContent); ?></textarea>
        </label>

        <input type="submit" value="Update Post" />
    </form>
</article>

<?php include_once("footer.php"); ?>
