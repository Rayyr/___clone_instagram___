<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // 1️⃣ Generate 6-digit code
    $code = rand(100000, 999999);

    // 2️⃣ Store it in session or database (example: session)
    $_SESSION['verification_code'] = $code;
    $_SESSION['verification_email'] = $email;
    $_SESSION['code_expiry'] = time() + 300; // expires in 5 minutes

    // 3️⃣ Send email
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER') ?: 'youremail@gmail.com';
        $mail->Password = getenv('EMAIL_PASS') ?: 'your_app_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('youremail@gmail.com', 'Your Website');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "Your verification code is: <b>$code</b>";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Verification code sent!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
    }
}
