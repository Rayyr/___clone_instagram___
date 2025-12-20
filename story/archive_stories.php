

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


// Fetch user data from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([ $temp_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


//fetch the user archived stories
$stmt = $pdo->prepare("SELECT s.*
                       FROM stories s  
                       WHERE s.user_id = ?
                       ORDER BY s.created_at DESC");
$stmt->execute([$temp_user_id]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getStoryViewersCount($story_id,$pdo)
{
     $stmt = $pdo->prepare("SELECT COUNT(*) as count from story_views WHERE story_views.story_id=?");

    $stmt->execute([$story_id]);
    $stories = $stmt->fetch(PDO::FETCH_ASSOC);
    return $stories['count'];
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
    <link rel="stylesheet" href="archived_stories_style.css">
</head>



<body>



<div class="container">


    <header>

        <div id="goback" class="archive-header">
            <svg aria-label="Back" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="20" role="img" viewBox="0 0 24 24" width="20"><title>Back</title><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="2.909" x2="22.001" y1="12.004" y2="12.004"></line><polyline fill="none" points="9.276 4.726 2.001 12.004 9.276 19.274" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>
            <span>Archive</span>
        </div>

    </header>


    <!-- Tabs -->
    <section class="tabs">
        <div class="tab active" data-tab="saved">
            <div class="html-div xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x9f619 xjbqb8w x78zum5 x15mokao x1ga7v0g x16uus16 xbiv7yw x1n2onr6 x1plvlek xryxfnj x1c4vz4f x2lah0s x1q0g3np xqjyukv x6s0dn4 x1oa3qoh x1nhvcw1">
                <svg aria-label="" class="x1lliihq x1n2onr6 x5n08af" fill="black" height="15" role="img" viewBox="0 0 24 24" width="15"><title></title>
                    <path d="M3.915 5.31q.337-.407.713-.779m-3.121 7.855Q1.5 12.194 1.5 12a10.505 10.505 0 0 1 .516-3.265m3.243 11.338a10.55 10.55 0 0 1-2.89-3.864m14.482 5.108a10.547 10.547 0 0 1-8.163.65M12.002 1.5a10.504 10.504 0 0 1 7.925 17.39" fill="none"
                          stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    </path>
                </svg>
                <span  style="color: black">
                     STORIES
                </span>
            </div>
        </div>
    </section>




    <!-- Posts Content - Always loaded but hidden/shown with CSS , default will be selected  -->
    <div class="tab-content stories-tab active">
        <div class="msg"> <span>Only you can see your archived stories.</span></div>

        <?php if (empty($stories)): ?>
            <div class="empty-state">
                <div class="no-stories">No stories created yet !</div>
            </div>
        <?php else: ?>
            <div class="stories-grid">
                <?php foreach ($stories as $story): ?>
                    <div class="story-item">
                        <img src="../story_images/<?php echo htmlspecialchars($story['media_url']); ?>" alt="Story image">
                        <div class="date-card">
                            <div class="day"><?php echo date('d', strtotime($story['created_at'])); ?></div>
                            <div class="month"><?php echo date('M', strtotime($story['created_at'])); ?></div>
                            <div class="year"><?php echo date('Y', strtotime($story['created_at'])); ?></div>
                        </div>
                        <div class="story-overlay">
                            <div class="story-stats">
                                <div class="story-stat"><i class="fa-solid fa-eye" style="color: #ffffff;"></i><?php echo getStoryViewersCount($story['story_id'],$pdo); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>




    <!-- Footer -->
    <footer>
        <div class="meta-info">
            English © 2025 Instagram from Meta
        </div>
    </footer>

</div>

</body>
</html>


<script>

    const archive=document.getElementById('goback');
    archive.addEventListener('click',function (){
        const user_id= new URLSearchParams(window.location.search).get('user_id');
        window.location.href=`../profile/profile.php?user_id=${user_id}&user_we_will_visit=-1`;
    });
</script>


