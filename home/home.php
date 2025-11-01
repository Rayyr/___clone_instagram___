<?php
session_start();

// الاتصال بقاعدة البيانات
$conn = mysqli_connect('localhost','root','','mini_instagram');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mini_instagram';

// Get current user data(current logged in user logically)
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$user_id = $_SESSION['user_id'];


//follower id : the user who is follow : المتابيع
//followee id : the user who is followed from other person : المتاباع
//follower_id = the person who is doing the following (current user)
//followee_id = the person being followed (the one you want to fetch)


//$followees = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ?");
//$followees->execute([$user_id]);
//$followees_result = $followees->fetch();

//get the followees'stories that the current logged-in user follows
$query=$pdo->prepare("SELECT u.*,s.*
                         FROM follows f 
                         JOIN users u ON f.followee_id = u.user_id
                         JOIN stories s ON u.user_id = s.user_id
                         WHERE f.follower_id = ?
                         AND s.expires_at > NOW()
                         ORDER BY s.created_at DESC ");
//AND s.expires_at > NOW() useless since i have created such an event to check every hour about expiration time for each story
//from nearer story to be ended to the newest order
$query->execute([$user_id]);
$followees_stories = $query->fetchAll(PDO::FETCH_ASSOC);

//current logged in user
$cu = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$cu->execute([$user_id]);
$cu_result = $cu->fetch();

// Get posts for feed (posts from users that current user follows)
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.profile_picture_url 
    FROM posts p 
    JOIN users u ON p.user_id = u.user_id 
    WHERE p.user_id IN (
        SELECT followee_id FROM follows WHERE follower_id = ?
    ) 
    ORDER BY p.created_at DESC
");
//descending order , the 1st comment is the newest
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Instagram</title>
</head>
<body>


<!-- Left Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img class="instagram-written-text-logo" src="../assets/login_register_page_logos/instagram_text.png" alt="Instagram logo">
    </div>
    <ul class="nav-menu">
        <li><a href="../home/home.php" ><i class="fa-solid fa-house" style="color: #000000;"></i> Home</a></li>
        <li><a href="search.php"><i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i> Search</a></li>
        <li><a href="explore.php"><svg aria-label="Explore" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><title>Explore</title><polygon fill="none" points="13.941 13.953 7.581 16.424 10.06 10.056 16.42 7.585 13.941 13.953" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon><polygon fill-rule="evenodd" points="10.06 10.056 13.949 13.945 7.581 16.424 10.06 10.056"></polygon><circle cx="12.001" cy="12.005" fill="none" r="10.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle></svg> Explore</a></li>
        <li><a href="reels.php"><svg aria-label="Reels" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><title>Reels</title><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="2.049" x2="21.95" y1="7.002" y2="7.002"></line><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="13.504" x2="16.362" y1="2.001" y2="7.002"></line><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="7.207" x2="10.002" y1="2.11" y2="7.002"></line><path d="M2 12.001v3.449c0 2.849.698 4.006 1.606 4.945.94.908 2.098 1.607 4.946 1.607h6.896c2.848 0 4.006-.699 4.946-1.607.908-.939 1.606-2.096 1.606-4.945V8.552c0-2.848-.698-4.006-1.606-4.945C19.454 2.699 18.296 2 15.448 2H8.552c-2.848 0-4.006.699-4.946 1.607C2.698 4.546 2 5.704 2 8.552Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M9.763 17.664a.908.908 0 0 1-.454-.787V11.63a.909.909 0 0 1 1.364-.788l4.545 2.624a.909.909 0 0 1 0 1.575l-4.545 2.624a.91.91 0 0 1-.91 0Z" fill-rule="evenodd"></path></svg> Reels</a></li>
        <li><a href="messages.php">✉️ Messages</a></li>
        <li><a href="notifications.php">🔔 Notifications</a></li>
        <li><button href="create.php">➕ Create</button></li>
    </ul>

    <div class="profile-section">
        <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>" class="profile-link">
            <img src="../profile_images/<?php echo htmlspecialchars($cu_result['profile_picture_url']); ?>"
                 alt="Profile" class="profile-pic" onerror="this.src='default.jpg'">
            <span>Profile</span>
        </a>
    </div>
</div>


<!-- Main Content -->
<div class="main-content">
    <!-- Stories Section -->
    <div class="stories">

        <!-- logged in user story ( to add story only ) -->
        <button   class="story story-handler-btn">
            <div class="story-image-container">
            <img src="../profile_images/<?php echo htmlspecialchars($cu_result['profile_picture_url']); ?>"
                 alt="Your story" class="story-pic">

                 <div class="add-story-overlay">
                    <i class="fa-solid fa-plus"></i>
                 </div>
            </div>
            <span class="story-username">Your story</span>
        </button>


<!--        Post Comments -->
<!--        <div class="post-comments">-->
<!--            <button class="openDialog" data-dialog="dialog--><?php //echo $post['post_id']; ?><!--">-->
<!--                View all --><?php //echo $post['comments_count']; ?><!-- comments-->
<!--            </button>-->
<!--        </div>-->



        <?php foreach ($followees_stories as $followee_story): ?>
            <button class="story story-handler-btn ">

                <div class="story-image-container">
                    <img src="../profile_images/<?php echo htmlspecialchars($followee_story['profile_picture_url']); ?>"
                         alt="Your story" class="story-pic">

                    <div class="add-story-overlay">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                </div>

                <span class="story-username"><?php echo $followee_story['username']?></span>
            </button>



        <?php endforeach; ?>

    </div>




    <!-- Posts Feed -->
    <div class="posts-feed">
        <?php if (empty($posts)): ?>
        <div style="text-align: center; padding: 40px; color: #8e8e8e;">
            <h3>No posts yet</h3>
            <p>Follow people to see their posts here!</p>
            <a href="explore.php" style="color: #0095f6; text-decoration: none;">Explore users</a>   <!-- later -->
        </div>

        <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post">
            <!-- Post Header -->
            <div class="post-header">
             <a href="../profile/profile.php?user_id=<?php echo $post['user_id']; ?>">
                 <img src="../profile_images/<?php echo htmlspecialchars($post['profile_picture_url']); ?>"
                     alt="<?php echo htmlspecialchars($post['username']); ?> profile image"
                        class="post-profile-pic" >
             </a>
                <a href="../profile/profile.php?user_id=<?php echo $post['user_id']; ?>"
                   class="post-username">
                    <?php echo htmlspecialchars($post['username']); ?>
                </a>
                <!-- Post Time -->
                <div class="post-time">
                    <?php
                    $time_ago = strtotime($post['created_at']);
                    echo time_elapsed_string($time_ago);
                    ?>
                </div>
                <button class="post-more">⋯</button>  <!-- later -->

            </div>


            <!-- Post Media Content -->
            <?php if($post['media_type'] == 'image'): ?>
                <img src="../posts_images/<?php echo htmlspecialchars($post['media_url']); ?>"
                     alt="Post image" class="post-image">
            <?php else: ?>
                <video src="../posts_videos/<?php echo htmlspecialchars($post['media_url']); ?>"
                       controls class="post-video">
                </video>
            <?php endif; ?>


<div class="wrapper-post-actions">
            <!-- post action Form -->
            <form  class="post-action-form" method="POST" action="../post_action_likes/post_action_likes.php">

                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">

                <!-- Post Actions -->
                            <div class="post-actions">

                                <!-- handling like form from current logged in user-->
                                <button type="submit" class="post-action" >
                                    <?php if (isLiked($post['post_id'], $user_id, $pdo)): ?>
                                         <i class="fa-solid fa-heart" style="color: #FF3040;"></i> <!-- Filled heart (already liked) -->
                                    <?php else: ?>
                                        <i class="fa-regular fa-heart" style="color: #000000;"></i>   <!-- Empty heart (not liked) -->
                                    <?php endif; ?>
                                </button>

                                <!-- handling comment btn to redirect to comment form downward-->
                                <button type="button" class="post-action" onclick="document.querySelector('.comment-input-<?php echo $post['post_id']; ?>').focus()">
                                    <i class="fa-regular fa-comment" style="color: #000000;"></i>
                                </button>



                            </div>
            </form>


    <!-- save Form -->
    <form  class="save-form" method="POST" action="../saved_posts/saved_posts.php">
        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">

        <button type="submit" class="post-save-action">
            <?php if (isSaved($post['post_id'], $user_id, $pdo)): ?>
                <i class="fa-solid fa-bookmark" style="color: #000000;"></i>
            <?php else: ?>
                <i class="fa-regular fa-bookmark" style="color: #000000;"></i>
            <?php endif; ?>
        </button>
    </form>


</div>





            <!-- Post Likes -->
            <div class="post-likes">
                <button class="likeOpenDialog" data-dialog="like-dialog<?php echo $post['post_id']; ?>">
                    <?php echo $post['likes_count']; ?> likes
                </button>
            </div>


            <dialog class="myLikeDialog" id="like-dialog<?php echo $post['post_id']; ?>">
                <h2>Likes</h2>

                <div class="likes-content">
                    <?php
                    // Fetch likes from database
                    $likes = getPostLikes($post['post_id'], $pdo);

                    if (empty($likes)): ?>
                        <p>No likes yet. Be the first to like!</p>
                    <?php else: ?>
                        <?php foreach ($likes as $like): ?>
                            <div class="like">
                                <div class="like-header">

                                    <!-- Make profile image clickable  for likers -->
                                    <a href="../profile/profile.php?user_id=<?php echo $like['user_id']; ?>">
                                        <img src="../profile_images/<?php echo ($like['profile_picture_url']); ?>">
                                    </a>

                                    <!-- Make username clickable -->
                                    <a href="../profile/profile.php?user_id=<?php echo $like['user_id']; ?>" class="username-link">
                                        <strong><?php echo ($like['username']); ?></strong>
                                    </a>
                                    <span><?php echo ($like['liked_at']); ?></span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button class="likeCloseDialog">Close</button>
            </dialog>








            <!-- Post Caption -->
            <div class="post-caption">
                            <span class="caption-username">
                                <?php echo htmlspecialchars($post['username']); ?>
                            </span>
                <?php echo htmlspecialchars($post['caption']); ?>
            </div>




            <!-- Post Comments -->
            <div class="post-comments">
                <button class="openDialog" data-dialog="dialog<?php echo $post['post_id']; ?>">
                    View all <?php echo $post['comments_count']; ?> comments
                </button>
            </div>


            <dialog class="myDialog" id="dialog<?php echo $post['post_id']; ?>">
                <h2>Comments</h2>

                <div class="comments-content">
                    <?php
                    // Fetch comments from database
                    $comments = getPostComments($post['post_id'], $pdo);

                    if (empty($comments)): ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-header">

                                    <!-- Make profile image clickable  for commentors -->
                                    <a href="../profile/profile.php?user_id=<?php echo $comment['user_id']; ?>">
                                        <img src="../profile_images/<?php echo ($comment['profile_picture_url']); ?>">
                                    </a>

                                    <!-- Make username clickable -->
                                    <a href="../profile/profile.php?user_id=<?php echo $comment['user_id']; ?>" class="username-link">
                                    <strong><?php echo ($comment['username']); ?></strong>
                                    </a>
                                    <span><?php echo ($comment['created_at']); ?></span>
                                </div>
                                <p class="comment-text"><?php echo ($comment['comment_content']); ?></p>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button class="closeDialog">Close</button>
            </dialog>



            <!-- Comment Form -->
            <form  class="comment-form" method="POST" action="../post_comments/post_comments.php">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <input  placeholder="Add a comment..." name="comment_content"
                       class="comment-input comment-input-<?php echo $post['post_id']; ?>" required>
                <button type="submit" class="post-button">Post</button>
            </form>


        </div>
            <?php endforeach; ?>
        <?php endif; ?>


        <!-- After all posts -->
        <div class="caught-up">

            <div class="symbol">
            <div class="circle">
                <div class="inner">
                    <div class="check"></div>
                </div>
            </div>
            </div>

            <h3>You're All Caught Up</h3>
            <p>You've seen all posts from your followers !</p><!--may later to add explore page to see other posts -->
        </div>




    </div>
</div>



<!-- Right Sidebar -->
<div class="right-sidebar">

    <!-- Suggestions -->
    <div class="suggestions-header">
        <div class="suggestions-title">Suggested for you</div>
        <a href="#" class="see-all">See All</a>
    </div>

    <!-- Suggested Users -->
    <div class="suggestion">
        <img src="../profile_images/default.jpg" alt="User" class="profile-pic">
        <div class="suggestion-info">
            <div class="suggestion-username">franktougan</div>
            <div class="suggestion-followers">Followed by fathalidad...</div>
        </div>
        <button class="follow-btn">Follow</button>
    </div>


    <div class="suggestion">
        <img src="../profile_images/default.jpg" alt="User" class="profile-pic">
        <div class="suggestion-info">
            <div class="suggestion-username">aya_rawajbeh</div>
            <div class="suggestion-followers">Followed by alia_s.sama...</div>
        </div>
        <button class="follow-btn">Follow</button>
    </div>

    <div class="suggestion">
        <img src="../profile_images/default.jpg" alt="User" class="profile-pic">
        <div class="suggestion-info">
            <div class="suggestion-username">gidx_mru</div>
            <div class="suggestion-followers">Followed by zhorouq_d...</div>
        </div>
        <button class="follow-btn">Follow</button>
    </div>



    <!-- Footer Links -->
    <div class="footer-links">
        <a href="#">About</a> • <a href="#">Help</a> • <a href="#">Press</a> • <a href="#">API</a> •
        <a href="#">Jobs</a> • <a href="#">Privacy</a> • <a href="#">Terms</a> • <a href="#">Locations</a> •
        <a href="#">Language</a> • <a href="#">Meta Verified</a>
    </div>
    <div class="copyright">© 2025 INSTAGRAM FROM META</div>
</div>





<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime("@$datetime");
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
    );

