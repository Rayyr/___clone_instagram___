<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password &#8226; Instagram</title>
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="forget_password_style.css">

</head>
<body>
<div class="container">
    <div class="login-box">

        <div class="logo">
            <img src="../assets/forget_password_logos/lock.png" alt="lock logo">
        </div>

        <h1>Trouble logging in?</h1>

        <p class="description">
            Enter your new password , then you can get back into your account.
        </p>

        <div class="input-group">
            <form method="post" action="handle_forget_password.php">
                <input type="text" name="phone_number" placeholder="Phone Number" minlength="6" maxlength="6" required>
            <input type="password" name="password" placeholder="New Password" minlength="6" maxlength="6" required>
                <div class="change_btn">
                    <button class="btn" type="submit">Change Password</button>
                </div>
             </form>
        </div>



        <div class="back_btn">
            <button class="btn" onclick="window.location.href='../login/login.php'">Back to login</button>
        </div>

    </div>

    <div class="footer">
        <div class="meta-info">
            © 2025 Instagram from Meta
        </div>
    </div>

</div>


</body>
</html>