<?php
    //get the correct sent code from the url
    $sent_code = $_GET['code'];
    $email = $_GET['email'];
?>


<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification 2 &#8226; Instagram</title>

    <link rel="icon" href="../../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="check_code_style.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Dancing+Script" />
</head>
<body>
<div class="container">
    <div class="check-code-box">
         <p class="desc">Enter the code you have received via the email</p>

        <form method="post" action="check_entered_code.php">
            <input style="display: none" type="text" inputmode="numeric" name="sent_code" value="<?php echo htmlspecialchars($sent_code); ?>">
            <input style="display: none" type="email"  name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input   class="code" type="text" inputmode="numeric" maxlength="6" name="code" placeholder="6-Digit Code" required>
            <button type="submit" class="check-code-btn">Verify Code</button>
        </form>

        <div class="back_btn">
            <button class="btn" onclick="window.location.href='../../login/login.php'">Back to login</button>
        </div>

    </div>

</div>


</body>
</html>