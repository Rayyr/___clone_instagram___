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
?>



<?php

// Get user_id from URL parameter
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;

$user_we_will_visit_id = isset($_GET['user_we_will_visit']) ? intval($_GET['user_we_will_visit']) : -1;
//$user_we_will_visit_id =$_GET['user_we_will_visit'];
$temp_user_id=$user_id;

//if user_we_will_visit=-1 then it is the original user
if($user_we_will_visit_id != -1) {//that means if user_we_will_visit=-1 the original user(logged-in user) go to his profile ( logically)
  $temp_user_id=$user_we_will_visit_id;
}


//impossilple to accur since we will be in this page from home page !!!
if($user_id === -1) {
// If no user_id provided, redirect to login or show error
header("Location: ../login/login.php");
exit;
}


//echo '<script>window.alert("' . $user_we_will_visit_id . '");</script>';

// Fetch user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([ $temp_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//get users's posts
//Now your posts will display with the newest posts first in the grid!
$stmt = $pdo->prepare("SELECT * FROM posts WHERE  user_id = ? ORDER BY created_at DESC");
$stmt->execute([ $temp_user_id]);
$user_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get users's saved-posts
//Now your saved-posts will display with the newest posts first in the grid!
$stmt = $pdo->prepare("SELECT sp.*,p.*,u.username,u.profile_picture_url
    FROM saved_posts sp 
    JOIN posts p ON sp.post_id = p.post_id 
    JOIN users u ON p.user_id = u.user_id 
    WHERE sp.user_id = ? 
    ORDER BY sp.saved_at DESC");
$stmt->execute([$temp_user_id]);
$user_saved_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);


$followers_list=getFollowersList($user['user_id'],$pdo);
$followings_list=getFollowingsList($user['user_id'],$pdo);

?>


<?php
//// Check if post_id is set in URL to show modal
$show_modal = isset($_GET['post_id']);
$modal_post_id = isset($_GET['post_id']) ? $_GET['post_id'] : 0;

// If modal should be shown, fetch the post data
if ($show_modal && $modal_post_id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$modal_post_id]);
    $modal_post = $stmt->fetch(PDO::FETCH_ASSOC);

    $post_comments=getPostComments($modal_post_id,$pdo);
    $post_likes=getPostLikes($modal_post_id,$pdo);
    $post_savings=getPostSavings($modal_post_id,$pdo);

}


