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
// Get user_id from URL parameter or we can extract it from the session
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;

//impossilple to accur since we will be in this page from home page !!!
if($user_id === -1) {
// If no user_id provided, redirect to login or show error
header("Location: ../login/login.php");
exit;
}
//we have logged-in user when profile image is clicked from home page
else if($user_id === $_SESSION['user_id']){

}

else {//when profile image is clicked from someones comment / post from home so not the logged in user
    //nth to do since we have our local user_id variable initially is set
};

// Fetch user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            <i class="far fa-heart"></i>
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
                <div><strong><?php echo getPostsCount($user['user_id'],$pdo)?></strong> posts</div> <!--later-->
                <div><strong><?php echo getFollowersCount($user['user_id'],$pdo)?></strong> followers</div><!--later-->
                <div><strong><?php echo getFollowingsCount($user['user_id'],$pdo)?></strong> following</div><!--later-->
            </div>
            <p class="profile-bio">Welcome, <?php echo $user['username']; ?>!</p>
            <div class="profile-actions">
                <button class="btn btn-primary">Edit Profile</button>
            </div>
        </div>
    </section>

    <!-- Tabs -->
    <section class="tabs">
        <div class="tab active">
            <i class="fas fa-table"></i>
            <span>Posts</span>
        </div>
        <div class="tab">
            <i class="fas fa-tv"></i>
            <span>Reels</span>
        </div>
    </section>

    <!-- Empty State -->
    <section class="empty-state">
        <div class="camera-icon">
            <i class="fas fa-camera"></i>
        </div>
        <h2 class="empty-title">Share Photos</h2>
        <p class="empty-description">
            When you share photos, they will appear on your profile.
        </p>
        <button class="share-btn">Share your first photo</button>
    </section>
</div>

<!-- Footer -->
<footer>
    <div class="meta-info">
        English © 2025 Instagram from Meta
    </div>
</footer>

<script src="profile_js.js"></script>
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

function  getFollowersCount($user_id,$pdo){

    //count(*):count all rows that match the mentioned Where condition
    $stmt = $pdo->prepare("SELECT COUNT(*) as followers_count FROM follows WHERE follows.follower_id=?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['followers_count'];

}


function  getFollowingsCount($user_id,$pdo){

    //count(*):count all rows that match the mentioned Where condition
    $stmt = $pdo->prepare("SELECT COUNT(*) as followings_count FROM follows WHERE follows.followee_id=?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['followings_count'];

}
?>