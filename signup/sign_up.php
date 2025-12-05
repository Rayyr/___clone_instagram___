<?php
//get the correct email  from the url
$email = $_GET['email'];
?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up&#8226;Instagram</title>

    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="sign_up_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="signup-box">
        <img class="instagram-written-text-logo" src="../assets/login_register_page_logos/instagram_text.png" alt="instagram as written text logo">
        <p class="desc">Sign up to see photos and videos from your friends</p>

        <form method="POST" action="handle_sign_up.php" enctype="multipart/form-data">

            <input style="display: none" type="email"  name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="text" placeholder="Username" name="username" required>
            <input type="password" placeholder="Password" name="password" minlength="6" maxlength="6" required>
            <input type="text" inputmode="numeric" placeholder="Phone Number" name="phone_number" minlength="6" maxlength="6" required>
            <input type="file" name="profile_image_url" accept="image/*" required>


            <input type="text" placeholder="Bio"  name="bio">
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
    </div>

    <div class="login-box">
        <p>Do you have an account? <a href="../login/login.php">Log in</a></p>
    </div>
</div>
</body>
</html>