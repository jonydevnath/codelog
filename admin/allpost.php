<?php include_once("header.php"); ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin_admin.php");
    exit();
}

// Fetch all posts from the database
function fetchPosts($conn) {
    $query = "SELECT * FROM posts";
    $result = mysqli_query($conn, $query);

    // Check if query was successful
    if (!$result) {
        echo "Error fetching posts: " . mysqli_error($conn);
        return [];
    }

    // Fetch all posts as an associative array
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Call the function to fetch posts
$posts = fetchPosts($conn);

// Close the database connection
mysqli_close($conn);

?>

        <article class="right">
            <a href="create.php"><button class="outline">Create Post</button></a>
            <br><br>

            <table>
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Title</th>
                        <th scope="col">Image</th>
                        <th scope="col">Operation</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $index => $post): ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1; ?></th>

                        <td>
                            <p>
                                <?php echo htmlspecialchars($post['title']); ?>
                            </p>
                        </td>

                        <td style="width: 20%;">
                            <img src="<?php echo htmlspecialchars($post['img']); ?>" alt="Post Image">
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $post['post_id']; ?>"><i class="bi bi-pencil-square"></i></a>

                            <a href="delete.php?id=<?php echo $post['post_id']; ?>" style="margin-left: 20px; color:#ff6347;"
                                onclick="return confirm('Are you sure you want to delete this post?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No posts available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

        </article>

<?php include_once("footer.php") ?>
