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

// Get current user data(current logged in user logically) from the session data
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;

//impossilple to accur since we will be in this page from home page !!!
if($user_id === -1) {
// If no user_id provided, redirect to login or show error
    header("Location: ../login/login.php");
    exit;
}

//$user_id = $_SESSION['user_id']; due to the issue of phpsession sharing at teh same browser


//follower id : the user who is follow : المتابيع
//followee id : the user who is followed from other person : المتاباع
//follower_id = the person who is doing the following (current user)
//followee_id = the person being followed (the one you want to fetch)


//$followees = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ?");
//$followees->execute([$user_id]);
//$followees_result = $followees->fetch();


//get the followees'stories that the current logged-in user follows
$query=$pdo->prepare("SELECT u.*,s.*,
                             CASE 
                                    WHEN EXISTS (
                                  SELECT * FROM story_views sv 
                                  WHERE sv.story_id = s.story_id AND sv.viewer_id = ?
                                  ) THEN 1 
                                    ELSE 0 
                                  END as is_viewed
                         FROM follows f 
                         JOIN users u ON f.followee_id = u.user_id
                         JOIN stories s ON u.user_id = s.user_id
                         WHERE f.follower_id = ?
                         AND s.expires_at > NOW()
                         ORDER BY s.created_at DESC ");
//AND s.expires_at > NOW() useless since i have created such an event to check every hour about expiration time for each story
//from nearer story to be ended to the newest order
$query->execute([$user_id, $user_id]);
$followees_stories = $query->fetchAll(PDO::FETCH_ASSOC);





/////////////////////////

//////////////////////////

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
    <link rel="stylesheet" href="create_post_btn_style.css">
    <link rel="stylesheet" href="../story/story_style.css">
    <link rel="stylesheet" href="../story/create_story_style.css">

    <title>Instagram &bull; Home</title>
</head>
<body>


<!-- Left Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img class="instagram-written-text-logo" src="../assets/login_register_page_logos/instagram_text.png" alt="Instagram logo">
    </div>
    <ul class="nav-menu">
        <li><a href="../home/home.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=-1" ><i class="fa-solid fa-house" style="color: #000000;"></i> Home</a></li><!-- modofied 9-11 -->
        <li><a href="search.php"><i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i> Search</a></li>
        <li><a href="../explore_users/explore_users.php?user_id=<?php echo $user_id?>"><i class="fa-solid fa-users" style="color: #000000;"></i> Explore</a></li>
        <li><a href="../profile/reels.php"><svg aria-label="Reels" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><title>Reels</title><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="2.049" x2="21.95" y1="7.002" y2="7.002"></line><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="13.504" x2="16.362" y1="2.001" y2="7.002"></line><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="7.207" x2="10.002" y1="2.11" y2="7.002"></line><path d="M2 12.001v3.449c0 2.849.698 4.006 1.606 4.945.94.908 2.098 1.607 4.946 1.607h6.896c2.848 0 4.006-.699 4.946-1.607.908-.939 1.606-2.096 1.606-4.945V8.552c0-2.848-.698-4.006-1.606-4.945C19.454 2.699 18.296 2 15.448 2H8.552c-2.848 0-4.006.699-4.946 1.607C2.698 4.546 2 5.704 2 8.552Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M9.763 17.664a.908.908 0 0 1-.454-.787V11.63a.909.909 0 0 1 1.364-.788l4.545 2.624a.909.909 0 0 1 0 1.575l-4.545 2.624a.91.91 0 0 1-.91 0Z" fill-rule="evenodd"></path></svg> Reels</a></li>
<!--        <li><a href="../story/user_stories.php">✉️ Stories</a></li>-->
        <li><a href="messages.php">✉️ Messages</a></li>
        <li><a href="notifications.php">🔔 Notifications</a></li>
        <li>
            <a href="#" id="openCreateDialog">
                <i class="fa-solid fa-plus fa-xl" style="color: #000000;"></i>
                <span>Create</span>
            </a>
        </li>
    </ul>


    <!-- ✅ Create Post Dialog -->
    <dialog id="createDialog" class="create-dialog">
        <form method="POST" class="dialog-box create-post-form" action="../create_post/create_post.php?user_id=<?php echo $user_id; ?>"  enctype="multipart/form-data" id="createPostForm"><!--modofied 9-11-->
            <div class="dialog-header">
                <h3>Create new post</h3>
                <button type="button" class="close-btn">&times;</button>
            </div>

            <div class="upload-area" id="uploadArea">

                <svg aria-label="Icon" class="x1lliihq x1n2onr6 x5n08af"   height="77" role="img" viewBox="0 0 97.6 77.3" width="96"><title>Icon to represent media such as images or videos</title><path d="M16.3 24h.3c2.8-.2 4.9-2.6 4.8-5.4-.2-2.8-2.6-4.9-5.4-4.8s-4.9 2.6-4.8 5.4c.1 2.7 2.4 4.8 5.1 4.8zm-2.4-7.2c.5-.6 1.3-1 2.1-1h.2c1.7 0 3.1 1.4 3.1 3.1 0 1.7-1.4 3.1-3.1 3.1-1.7 0-3.1-1.4-3.1-3.1 0-.8.3-1.5.8-2.1z" fill="currentColor"></path><path d="M84.7 18.4 58 16.9l-.2-3c-.3-5.7-5.2-10.1-11-9.8L12.9 6c-5.7.3-10.1 5.3-9.8 11L5 51v.8c.7 5.2 5.1 9.1 10.3 9.1h.6l21.7-1.2v.6c-.3 5.7 4 10.7 9.8 11l34 2h.6c5.5 0 10.1-4.3 10.4-9.8l2-34c.4-5.8-4-10.7-9.7-11.1zM7.2 10.8C8.7 9.1 10.8 8.1 13 8l34-1.9c4.6-.3 8.6 3.3 8.9 7.9l.2 2.8-5.3-.3c-5.7-.3-10.7 4-11 9.8l-.6 9.5-9.5 10.7c-.2.3-.6.4-1 .5-.4 0-.7-.1-1-.4l-7.8-7c-1.4-1.3-3.5-1.1-4.8.3L7 49 5.2 17c-.2-2.3.6-4.5 2-6.2zm8.7 48c-4.3.2-8.1-2.8-8.8-7.1l9.4-10.5c.2-.3.6-.4 1-.5.4 0 .7.1 1 .4l7.8 7c.7.6 1.6.9 2.5.9.9 0 1.7-.5 2.3-1.1l7.8-8.8-1.1 18.6-21.9 1.1zm76.5-29.5-2 34c-.3 4.6-4.3 8.2-8.9 7.9l-34-2c-4.6-.3-8.2-4.3-7.9-8.9l2-34c.3-4.4 3.9-7.9 8.4-7.9h.5l34 2c4.7.3 8.2 4.3 7.9 8.9z" fill="currentColor"></path><path d="M78.2 41.6 61.3 30.5c-2.1-1.4-4.9-.8-6.2 1.3-.4.7-.7 1.4-.7 2.2l-1.2 20.1c-.1 2.5 1.7 4.6 4.2 4.8h.3c.7 0 1.4-.2 2-.5l18-9c2.2-1.1 3.1-3.8 2-6-.4-.7-.9-1.3-1.5-1.8zm-1.4 6-18 9c-.4.2-.8.3-1.3.3-.4 0-.9-.2-1.2-.4-.7-.5-1.2-1.3-1.1-2.2l1.2-20.1c.1-.9.6-1.7 1.4-2.1.8-.4 1.7-.3 2.5.1L77 43.3c1.2.8 1.5 2.3.7 3.4-.2.4-.5.7-.9.9z" fill="currentColor"></path></svg>

                <p>Drag photos and videos here</p>
                <label for="fileInput" class="upload-btn">Select From Computer</label>
                <!-- Placeholder for displaying selected file name -->
                <p id="fileName" class="file-name">No file chosen</p>
                <input class="upload-btn" type="file" id="fileInput" name="media" accept="image/*,video/*" hidden >
                <input class="caption" type="text" id="captionInput" name="caption" placeholder="Post Caption (optional)">

            </div>

            <!-- Added action buttons -->
            <div class="dialog-actions" id="dialogActions"  style="display: none" >
                <button type="submit" class="share-btn" >Share Post</button>
            </div>
        </form>
    </dialog>





    <div class="profile-section">

       <!--profile-->
        <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=-1" class="profile-link">  <!-- modified 9-11 -->
            <img src="../profile_images/<?php echo htmlspecialchars($cu_result['profile_picture_url']); ?>"
                 alt="Profile" class="profile-pic"">
            <span>Profile</span>
        </a>


        <!--logout-->
        <div style="margin-top: 7px;margin-left: 3px;">
            <a href="../login/login.php" class="profile-link">  <!-- modified 9-11 -->
                <i class="fas fa-sign-out-alt fa-lg" title="logout" style="color: #000000;"></i>
                <span>Logout</span>
            </a>
        </div>

    </div>
</div>


<!-- Main Content -->
<div class="main-content">
    <!-- Stories Section -->
    <div class="stories">

        <!-- logged in user story ( to add story only ) -->
        <button class="current-user-story story story-handler-btn">
            <div class="story-image-container">
            <img src="../profile_images/<?php echo htmlspecialchars($cu_result['profile_picture_url']); ?>"
                 alt="Your story" class="story-pic">

                 <div class="add-story-overlay">
                    <i class="fa-solid fa-plus"></i>
                 </div>
            </div>
            <span class="current-user-story story-username">Your story</span>
        </button>




        <?php foreach ($followees_stories as $followee_story): ?>

            <?php if($followee_story['is_viewed'] == 0): ?> <!--not viewed yet-->
                <button class="story story-handler-btn"
                    data-story-id="<?php echo $followee_story['story_id']; ?>"
                    data-user-id="<?php echo $followee_story['user_id']; ?>"
                        data-viewer-id="<?php echo $user_id; ?>"
                    data-username="<?php echo htmlspecialchars($followee_story['username']); ?>"
                    data-profile-pic="../profile_images/<?php echo htmlspecialchars($followee_story['profile_picture_url']); ?>"
                    data-story-media="../story_images/<?php echo htmlspecialchars($followee_story['media_url']); ?>">

                <div class="story-image-container">
                    <img src="../profile_images/<?php echo htmlspecialchars($followee_story['profile_picture_url']); ?>"
                         alt="Your story" class="story-pic">
                </div>

                    <span class="story-username" data-user-id="<?php echo $followee_story['user_id']; ?>" data-viewer-id="<?php echo $user_id; ?>">
                        <?php echo $followee_story['username']?>
                    </span>



                </button>


        <?php endif; ?>
        <?php endforeach; ?>


    </div>



    <!-- ✅ Create Story Dialog -->
    <dialog id="createStoryDialog" class="create-story-dialog">
        <form method="POST" class="dialog-story-box create-story-form" action="../story/create_story.php?user_id=<?php echo $user_id; ?>" enctype="multipart/form-data" id="createStoryForm">
            <div class="dialog-story-header">
                <h3>Create new story</h3>
                <button type="button" class="close-story-btn">&times;</button>
            </div>

            <div class="upload-story-area" id="uploadStoryArea">

                <svg aria-label="Icon" class="x1lliihq x1n2onr6 x5n08af" height="77" role="img" viewBox="0 0 97.6 77.3" width="96"><title>Icon to represent media such as images or videos</title><path d="M16.3 24h.3c2.8-.2 4.9-2.6 4.8-5.4-.2-2.8-2.6-4.9-5.4-4.8s-4.9 2.6-4.8 5.4c.1 2.7 2.4 4.8 5.1 4.8zm-2.4-7.2c.5-.6 1.3-1 2.1-1h.2c1.7 0 3.1 1.4 3.1 3.1 0 1.7-1.4 3.1-3.1 3.1-1.7 0-3.1-1.4-3.1-3.1 0-.8.3-1.5.8-2.1z" fill="currentColor"></path><path d="M84.7 18.4 58 16.9l-.2-3c-.3-5.7-5.2-10.1-11-9.8L12.9 6c-5.7.3-10.1 5.3-9.8 11L5 51v.8c.7 5.2 5.1 9.1 10.3 9.1h.6l21.7-1.2v.6c-.3 5.7 4 10.7 9.8 11l34 2h.6c5.5 0 10.1-4.3 10.4-9.8l2-34c.4-5.8-4-10.7-9.7-11.1zM7.2 10.8C8.7 9.1 10.8 8.1 13 8l34-1.9c4.6-.3 8.6 3.3 8.9 7.9l.2 2.8-5.3-.3c-5.7-.3-10.7 4-11 9.8l-.6 9.5-9.5 10.7c-.2.3-.6.4-1 .5-.4 0-.7-.1-1-.4l-7.8-7c-1.4-1.3-3.5-1.1-4.8.3L7 49 5.2 17c-.2-2.3.6-4.5 2-6.2zm8.7 48c-4.3.2-8.1-2.8-8.8-7.1l9.4-10.5c.2-.3.6-.4 1-.5.4 0 .7.1 1 .4l7.8 7c.7.6 1.6.9 2.5.9.9 0 1.7-.5 2.3-1.1l7.8-8.8-1.1 18.6-21.9 1.1zm76.5-29.5-2 34c-.3 4.6-4.3 8.2-8.9 7.9l-34-2c-4.6-.3-8.2-4.3-7.9-8.9l2-34c.3-4.4 3.9-7.9 8.4-7.9h.5l34 2c4.7.3 8.2 4.3 7.9 8.9z" fill="currentColor"></path><path d="M78.2 41.6 61.3 30.5c-2.1-1.4-4.9-.8-6.2 1.3-.4.7-.7 1.4-.7 2.2l-1.2 20.1c-.1 2.5 1.7 4.6 4.2 4.8h.3c.7 0 1.4-.2 2-.5l18-9c2.2-1.1 3.1-3.8 2-6-.4-.7-.9-1.3-1.5-1.8zm-1.4 6-18 9c-.4.2-.8.3-1.3.3-.4 0-.9-.2-1.2-.4-.7-.5-1.2-1.3-1.1-2.2l1.2-20.1c.1-.9.6-1.7 1.4-2.1.8-.4 1.7-.3 2.5.1L77 43.3c1.2.8 1.5 2.3.7 3.4-.2.4-.5.7-.9.9z" fill="currentColor"></path></svg>

                <p>Drag photo here</p>
                <label for="fileStoryInput" class="upload-story-btn">Select From Computer</label>
                <!-- Placeholder for displaying selected file name -->
                <p id="fileStoryName" class="file-story-name">No image chosen</p>
                <input class="upload-story-btn" type="file" id="fileStoryInput" name="media" accept="image/*" hidden >
            </div>

            <!-- Added action buttons -->
            <div class="dialog-actions" id="storyDialogActions"  style="display: none" >
                <button type="submit" class="share-story-btn" >Share Story</button>
            </div>
        </form>
    </dialog>



<!--     Story Viewer Dialog (Single Instance) -->
    <dialog id="storyDialog" class="story-dialog">
        <div class="story-dialog-content">
            Header
            <div class="story-header">
                <div class="story-user-info">
                    <img id="storyUserAvatar" src="" alt="" class="story-avatar">
                    <span id="storyUsername" class="story-username-header"></span>
                </div>
                <button class="close-story-btn" id="closeStoryBtn">&times;</button>
            </div>

<!--              Story Content -->
            <div class="story-media-container">
                <img id="storyMedia" src=""  alt="Story" class="story-media">
            </div>


<!--              Progress Bar -->
            <div class="story-progress-container">
                <div class="story-progress-bar">
                    <div class="story-progress-fill"></div>
                </div>
            </div>
        </div>
    </dialog>









    <!-- Posts Feed -->
    <div class="posts-feed">
        <?php if (empty($posts)): ?>
        <div style="text-align: center; padding: 40px; color: #8e8e8e;">
            <h3>No posts yet</h3>
            <p>Follow people to see their posts here!</p>
            <a href="../explore_users/explore_users.php?user_id=<?php echo $user_id?>" style="color: #0095f6; text-decoration: none;">Explore users</a>   <!-- later -->
        </div>

        <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post">
            <!-- Post Header -->
            <div class="post-header">
             <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $post['user_id']; ?>">
                 <img src="../profile_images/<?php echo htmlspecialchars($post['profile_picture_url']); ?>"
                     alt="<?php echo htmlspecialchars($post['username']); ?> profile image"
                        class="post-profile-pic" >
             </a> <!-- modified 9-11-->
                <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $post['user_id']; ?>"
                   class="post-username">
                    <?php echo htmlspecialchars($post['username']); ?>
                </a><!-- modified 9-11-->
                <!-- Post Time -->
                <div class="post-time">
                    <?php
//                    $time_ago = strtotime($post['created_at']);
                    echo time_elapsed_string($post['created_at']);
                    ?>
                </div>
<!--                <button class="post-more">⋯</button>-->
                <!-- later -->

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
            <form  class="post-action-form" method="POST" action="../post_action_likes/post_action_likes.php?user_id=<?php echo $user_id; ?>">

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
    <form  class="save-form" method="POST" action="../saved_posts/saved_posts.php?user_id=<?php echo $user_id?>">
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
                                    <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $like['user_id']; ?>">    <!-- modified 9-11 -->
                                        <img src="../profile_images/<?php echo ($like['profile_picture_url']); ?>">
                                    </a>

                                    <!-- Make username clickable -->
                                    <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $like['user_id']; ?>" class="username-link"><!-- modified 9-11 -->
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
                            <span class="caption-username" onclick='window.location.href = `../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $post['user_id']; ?>`'>
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
                                    <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $comment['user_id']; ?>"><!-- modified 9-11 -->
                                        <img src="../profile_images/<?php echo ($comment['profile_picture_url']); ?>">
                                    </a>

                                    <!-- Make username clickable -->
                                    <a href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $comment['user_id']; ?>" class="username-link"><!-- modified 9-11 -->
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
            <form  class="comment-form" method="POST" action="../post_comments/post_comments.php?user_id=<?php echo $user_id; ?>;">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <input  placeholder="Add a comment..." name="comment_content"
                       class="comment-input comment-input-<?php echo $post['post_id']; ?>" required>
                <button type="submit" class="post-button">Post</button>
            </form>


        </div>
            <?php endforeach; ?>
        <?php endif; ?>




        <?php if (!empty($posts)): ?>
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
        <?php endif; ?>




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
function time_elapsed_string($datetime)
{
    // Use your DB timezone explicitly
    $timezone = new DateTimeZone('+02:00');

    // Parse the database datetime in that timezone
    $ago = new DateTime($datetime, $timezone);
    $now = new DateTime('now', $timezone);

    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'm ago';
    if ($diff->w > 0) return $diff->w . 'w ago';
    if ($diff->d > 0) return $diff->d . 'd ago';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'm ago';
    return 'just now';
}

?>


<?php
function isLiked($post_id, $user_id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $result = $stmt->fetch();

       return $result['like_count'] > 0;//actually either 1 / 0 likes !

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



<?php
function changeStoryToViewed($pdo, $story_id, $user_id) {

        $query = $pdo->prepare("INSERT INTO story_views (story_id, viewer_id, viewed_at) 
                                VALUES (?, ?, NOW()) ");
        $query->execute([$story_id, $user_id]);
}
?>



<script src="logic.js"></script>
<script src="../story/story_dialog_logic.js"></script>
</body>
</html>