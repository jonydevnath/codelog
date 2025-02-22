<?php
include_once("header.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin_admin.php");
    exit();
}

// Initialize variables for error/success messages
$error = $success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? mysqli_real_escape_string($conn, trim($_POST['title'])) : '';
    $postContent = isset($_POST['post']) ? mysqli_real_escape_string($conn, trim($_POST['post'])) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, trim($_POST['category'])) : '';

    // Handle image upload
    $imagePath = '';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $error = "Error uploading the image.";
            $imagePath = ''; // Reset image path if upload fails
        }
    }

    // Validate form fields
    if (!empty($title) && !empty($postContent) && !empty($category)) {
        // Insert the post into the database
        $query = "INSERT INTO posts (title, img, post, category, created_at) 
                  VALUES ('$title', '$imagePath', '$postContent', '$category', NOW())";

        if (mysqli_query($conn, $query)) {
            $success = "Post created successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "All fields are required.";
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!-- Display success or error messages -->
<?php if ($error): ?>
    <article style='color: #ff6347; width: 73%; float: right;'><?php echo $error; ?></article>
<?php elseif ($success): ?>
    <article style='color: #32CD32; width: 73%; float: right;'><?php echo $success; ?></article>
<?php endif; ?>

<article class="right">
    <h3>Create Post</h3>

    <form action="" method="post" enctype="multipart/form-data">
        <label>
            Title
            <input type="text" name="title" placeholder="Title" required>
        </label>

        <label>
            Category
            <select name="category" required>
                <option disabled selected value="">Select</option>
                <option value="News">News</option>
                <option value="Programming">Programming</option>
                <option value="Python">Python</option>
                <option value="Cyber Security">Cyber Security</option>
                <option value="Data Science">Data Science</option>
                <option value="Self Improvement">Self Improvement</option>
            </select>
        </label>

        <label>
            Upload Image
            <input type="file" name="image" accept="image/*">
        </label>

        <label>
            Write a post
            <textarea name="post" placeholder="Write what you are thinking..." style="height: 500px;" required></textarea>
        </label>

        <input type="submit" value="Post" />
    </form>
</article>

<?php include_once("footer.php"); ?>
