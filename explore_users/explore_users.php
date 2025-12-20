<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mini_instagram';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

//display all registerd users expect the current of sure , then he will follow as he need
//get current user_id from URL
$user_id=$_GET['user_id'];


$stmt = $pdo->prepare("
    SELECT u.* 
    FROM users u
    WHERE u.user_id != ?
    AND NOT EXISTS (
        SELECT 1 
        FROM follows f 
        WHERE f.follower_id = ?
         AND f.followee_id = u.user_id )
");
$stmt->execute([$user_id,$user_id]);
$other_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="explore_users_style.css">
    <title>Explore Users &bull; Instagram </title>
</head>
<body>


<!-- Header/Navigation -->
<header>
    <div class="nav-container">
        <div class="logo">
            <img id="home1" src="../assets/login_register_page_logos/instagram_text.png" alt="instagram logo">
        </div>

        <div class="nav-icons">
            <i class="fas fa-home " id="home2"></i>
        </div>
    </div>
</header>


<div class="users-grid">
    <?php if(!empty($other_users)) : ?>
    <?php foreach ($other_users as $other_user): ?>

        <div class="user-card">
            <!-- Profile Picture -->
            <a style="text-decoration: none ; color: black" href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $other_user['user_id']; ?>">
            <div class="user-avatar">
                    <img src="../profile_images/<?php echo htmlspecialchars($other_user['profile_picture_url']); ?>"
                         alt="<?php echo htmlspecialchars($other_user['username']); ?>">
            </div>
            </a>

            <!-- User Info -->
            <a style="text-decoration: none ; color: black" href="../profile/profile.php?user_id=<?php echo $user_id; ?>&user_we_will_visit=<?php echo $other_user['user_id']; ?>">
                <div class="username">
                    <?php echo htmlspecialchars($other_user['username']); ?>
                </div>
            </a>



            <div class="bio"><?php echo htmlspecialchars($other_user['bio']); ?></div>


            <!-- Stats later untill i solve the following issue  -->
            <div style="display: flex; justify-content: space-around; margin-top: 10px; font-size: 12px; color: #666;">
                <div>
                    <strong><?php echo $other_user['followers_count']; ?></strong><br>
                    <span>Followers</span>
                </div>
                <div>
                    <strong><?php echo $other_user['following_count']; ?></strong><br>
                    <span>Following</span>
                </div>
            </div>

            <form method="POST" action="make_relation.php?">
                <input style="display: none" type="number"  name="follower_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <input style="display: none" type="number"  name="followee_id" value="<?php echo htmlspecialchars($other_user['user_id']); ?>">
                <button type="submit" class="btn">Follow</button>
            </form>
        </div>
    <?php endforeach; ?>

    <?php else: ?>
            <div class="no-users">
                <p>No users found !</p>
            </div>
    <?php endif; ?>

</div>


<!-- Footer -->
<footer>
    <div class="meta-info">
        English © 2025 Instagram from Meta
    </div>
</footer>


</body>
</html>


<script>
    const user_id=new URLSearchParams(window.location.search).get('user_id');

    const homeLogo1=document.getElementById('home1').addEventListener('click',function(){
        window.location.href='../home/home.php?user_id='+user_id;
    });

    const homeLogo2=document.getElementById('home2').addEventListener('click',function(){
        window.location.href='../home/home.php?user_id='+user_id;
    });
</script>