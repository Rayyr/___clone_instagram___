

<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification 1 &#8226; Instagram</title>

    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="verify_email_when_sign_up.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Dancing+Script" />
</head>
<body>
<div class="container">
    <div class="signup-box">
        <h1 class="logo">Verify your email address</h1>
        <p class="desc">Enter the email address at which you can be contacted</p>

        <form method="post" action="send_code.php">
            <input style="display: block" class="email" type="email" name="email" placeholder="user@gmail.com" required>
            <button type="submit" class="send-code-btn">Send Code</button>
        </form>

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