function getPostSavings($post_id, $pdo) {
    $stmt = $pdo->prepare("
SELECT ps.*, u.username ,u.profile_picture_url,u.user_id
FROM saved_posts ps
JOIN users u ON ps.user_id = u.user_id
WHERE ps.post_id = ?
ORDER BY ps.saved_at ASC
");
//ascending order , the 1st saving is the oldest
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
function getPostComments($post_id,$pdo){

    //i use naming alis to avoid duplications colums in different tables
    $stmt=$pdo->prepare("SELECT 
        pc.post_comment_id,
        pc.post_id,
        pc.user_id,
        pc.comment_content,
        pc.created_at AS comment_created_at, 
        
        u.user_id  ,
        u.username  , 
        u.email  ,
        u.created_at AS user_created_at,
        u.profile_picture_url

FROM  post_comments pc 
JOIN users u ON pc.user_id=u.user_id 
WHERE pc.post_id = ? 
ORDER BY pc.created_at ASC");

    $stmt->execute([$post_id]);
    $post_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $post_comments;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram &bull; Profile</title>
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="profile_style.css">
    <link rel="stylesheet" href="posts_style.css">
    <link rel="stylesheet" href="post_modal_style.css">
    <link rel="stylesheet" href="comment_list_style.css">
    <link rel="stylesheet" href="likes_list_style.css">
    <link rel="stylesheet" href="savings_list_style.css">
    <link rel="stylesheet" href="followers_list_style.css">
    <link rel="stylesheet" href="followings_list_style.css">

    <link rel="stylesheet" href="saved_posts_style.css">

</head>
<body>

 <!-- Header/Navigation -->
<header>
    <div class="nav-container">
        <div class="logo">
            <img src="../assets/login_register_page_logos/instagram_text.png" alt="instagram logo">
        </div>

        <div class="nav-icons">
            <i class="fas fa-home"></i>
        </div>
    </div>
</header>

<div class="container">
    <!-- Profile Header -->
    <section class="profile-header">
        <div class="profile-pic">
            <?php if($user['profile_picture_url'] != ""): ?>
                <img src="../profile_images/<?php echo $user['profile_picture_url']; ?>" alt="Profile Picture" width="100">
            <?php else: ?>
                <i class="far fa-user"></i>
            <?php endif; ?>
        </div>
        <div class="profile-info">

            <h1 class="profile-name"><?php echo $user['username']; ?></h1>
            <div class="profile-stats">
                <div><strong><?php echo getPostsCount($user['user_id'],$pdo)?></strong> posts</div>
                <div class="followerOpenDialog"><strong><?php echo getFollowersCount($user['user_id'],$pdo)?></strong> followers</div>
                <div class="followingOpenDialog"><strong><?php echo getFollowingsCount($user['user_id'],$pdo)?></strong> following</div>
            </div>
            <p class="profile-bio"><?php echo getBio($user) ?></p>
            <div class="profile-actions" id="editProfile">
                <button onclick="window.location.href='../edit_profile/edit_profile.php?user_id=<?php echo $user_id; ?>'" class="btn btn-primary">Edit Profile</button>
                <button onclick="window.location.href='../story/archive_stories.php?user_id=<?php echo $user_id; ?>'" class="btn btn-primary">View Archive</button>
            </div>


        </div>
    </section>




    <dialog class="myFollowingsDialog" >
        <h2>Followings</h2>
        <div class="followings-content">
            <?php
            if (empty($followings_list)): ?>
                <p>No followings yet!</p>
            <?php else: ?>
                <?php foreach ($followings_list as $following): ?>
                    <div class="following">
                        <div class="following-header">

                            <!-- Make profile image clickable  for savers -->
                            <a href="../profile/profile.php?user_id=<?php echo $user_id ;?>&user_we_will_visit=<?php echo $following['user_id']; ?>" >
                                <img src="../profile_images/<?php echo ($following['profile_picture_url']); ?>">
                            </a>

                            <!-- Make username clickable -->
                            <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $following['user_id']; ?>" class="username-link" style="text-decoration: none">
                                <strong><?php echo ($following['username']); ?></strong>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="followingCloseDialog">Close</button>
    </dialog>





    <dialog class="myFollowersDialog" >
        <h2>Followers</h2>
        <div class="followers-content">
            <?php
            if (empty($followers_list)): ?>
                <p>No followers yet!</p>
            <?php else: ?>
                <?php foreach ($followers_list as $follower): ?>
                    <div class="follower">
                        <div class="follower-header">

                            <!-- Make profile image clickable  for savers -->
                            <a href="../profile/profile.php?user_id=<?php echo $user_id ;?>&user_we_will_visit=<?php echo $follower['user_id']; ?>" >
                                <img src="../profile_images/<?php echo ($follower['profile_picture_url']); ?>">
                            </a>

                            <!-- Make username clickable -->
                            <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $follower['user_id']; ?>" class="username-link" style="text-decoration: none">
                                <strong><?php echo ($follower['username']); ?></strong>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="followerCloseDialog">Close</button>
    </dialog>





    <!-- Tabs -->
    <section class="tabs">
        <div class="tab active" data-tab="posts">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" class="x14rh7hd"><title>Posts</title>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M3 3H21V21H3z"></path>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M9.01486 3 9.01486 21"></path>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M14.98514 3 14.98514 21"></path>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M21 9.01486 3 9.01486"></path>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M21 14.98514 3 14.98514"></path>
            </svg>
        </div>
        <div class="tab" data-tab="saved">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" class="x14rh7hd" ><title>Saved</title>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2px" d="M20 21 12 13.44 4 21 4 3 20 3 20 21z"></path>
            </svg>
        </div>
    </section>



    <!-- Posts Content - Always loaded but hidden/shown with CSS , default will be selected  -->
    <div class="tab-content posts-tab active">
        <?php if (empty($user_posts)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-camera" style="color: #000000;"></i></div>
                <div class="empty-title">Share Photos</div>
                <div class="empty-subtitle">When you share photos, they will appear on your profile.</div>
                <button class="share-btn">Share your first photo</button><!--later ////////////////////////////////////////////////-->
            </div>



        <?php else: ?>
            <div class="posts-grid">
                <?php foreach ($user_posts as $post): ?>
                    <div class="post-item" data-post-id="<?php echo $post['post_id']; ?>" data-media-type="<?php echo $post['media_type'];?>" data-user-id="<?php echo $temp_user_id; ?>"><!--modified 9-11-->
                        <?php if ($post['media_type'] === 'image'): ?>
                            <img src="../posts_images/<?php echo htmlspecialchars($post['media_url']); ?>" alt="Post image">
                        <?php else: ?><!--video-->
                            <video >
                                <source src="../posts_videos/<?php echo htmlspecialchars($post['media_url']); ?>" type="video/mp4">
                            </video>
                        <div class="video-icon">
                            <i class="fa-solid fa-play custom-icon"  ></i>
                        </div>
                        <?php endif; ?>
                        <div class="post-overlay">
                            <div class="post-stats">
                                <div class="post-stat"><i class="fa-solid fa-heart" style="color: #ffffff;"></i> <?php echo $post['likes_count']; ?></div>
                                <div class="post-stat"><i class="fa-solid fa-comment" style="color: #ffffff;"></i> <?php echo $post['comments_count']; ?></div>
                                <div class="post-stat"><i class="fa-solid fa-bookmark" style="color: #ffffff;"></i> <?php echo $post['savings_count']; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>




    <!-- Post Detail Modal -->
        <?php if ($show_modal && $modal_post): ?>
    <div class="post-modal" id="postModal" data-user-id="<?php echo $temp_user_id ;?>"> <!-- modified 9-11 -->
        <div class="modal-content">
            <div class="modal-close" data-user-id="<?php echo $temp_user_id ;?>"><!-- modified 9-11 -->
                <i class="fa-solid fa-xmark"></i>
            </div>

            <div class="post-detail">
                <!-- Left side - Media -->
                <div class="post-media">
                    <?php if ($modal_post['media_type'] === 'image'): ?>
                    <img id="modalImage" src="../posts_images/<?php echo $modal_post['media_url']; ?>" alt="Post image">
                    <?php else: ?>  <!--video-->
                    <video id="modalVideo" controls>
                        <source src="../posts_videos/<?php echo $modal_post['media_url']; ?>" type="video/mp4">
                    </video>
                    <?php endif; ?>
                </div>

                <!-- Right side - Content -->
                <div class="post-info">
                    <!-- Header -->
                    <div class="post-header">
                        <div class="user-info">
                            <div class="avatar">
                                <img class="profile-Image" src="../profile_images/<?php echo $user['profile_picture_url']; ?>" alt="Profile image">
                            </div>
                            <div class="username" id="modalUsername"><?php echo $user['username']; ?></div>
                        </div>
<!--                        <div class="post-options">-->
<!--                            <i class="fa-solid fa-ellipsis"></i>-->
<!--                        </div>-->
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section">
                        <!-- users's info + caption -->
                         <div class="caption">
                            <div class="user-info">
                                <div class="avatar">
                                    <img class="profile-Image" src="../profile_images/<?php echo $user['profile_picture_url']; ?>" alt="Profile image">
                                </div>
                                <strong class="username" id="modalUsername"><?php echo $user['username']; ?></strong>
                                <div class="caption-text" id="modalCaption"><?php echo $modal_post['caption']?></div>
                            </div>
                         </div>



                        <!-- View translation -->
                        <div class="translation-link" id="translationLink" >
                            <span id="translate" data-caption="<?php echo $modal_post['caption']?>">See Translation</span>
                            <span id="translated-caption" style="display: none"></span>
                        </div>


                        <!-- Comments list -->
                        <div class="comments-list" id="commentsList">
                            <!-- Comments will be loaded dynamically from database -->
                            <?php if (empty($post_comments)): ?>
                            <div class="no-comment-post" id="noCommentPost"> No Comments yet !</div>
                             <?php else: ?>
                                <?php foreach ($post_comments as $post_comment): ?>
                            <div class="comment">
                                <div class="user-info">
                                    <div class="avatar" onclick="window.location.href='../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $post_comment['user_id']; ?>'"><!--modified 9-11-->
                                        <img class="profile-Image" src="../profile_images/<?php echo $post_comment['profile_picture_url'];?>" alt="Profile image">
                                    </div>

                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <strong class="username" onclick="window.location.href='../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $post_comment['user_id']; ?>'"><?php echo $post_comment['username'];?></strong><!--modified 9-11-->
                                            <div>  <span class="comment-text"><?php echo $post_comment['comment_content'];?></span></div>
                                        </div>
                                        <div class="comment-actions">
                                            <span class="comment-time"><?php echo time_elapsed_string($post_comment['comment_created_at']);?></span>
                                            <span class="reply-btn">Reply</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>


                    </div>

                    <!-- Engagement Stats -->
                    <div class="engagement-stats">
                        <div class="actions">
                            <div class="action-buttons">
<!--                                <button class="action-btn"><i class="fa-regular fa-heart"></i></button>-->
<!--                                <button class="action-btn"><i class="fa-regular fa-comment"></i></button>-->
<!--                                <button class="action-btn"><i class="fa-regular fa-paper-plane"></i></button>-->
                            </div>
<!--                            <button class="action-btn"><i class="fa-regular fa-bookmark"></i></button>-->
                        </div>




                        <!-- Post Likes -->
                        <div class="post-likes">
                            <button class="likeOpenDialog" data-dialog="like-dialog<?php echo $modal_post['post_id']; ?>">
                                <?php echo $modal_post['likes_count']; ?> likes
                            </button>
                        </div>



                        <dialog class="myLikeDialog" id="like-dialog<?php echo $modal_post['post_id']; ?>">
                            <h2>Likes</h2>

                            <div class="likes-content">
                                <?php
                                // Fetch likes from database


                                if (empty($post_likes)): ?>
                                    <p>No likes yet. Be the first to like!</p>
                                <?php else: ?>
                                    <?php foreach ($post_likes as $like): ?>
                                        <div class="like">
                                            <div class="like-header">

                                                <!-- Make profile image clickable  for likers -->
                                                <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $like['user_id']; ?>" >
                                                    <img src="../profile_images/<?php echo ($like['profile_picture_url']); ?>">
                                                </a>

                                                <!-- Make username clickable -->
                                                <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $like['user_id']; ?>" style="text-decoration: none">
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




                        <!-- Post savings -->
                        <div class="post-savings">
                            <button class="saveOpenDialog" data-dialog="save-dialog<?php echo $modal_post['post_id']; ?>">
                                <?php echo $modal_post['savings_count']; ?> <i class="fa-regular fa-bookmark"></i>
                            </button>
                        </div>


                        <dialog class="mySaveDialog" id="save-dialog<?php echo $modal_post['post_id']; ?>">
                            <h2>Savings</h2>

                            <div class="savings-content">
                                <?php
                                // Fetch savings from database


                                if (empty($post_savings)): ?>
                                    <p>No savings yet!</p>
                                <?php else: ?>
                                    <?php foreach ($post_savings as $saving): ?>
                                        <div class="saving">
                                            <div class="saving-header">

                                                <!-- Make profile image clickable  for savers -->
                                                <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $saving['user_id']; ?>" >
                                                    <img src="../profile_images/<?php echo ($saving['profile_picture_url']); ?>">
                                                </a>

                                                <!-- Make username clickable -->
                                                <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $saving['user_id']; ?>" class="username-link" style="text-decoration: none">
                                                    <strong><?php echo ($saving['username']); ?></strong>
                                                </a>
                                                <span><?php echo ($saving['saved_at']); ?></span>
                                            </div>

                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <button class="saveCloseDialog">Close</button>
                        </dialog>






                        <!-- post time -->
                        <div class="post-time-container">
                            <div class="post-time" id="modalTime"><?php echo time_elapsed_string($modal_post['created_at']);?></div>
                        </div>
                    </div>

                    <!-- Add Comment -->
<!--                    <div class="add-comment">-->
<!--                        <input type="text" placeholder="Add a comment..." class="comment-input">-->
<!--                        <button class="post-btn">Post</button>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
        <?php endif; ?>




    <!-- Saved Posts Content - Always loaded but hidden/shown with CSS -->
    <div class="tab-content saved-tab">
        <button class="share-btn" style="display: none;">Create your first reel</button>

        <?php if (empty($user_saved_posts)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="190" height="190">
                        <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,23c-6.065,0-11-4.935-11-11S5.935,1,12,1s11,4.935,11,11-4.935,11-11,11Zm2.5-17h-5c-1.379,0-2.5,1.122-2.5,2.5v8.181c0,.535.319,1.013.813,1.218.5.206,1.051.099,1.437-.286l2.75-2.75,2.75,2.75c.256.256.584.39.924.39.171,0,.345-.034.513-.104.494-.205.813-.683.813-1.218v-8.181c0-1.378-1.121-2.5-2.5-2.5Zm1.5,10.681c0,.191-.138.27-.196.294-.058.023-.211.066-.347-.069l-3.457-3.457-3.457,3.457c-.136.135-.289.093-.347.069-.059-.024-.196-.103-.196-.294v-8.181c0-.827.673-1.5,1.5-1.5h5c.827,0,1.5.673,1.5,1.5v8.181Z"/>
                    </svg>
                </div>
                <div class="empty-title" style="font-weight:bold; color: #000000">Nothing saved Yet</div>
                <div class="empty-subtitle" style="font-size: medium">All the posts and items you've saved will show up here.</div>
                <button class="share-btn" style="display: none;">Create your first reel</button>
            </div>
        <?php else: ?>
            <!-- Main posts grid -->
            <div class="saved-posts-grid">
                <?php foreach ($user_saved_posts as $saved_post): ?>
                    <!-- Grid item - click to open dialog -->
                    <div class="saved-post-item"
                         data-post-id="<?php echo $saved_post['post_id']; ?>"
                         data-media-type="<?php echo $saved_post['media_type']; ?>"
                         data-user-id="<?php echo $temp_user_id; ?>">
                        <?php if ($saved_post['media_type'] === 'image'): ?>
                            <img src="../posts_images/<?php echo htmlspecialchars($saved_post['media_url']); ?>" alt="Post image">
                        <?php else: ?>
                            <video>
                                <source src="../posts_videos/<?php echo htmlspecialchars($saved_post['media_url']); ?>" type="video/mp4">
                            </video>
                            <div class="saved-video-icon">
                                <i class="fa-solid fa-play custom-icon"></i>
                            </div>

                        <?php endif; ?>
                        <div class="saved-post-overlay">
                            <div class="saved-post-stats">
                                <div class="saved-post-stat"><i class="fa-solid fa-heart" style="color: #ffffff;"></i> <?php echo $saved_post['likes_count']; ?></div>
                                <div class="saved-post-stat"><i class="fa-solid fa-comment" style="color: #ffffff;"></i> <?php echo $saved_post['comments_count']; ?></div>
                                <div class="saved-post-stat"><i class="fa-solid fa-bookmark" style="color: #ffffff;"></i> <?php echo $saved_post['savings_count']; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Separate modal dialogs (outside the grid) -->
            <?php foreach ($user_saved_posts as $saved_post): ?>
                <dialog class="saved-modal-dialog" id="saved-modal-<?php echo $saved_post['post_id']; ?>">
                    <div class="saved-modal-content">
                        <div class="saved-modal-header">
                            <h2>Post Details</h2>
<!--                            <button class="saved-modal-close" data-post-id="--><?php //echo $saved_post['post_id']; ?><!--">×</button>-->
                            <div class="saved-modal-close" data-post-id="<?php echo $saved_post['post_id']; ?>"><!-- modified 9-11 -->
                                <i class="fa-solid fa-xmark"></i>
                            </div>
                        </div>
                        <div class="saved-modal-body">
                            <div class="saved-post-info">
                                <div class="saving-header-saved">
                                <!-- Make profile image clickable  for savers this more robust -->
<!--                                <a href="../profile/profile.php?user_id=--><?php //echo $user_id ; ?><!--&user_we_will_visit=--><?php //echo $saved_post['user_id']; ?><!--" >-->
<!--                                    <img src="../profile_images/--><?php //echo ($saved_post['profile_picture_url']); ?><!--">-->
<!--                                </a>-->

                                    <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $saved_post['user_id']; ?>&post_id=<?php echo $saved_post['post_id']; ?>&scroll=0" >
                                        <img src="../profile_images/<?php echo ($saved_post['profile_picture_url']); ?>">
                                    </a>


                                    <!--     make the username clickable  this more robust -->
<!--                                <a href="../profile/profile.php?user_id=--><?php //echo $user_id ; ?><!--&user_we_will_visit=--><?php //echo $saved_post['user_id']; ?><!--" class="username-link" style="text-decoration: none">-->
<!--                                    <strong>--><?php //echo ($saved_post['username']); ?><!--</strong>-->
<!--                                </a>-->

                                    <a href="../profile/profile.php?user_id=<?php echo $user_id ; ?>&user_we_will_visit=<?php echo $saved_post['user_id']; ?>&post_id=<?php echo $saved_post['post_id']; ?>&scroll=0" class="username-link" style="text-decoration: none">
                                        <strong><?php echo ($saved_post['username']); ?></strong>
                                    </a>

                                    <div class="saved-caption-text"><?php echo htmlspecialchars($saved_post['caption'] ); ?></div>
                                </div>
                            </div>

                            <?php if ($saved_post['media_type'] === 'image'): ?>
                                <img src="../posts_images/<?php echo htmlspecialchars($saved_post['media_url']); ?>" alt="Post image" class="saved-modal-image">
                            <?php else: ?>
                                <video controls class="saved-modal-video">
                                    <source src="../posts_videos/<?php echo htmlspecialchars($saved_post['media_url']); ?>" type="video/mp4">
                                </video>

                            <?php endif; ?>

                        </div>
                    </div>
                </dialog>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>




<!-- Footer -->
<footer>
    <div class="meta-info">
        English © 2025 Instagram from Meta
    </div>
</footer>

    <script src="profile_js.js"></script>
    <script src="post_modal_logic.js"></script>
    <script type="module" src="translating_logic.js"></script>
    <script src="savings_list_logic.js"></script>
    <script src="likes_list_logic.js"></script>
    <script src="editProfile_logic.js"></script>
    <script src="followers_list_logic.js"></script>
    <script src="followings_list_logic.js"></script>

    <script src="saved_posts_logic.js"></script>

</body>
</html>


<?php
function  getPostsCount($user_id,$pdo){

    //count(*):count all rows that match the mentioned Where condition
    $stmt = $pdo->prepare("SELECT COUNT(*) as posts_count FROM posts WHERE posts.user_id=?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['posts_count'];

}

function  getFollowingsCount($user_id,$pdo){
    //count(*):count all rows that match the mentioned Where condition
    $stmt = $pdo->prepare("SELECT COUNT(*) as followings_count FROM follows WHERE follows.follower_id=?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['followings_count'];

}

//fetch the people that the current user follows اللي بتابعهم
// New function to get followers list
function getFollowingsList($user_id, $pdo) {
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.profile_picture_url 
        FROM follows f 
        JOIN users u ON f.followee_id = u.user_id 
        WHERE f.follower_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



//fetch the people who follows the current user اللي بتابعو
function getFollowersList($user_id, $pdo) {
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.profile_picture_url 
        FROM follows f 
        JOIN users u ON f.follower_id = u.user_id 
        WHERE f.followee_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function  getFollowersCount($user_id,$pdo){

    //count(*):count all rows that match the mentioned Where condition
    $stmt = $pdo->prepare("SELECT COUNT(*) as followers_count FROM follows WHERE follows.followee_id=?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['followers_count'];

}


function getBio($user){

  if($user['bio'] != ""){
      return $user['bio'];
  }
  //the default bio
  return "Welcome $user[username] !";


}



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

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const urlParams = new URLSearchParams(window.location.search);
        const scrollParam = urlParams.get('scroll');

        if (scrollParam === '0') {
            // Disable scrolling
            document.body.style.overflow = "hidden";
         } else {
            // Enable scrolling (default)
            document.body.style.overflow = "auto";
         }

     });
</script>



