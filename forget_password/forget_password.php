<?php
/**
 * forget_password.php
 * - ملف واحد يجمع: إرسال كود / إدخال كود / تغيير كلمة المرور
 * - يعتمد على PHPMailer (Composer)
 * - التصميم: رسمي مثل Instagram (CSS مضمّن)
 */

session_start();
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ------------------ إعدادات (عدل هنا) ------------------ */
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'mini_instagram';

$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpSecure = 'tls';
$smtpUser = 'alaasawalhh14@gmail.com';
$smtpPass = 'gqpy otpj tlfb uwfe';

$store_plain_password = false;
/* -------------------------------------------------------- */

// اتصال بقاعدة البيانات
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$conn) {
    die("DB Connection failed: " . mysqli_connect_error());
}

// helper لإرسال البريد
function send_verification_email($toEmail, $code, $smtpHost, $smtpPort, $smtpSecure, $smtpUser, $smtpPass) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = $smtpSecure;
        $mail->Port       = $smtpPort;

        $mail->setFrom($smtpUser, 'Instagram');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your Instagram verification code';
        $mail->Body    = "
            <div style='font-family: Arial, Helvetica, sans-serif; color:#111;'>
                <h3 style='margin:0;padding:0;'>Instagram</h3>
                <p style='margin:4px 0 12px 0;'>Your verification code is:</p>
                <div style='font-size:24px; font-weight:bold; letter-spacing:4px;'>$code</div>
                <p style='color:#666; font-size:13px;'>If you didn't request this, ignore this email.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

/* -------------------- منطق السيرفر لكل زر -------------------- */
$server_error = '';
$server_success = '';

// زر: إرسال الكود
if (isset($_POST['action']) && $_POST['action'] === 'send_code') {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['fp_user_email'] = $email;

        $code = rand(100000, 999999);
        $_SESSION['fp_code'] = (string)$code;
        $_SESSION['fp_code_time'] = time();

        $sendResult = send_verification_email($email, $code, $smtpHost, $smtpPort, $smtpSecure, $smtpUser, $smtpPass);
        if ($sendResult === true) {
            $_SESSION['fp_step'] = 'code_sent';
            $server_success = "Verification code sent to $email";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $server_error = "Mailer error: " . htmlspecialchars($sendResult);
        }
    } else {
        $server_error = "Please enter a valid email address.";
    }
}

// زر: التحقق من الكود
if (isset($_POST['action']) && $_POST['action'] === 'verify_code') {
    if (!isset($_SESSION['fp_user_email']) || !isset($_SESSION['fp_code'])) {
        $server_error = "No verification request found. Please start again.";
    } else {
        $entered = trim($_POST['confirmation_code']);
        $valid = true;
        if (isset($_SESSION['fp_code_time']) && (time() - $_SESSION['fp_code_time'] > 600)) {
            $valid = false;
            $server_error = "The verification code expired. Please resend.";
        }
        if ($valid) {
            if ($entered === $_SESSION['fp_code']) {
                $_SESSION['verified_email'] = $_SESSION['fp_user_email'];
                $_SESSION['fp_step'] = 'verified';
                unset($_SESSION['fp_code'], $_SESSION['fp_code_time']);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $server_error = "The code you entered is incorrect!";
            }
        }
    }
}

// زر: إعادة إرسال الكود
if (isset($_POST['action']) && $_POST['action'] === 'resend_code') {
    if (!isset($_SESSION['fp_user_email'])) {
        $server_error = "Please enter your email first.";
    } else {
        $email = $_SESSION['fp_user_email'];
        $code = rand(100000, 999999);
        $_SESSION['fp_code'] = (string)$code;
        $_SESSION['fp_code_time'] = time();
        $sendResult = send_verification_email($email, $code, $smtpHost, $smtpPort, $smtpSecure, $smtpUser, $smtpPass);
        if ($sendResult === true) {
            $_SESSION['fp_step'] = 'code_sent';
            $server_success = "Verification code resent to $email";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $server_error = "Mailer error: " . htmlspecialchars($sendResult);
        }
    }
}

// زر: تغيير كلمة المرور
if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    if (!isset($_SESSION['verified_email'])) {
        $server_error = "You must verify your email first.";
    } else {
        $email = $_SESSION['verified_email'];
        $newPassword = trim($_POST['password']);
        if (empty($newPassword)) {
            $server_error = "Please enter a new password.";
        } else {
            $passwordToStore = $newPassword;
            $updateSql = "UPDATE users SET password = ? WHERE email = ?";
            if ($stmt = mysqli_prepare($conn, $updateSql)) {
                mysqli_stmt_bind_param($stmt, "ss", $passwordToStore, $email);
                if (mysqli_stmt_execute($stmt)) {
                    unset($_SESSION['verified_email'], $_SESSION['fp_user_email'], $_SESSION['fp_step']);
                    mysqli_stmt_close($stmt);
                    header("Location: ../login/login.php");
                    exit;
                } else {
                    $server_error = "Error updating password. Please try again.";
                    mysqli_stmt_close($stmt);
                }
            } else {
                $server_error = "Database error (prepare failed).";
            }
        }
    }
}

/* -------------------- تحديد الشاشة -------------------- */
if (isset($_SESSION['fp_step'])) {
    $step = $_SESSION['fp_step'];
    if ($step === 'code_sent') $screen = 'screen-code';
    else if ($step === 'verified') $screen = 'screen-reset';
    else $screen = 'screen-email';
} else {
    $screen = 'screen-email';
    $_SESSION['fp_step'] = 'email';
}

