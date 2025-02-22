<?php
include_once("header.php"); 

$error = $success = "";
$title = $postContent = $imagePath = $category = "";

// Check if a post ID is provided
if (isset($_GET['id'])) {
    $postId = (int)$_GET['id']; // Sanitize input

    // Fetch the post data
    $query = "SELECT * FROM posts WHERE post_id = $postId LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $post = mysqli_fetch_assoc($result);
        $title = $post['title'];
        $postContent = $post['post'];
        $imagePath = $post['img'];
        $category = $post['category']; // Fetch stored category
    } else {
        $error = "Post not found.";
    }
} else {
    $error = "No post ID provided.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $postContent = htmlspecialchars($_POST['post']);
    $category = htmlspecialchars($_POST['category']); // Get selected category

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate if the file is an image
        if (getimagesize($_FILES["image"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $error = "Error uploading the file.";
            }
        } else {
            $error = "Invalid image file.";
        }
    }

    // Validate and update post
    if (!empty($title) && !empty($postContent) && !empty($category)) {
        $query = "UPDATE posts SET title = '$title', img = '$imagePath', post = '$postContent', category = '$category' WHERE post_id = $postId";
        if (mysqli_query($conn, $query)) {
            $success = "Post updated successfully!";
        } else {
            $error = "Error updating post: " . mysqli_error($conn);
        }
    } else {
        $error = "All fields are required.";
    }
}

// Close connection
mysqli_close($conn);
?>

<!-- Display success or error messages -->
<?php if ($error): ?>
    <article style='color: #ff6347; width: 73%; float: right;'><?php echo $error; ?></article>
<?php elseif ($success): ?>
    <article style='color: #32CD32; width: 73%; float: right;'><?php echo $success; ?></article>
<?php endif; ?>

<article class="right">
    <h3>Edit Post</h3>

    <!-- Form to update the post -->
    <form action="" method="post" enctype="multipart/form-data">
        <label>
            Title
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </label>

        <label>
            Category
            <select name="category" required>
                <option disabled value="">Select</option>
                <option value="News" <?php if ($category == "News") echo "selected"; ?>>News</option>
                <option value="Programming" <?php if ($category == "Programming") echo "selected"; ?>>Programming</option>
                <option value="Python" <?php if ($category == "Python") echo "selected"; ?>>Python</option>
                <option value="Cyber Security" <?php if ($category == "Cyber Security") echo "selected"; ?>>Cyber Security</option>
                <option value="Data Science" <?php if ($category == "Data Science") echo "selected"; ?>>Data Science</option>
                <option value="Self Improvement" <?php if ($category == "Self Improvement") echo "selected"; ?>>Self Improvement</option>
            </select>
        </label>

        <label>
            Upload Image
            <input type="file" name="image">
            <?php if ($imagePath): ?>
                <p><strong>Uploaded Image:</strong><br>
                <img src="<?php echo $imagePath; ?>" alt="Uploaded Image" style="max-width: 200px;"></p>
            <?php endif; ?>
        </label>

        <label>
            Write a post
            <textarea name="post" required><?php echo htmlspecialchars($postContent); ?></textarea>
        </label>

        <input type="submit" value="Update Post" />
    </form>
</article>

<?php include_once("footer.php"); ?>
