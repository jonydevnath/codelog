<?php
include_once("header.php");

// Initialize variables for error messages
$error = "";

// Check if 'id' is present in the URL query string
if (isset($_GET['id'])) {
    // Sanitize the ID (cast to an integer)
    $postId = (int) $_GET['id'];

    // Validate that the postId is greater than 0
    if ($postId > 0) {
        // Create a DELETE query
        $query = "DELETE FROM posts WHERE post_id = $postId";

        // Execute the query
        $result = mysqli_query($conn, $query);

        // Check if the deletion was successful
        if ($result) {
            // Redirect to the posts listing page after successful deletion
            header("Location: allpost.php");  // Change 'allpost.php' to your posts list page
            exit();
        } else {
            // If there was an error in deletion
            $error = "Error deleting post: " . mysqli_error($conn);
        }
    } else {
        // If the ID is not valid
        $error = "Invalid post ID.";
    }
} else {
    // If no ID is provided in the URL
    $error = "No post ID provided.";
}

// Close the database connection
mysqli_close($conn);

?>
