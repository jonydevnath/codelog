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
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
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

    // Like or Unlike Post
    if (isset($_POST['like_action'])) {
        $action = $_POST['like_action'];

        // Check like/unlike action
        if ($action === 'like') {
            $like_query = "INSERT INTO likes (post_id, user_id) VALUES ('$post_id_safe', '$user_id')";
            mysqli_query($conn, $like_query);
        } elseif ($action === 'unlike') {
            $unlike_query = "DELETE FROM likes WHERE post_id = '$post_id_safe' AND user_id = '$user_id'";
            mysqli_query($conn, $unlike_query);
        }
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

// Fetch like count and check if the user liked the post
$like_query = "SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = '$post_id_safe'";
$like_result = mysqli_query($conn, $like_query);
$like_row = mysqli_fetch_assoc($like_result);
$like_count = $like_row['total_likes'];

$has_liked_query = "SELECT * FROM likes WHERE post_id = '$post_id_safe' AND user_id = '$user_id'";
$has_liked_result = mysqli_query($conn, $has_liked_query);
$has_liked = mysqli_num_rows($has_liked_result) > 0;

$comment_count_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = '$post_id_safe'";
$cmtresult = mysqli_query($conn, $comment_count_query);
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

            <!-- Like Icon -->
            <?php if ($is_logged_in): ?>
            <?php if ($has_liked): ?>
            <!-- Unlike Icon (Filled Heart) -->
            <form action="" method="post" style="display:inline;">
                <input type="hidden" name="like_action" value="unlike">
                <i class="bi bi-heart-fill" style="color: #01AAFF; cursor: pointer;"
                    onclick="this.closest('form').submit();"></i>
            </form>
            <?php else: ?>
            <!-- Like Icon (Empty Heart) -->
            <form action="" method="post" style="display:inline;">
                <input type="hidden" name="like_action" value="like">
                <i class="bi bi-heart" style="color: #01AAFF; cursor: pointer;"
                    onclick="this.closest('form').submit();"></i>
            </form>
            <?php endif; ?>
            <?php else: ?>
            <!-- Redirect to login if not logged in -->
            <a href="signin.php" style="text-decoration: none; display: inline-block;">
                <i class="bi bi-heart" style="color: #01AAFF; cursor: pointer;"></i>
            </a>
            <?php endif; ?>

            <!-- Like Count Display -->
            <span id="like-count" style="margin-left: 10px; color: #01AAFF;"><?php echo $like_count; ?> Likes</span>

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