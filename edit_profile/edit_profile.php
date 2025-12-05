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

</body>
</html>