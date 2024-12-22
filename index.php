<?php include_once("header.php") ?>

<?php
// Fetch the posts
$query = "SELECT * FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>

    <section class="container hero">
        <h1>
            Unleash
        </h1>
        <p class="secondary">
            The Power of Knowledge - Code, Create, Conquer"
        </p>
    </section>

    <main class="container">

        <section class="grid">

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <article class="post">
                        <div class="centerImg">
                            <?php if (!empty($row['img'])): ?>
                                <img src="admin/<?php echo htmlspecialchars($row['img']); ?>" alt="Post Image">
                            <?php else: ?>
                                <img src="assets/placeholder.png" alt="Default Image">
                            <?php endif; ?>
                        </div>
                        <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p><?php echo htmlspecialchars(substr($row['post'], 0, 100)) . "..."; ?></p>
                        <a href="blog.php?id=<?php echo $row['post_id']; ?>">read more...</a>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>

        </section>

        <!-- <div class="btn">
            <button class="outline" id="load-more" data-offset="6">load more</button>
        </div> -->

    </main>

    <section class="container newsletter">

        <div class="grid">

            <div>
                <h6>Subscribe to Our Newsletter</h6>
                <form>
                    <fieldset role="group">
                        <input name="email" type="email" placeholder="Enter your email" autocomplete="email" />
                        <input type="submit" value="Subscribe" />
                    </fieldset>
                </form>
                <small>We respect your privacy. No spam, ever.</small>
            </div>

            <div class="social">
                <div class="media">
                    <a href="https://facebook.com" target="_blank" style="margin-right: 30px; "><i
                            class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com" target="_blank" style="margin-right: 30px;"><i
                            class="bi bi-twitter-x"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

        </div>

    </section>

<?php include_once("footer.php") ?>    