//    foreach ($string as $k => &$v) {
//        if ($diff->$k) {
//            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
//        } else {
//            unset($string[$k]);
//        }
//    }
    if ($diff->y > 0) {
        return $diff->y . 'y';
    }
    if ($diff->m > 0) {
        return $diff->m . 'm';
    }
    if ($diff->w > 0) {
        return $diff->w . 'w';
    }
    if ($diff->d > 0) {
        return $diff->d . 'd';
    }
    if ($diff->h > 0) {
        return $diff->h . 'h';
    }
    if ($diff->i > 0) {
        return $diff->i . 'm';
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>


<?php
function isLiked($post_id, $user_id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $result = $stmt->fetch();

       return $result['like_count'] > 0;

    } catch(PDOException $e) {
        error_log("Error checking like status: " . $e->getMessage());
        return false;
    }
}
?>


<?php
function isSaved($post_id, $user_id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as savings_count FROM saved_posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $result = $stmt->fetch();

        return $result['savings_count'] > 0;

    } catch(PDOException $e) {
        error_log("Error checking saving status : " . $e->getMessage());
        return false;
    }
}
?>


<?php
function getPostComments($post_id, $pdo) {
    $stmt = $pdo->prepare("
        SELECT pc.*, u.username ,u.profile_picture_url,u.user_id
        FROM post_comments pc 
        JOIN users u ON pc.user_id = u.user_id 
        WHERE pc.post_id = ? 
        ORDER BY pc.created_at ASC 
    ");
    //ascending order , the 1st comment is the oldest
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
function getPostLikes($post_id, $pdo) {
$stmt = $pdo->prepare("
SELECT pl.*, u.username ,u.profile_picture_url,u.user_id
FROM post_likes pl
JOIN users u ON pl.user_id = u.user_id
WHERE pl.post_id = ?
ORDER BY pl.liked_at ASC
");
//ascending order , the 1st like is the oldest
$stmt->execute([$post_id]);
return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<script src="logic.js"></script>
</body>
</html>