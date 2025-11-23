<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $_SESSION['user_email'] = $email;

    $verification_code = rand(100000, 999999);
    $_SESSION['verification_code'] = $verification_code;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'alaasawalhh14@gmail.com';
        $mail->Password   = 'gqpy otpj tlfb uwfe';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('alaasawalhh14@gmail.com', 'Your Name'); // صححت هنا
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body    = "Your verification code is: <b>$verification_code</b>";

        $mail->send();

        // الانتقال لصفحة إدخال الكود
        header("Location: enter_the_confirmation_code.php");
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification &#8226; Instagram</title>

    <link rel="stylesheet" href="verify_email_when_sign_up.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Dancing+Script" />
</head>
<body>
<div class="container">
    <div class="signup-box">
        <h1 class="logo">Verify your email address</h1>
        <p class="desc">Enter the email address at which you can be contacted</p>

        <form method="post">
            <input class="email" type="email" name="email" placeholder="Email Address" required>
            <button type="submit" class="send-code-btn">Send Code</button> <!--in php backend will be menipulated , so when he write the email then i will add input to write the received code -->
        </form>

        <div class="back_btn">
            <button class="btn" onclick="window.location.href='../login/login.html'">Back to login</button>
        </div>

    </div>

</div>

<script src="verify_email.js"></script>
</body>
</html>
