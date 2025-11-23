<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: verify_email_when_sign_up.php");
    exit();
}

$email = $_SESSION['user_email'];
$error = "";

if (isset($_POST['next'])) {
    $entered_code = trim($_POST['confirmation_code']);

    if (!empty($entered_code)) {
        if ($entered_code == $_SESSION['verification_code']) {
            $_SESSION['verified_email'] = $email;
            header("Location: ../signup/signup.php");
            exit();
        } else {
            $error = "The code you entered is incorrect!";
        }
    } else {
        $error = "Please enter the confirmation code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Confirmation Code</title>
    <link rel="stylesheet" href="enter_the_confirmation_code.css">
</head>
<body>
<div class="container">
    <h2>Enter the confirmation code</h2>
    <p>To confirm your account, enter the 6-digit code that we sent to
        <span class="email"><?= htmlspecialchars($email) ?></span>.
    </p>

    <!-- استخدم فورم بدل <a> -->
    <form method="POST" action="">
        <input type="text" name="confirmation_code" class="code-input" placeholder="Confirmation code" maxlength="6" required>
        <button type="submit" name="next" class="next-btn">Next</button>
    </form>

    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <button type="submit" name="resend" class="resend-btn">I didn't receive the code</button>
    </form>
</div>
</body>
</html>
