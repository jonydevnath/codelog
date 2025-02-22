<?php
include_once("header.php");

// Initialize variables
$title = "Post Not Found";
$image = "assets/placeholder.png";
$content = "Sorry, the post you are looking for does not exist.";
$error = "";
$success = "";
$comments = [];

// Fetch the post based on `post_id` from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ensure a valid post ID
if ($post_id > 0) {
    $post_id_safe = mysqli_real_escape_string($conn, $post_id);
    $query = "SELECT * FROM posts WHERE post_id = '$post_id_safe'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $title = htmlspecialchars($row['title']);
        $image = htmlspecialchars($row['img']);
        $content = nl2br(htmlspecialchars($row['post']));
        mysqli_free_result($result);
    }
}

// Fetch logged-in user name
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if (!empty($user_name) && !empty($comment)) {
        $comment_safe = mysqli_real_escape_string($conn, $comment);
        $query = "INSERT INTO comments (post_id, user_name, commnt) VALUES ('$post_id_safe', '$user_name', '$comment_safe')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Comment added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Comment cannot be empty.";
    }
}

// Fetch comments for the post
$query = "SELECT user_name, commnt, created_at FROM comments WHERE post_id = '$post_id_safe' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    mysqli_free_result($result);
}

// Fixed Comment Count Query
$count_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = '$post_id_safe'";
$cmtresult = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($cmtresult);
$comment_count = $count_row['total_comments'];

mysqli_close($conn);
?>

<main class="container blog">

    <article>

        <h2><?php echo $title; ?></h2>

        <div class="centerImg">
            <img src="admin/<?php echo $image; ?>" alt="Post Image">
        </div>

        <div>
            <hr>
                <a href=""><i class="bi bi-heart"></i></a>
                <a style="margin-left: 20px;" href=""><i class="bi bi-play-circle"></i></a>
                <a style="margin-left: 20px;" href=""><i class="bi bi-box-arrow-up"></i></a>
            <hr>
        </div>

        <p class="post">
            <?php echo $content; ?>
        </p>

    </article>

    <!-- Display success or error messages -->
    <?php if (!empty($success)): ?>
        <article style="color: #32CD32;"><?php echo $success; ?></article>
    <?php elseif (!empty($error)): ?>
        <article style="color: #ff6347;"><?php echo $error; ?></article>
    <?php endif; ?>

    <article>
        <h3>Comments</h3>
        <h6><?php echo $comment_count; ?> Comments</h6>

        <!-- Comment Form -->
        <?php if ($user_name): ?>
            <form action="" method="post">
                <textarea name="comment" placeholder="Write a comment..." aria-label="Comment" required></textarea>
                <button type="submit" class="cbtn">Comment</button>
            </form>
        <?php else: ?>
            <p>Please log in to leave a comment. <a href="signin.php" class="secondary">Sign In</a></p>
        <?php endif; ?>

        <hr>

        <!-- Display Comments -->
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <small><b><?php echo htmlspecialchars($comment['user_name']); ?></b></small><br>
                <small> ~<?php echo htmlspecialchars($comment['commnt']); ?></small><br><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </article>

</main>

<?php include_once("footer.php"); ?>
