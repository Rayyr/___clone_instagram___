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


 $user_id=$_GET['user_id'];

    $stmt=$pdo->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$user_id]);
    $user=$stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <title>Edit Profile &bull; Instagram</title>
    <link rel="stylesheet" href="edit_profile_style.css">
</head>

<body>


<header>
    <div id="goback" class="archive-header">
        <svg aria-label="Back" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="20" role="img" viewBox="0 0 24 24" width="20"><title>Back</title><line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="2.909" x2="22.001" y1="12.004" y2="12.004"></line><polyline fill="none" points="9.276 4.726 2.001 12.004 9.276 19.274" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>
        <span>Edit Profile</span>
    </div>
</header>




<div class="edit-profile-container">
    <div class="edit-profile-header">
        <h1 class="edit-profile-title">Edit Profile</h1>
    </div>

    <form method="post" action="handle_edit_profile.php" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="profile_img_url" value="<?php echo $user['profile_picture_url'];?>">


        <div class="edit-profile-content">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                 <img src="../profile_images/<?php echo $user['profile_picture_url']; ?>">
            </div>
            <div class="username"><?php echo $user['username']; ?></div>
            <div class="form-input">
                <label for="profile_picture_url">Change Profile Picture</label>
                <input type="file" accept="image/*" name="profile_picture_url">
            </div>
        </div>


        <div class="edit-form">
                <!-- Username -->
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="form-input">
                    <input type="text" name="username" placeholder="Username" value="<?php echo $user['username']; ?>">
                </div>
            </div>


            <!-- Phone -->
            <div class="form-group">
                <label class="form-label">Phone</label>
                <div class="form-input">
                    <input type="text" name="phone_number" placeholder="Phone" minlength="6" maxlength="6" value="<?php echo $user['phone']; ?>">
                </div>
            </div>


            <!-- Password -->
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-input">
                    <input type="password" name="password" placeholder="Password" minlength="6" maxlength="6" value="<?php echo $user['password']; ?>">
                </div>
            </div>



            <!-- Bio -->
            <div class="form-group">
                <label class="form-label">Bio</label>
                <div class="form-input">
                    <input type="text" placeholder="Bio" name="bio" value="<?php echo $user['bio']; ?>">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <div class="form-label"></div>
                <div class="form-input submit-section">
                    <button type="submit" class="submit-btn">Save</button>
                </div>
            </div>

        </form>

        </div>
    </div>
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
    const archive=document.getElementById('goback');
    archive.addEventListener('click',function (){
        const user_id= new URLSearchParams(window.location.search).get('user_id');
        window.location.href=`../profile/profile.php?user_id=${user_id}&user_we_will_visit=-1`;
    });
</script>