?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password • Instagram</title>
    <style>
        :root{--insta-gradient:linear-gradient(45deg,#405de6,#5851db,#833ab4,#c13584,#e1306c,#fd1d1d);--card-bg:#fff;--muted:#8e8e8e;}
        *{box-sizing:border-box;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background: #fafafa;}
        .layout{display:flex;gap:28px;align-items:center;padding:24px;}
        .left{display:flex;flex-direction:column;gap:16px;align-items:center;}
        .phone-mock{width:320px;height:640px;border-radius:28px;background: linear-gradient(180deg,#fff,#f6f6f6);box-shadow: 0 6px 20px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:center;overflow:hidden;}
        .phone-mock img{width:88%;}
        .card{width:380px;background:var(--card-bg);border:1px solid #dbdbdb;border-radius:8px;padding:22px;text-align:center;}
        .insta-logo{background: var(--insta-gradient);-webkit-background-clip: text;background-clip: text;color: transparent;font-weight:700;font-size:30px;letter-spacing:1px;margin-bottom:8px;}
        .desc{color:var(--muted);font-size:14px;margin:8px 0 16px;}
        .screen{display:none;}
        .screen.active{display:block;animation: fadeIn .18s ease;}
        input[type="email"], input[type="text"], input[type="password"]{width:100%;padding:10px 12px;margin:8px 0 12px;border-radius:4px;border:1px solid #dbdbdb;background:#fafafa;font-size:15px;}
        .btn{width:100%;padding:10px 12px;border-radius:4px;border: none;cursor:pointer;font-weight:600;font-size:15px;}
        .btn.primary{background:var(--insta-gradient);color:#fff;}
        .btn.ghost{background:transparent;color:#0095f6;border:1px solid transparent;}
        .small{font-size:13px;color:var(--muted);margin-top:10px;}
        .error{color:#d93025;margin-top:8px;}
        .success{color:#0a8a08;margin-top:8px;}
        .code-input{letter-spacing:6px;text-align:center;font-size:18px;padding:12px;}
        .footer{margin-top:14px;font-size:13px;color:var(--muted);}
        @media (max-width:900px){.layout{flex-direction:column;padding:12px;}.left{display:none;}.card{width:92%;}}
        @keyframes fadeIn{from{opacity:0;transform: translateY(6px)} to{opacity:1;transform:none}}
    </style>
</head>
<body>
<div class="layout">
    <div class="left">
        <div class="phone-mock">
            <img src="../assets/forget_password_logos/phone_mock.png" alt="phone mock" onerror="this.style.display='none'">
        </div>
        <div style="width:320px;text-align:center;color:var(--muted)">
            <p style="font-weight:600;">Instagram</p>
            <p style="font-size:13px;">Find photos and videos from your friends</p>
        </div>
    </div>

    <div class="card" role="main" aria-labelledby="title">
        <div id="title" class="insta-logo">Instagram</div>
        <div class="desc">Trouble logging in? We'll help you reset your password.</div>

        <!-- شاشة 1: إدخال الإيميل -->
        <div id="screen-email" class="screen <?php if($screen==='screen-email') echo 'active'; ?>">
            <form method="post" novalidate>
                <input type="hidden" name="action" value="send_code">
                <input type="email" name="email" placeholder="Email address" required>
                <button class="btn primary" type="submit">Send Code</button>
            </form>
            <div class="small">We'll send a 6-digit code to your email.</div>
        </div>

        <!-- شاشة 2: إدخال الكود -->
        <div id="screen-code" class="screen <?php if($screen==='screen-code') echo 'active'; ?>">
            <div style="text-align:left;color:var(--muted);margin-bottom:8px;">
                <strong>Enter confirmation code</strong>
                <div class="small">We sent a code to <span style="font-weight:600;color:#111;"><?= isset($_SESSION['fp_user_email'])?htmlspecialchars($_SESSION['fp_user_email']):'' ?></span></div>
            </div>
            <form method="post" novalidate>
                <input type="hidden" name="action" value="verify_code">
                <input type="text" name="confirmation_code" class="code-input" maxlength="6" placeholder="000000" required pattern="\d{6}">
                <button class="btn primary" type="submit">Next</button>
            </form>
            <form method="post" style="margin-top:10px;">
                <input type="hidden" name="action" value="resend_code">
                <button class="btn ghost" type="submit">I didn't receive the code</button>
            </form>
        </div>

        <!-- شاشة 3: إعادة تعيين كلمة المرور -->
        <div id="screen-reset" class="screen <?php if($screen==='screen-reset') echo 'active'; ?>">
            <div style="text-align:left;color:var(--muted);margin-bottom:8px;">
                <strong>Enter new password</strong>
                <div class="small">Reset password for <span style="font-weight:600;color:#111;"><?= isset($_SESSION['verified_email'])?htmlspecialchars($_SESSION['verified_email']):'' ?></span></div>
            </div>
            <form method="post" novalidate>
                <input type="hidden" name="action" value="reset_password">
                <input type="password" name="password" placeholder="New password" required>
                <button class="btn primary" type="submit">Change Password</button>
            </form>
        </div>

        <?php if(!empty($server_error)): ?>
            <div class="error" role="alert"><?= htmlspecialchars($server_error) ?></div>
        <?php endif; ?>
        <?php if(!empty($server_success)): ?>
            <div class="success"><?= htmlspecialchars($server_success) ?></div>
        <?php endif; ?>

        <div class="footer">
            <a href="../login/login.php" style="text-decoration:none;color:#0095f6;">Back to login</a>
        </div>
    </div>
</div>

<script>
    (function(){
        const screen = "<?= $screen ?>";
        document.querySelectorAll('.screen').forEach(s=>s.classList.remove('active'));
        const el = document.getElementById(screen);
        if(el) el.classList.add('active');
    })();
</script>
</body>
</html>
