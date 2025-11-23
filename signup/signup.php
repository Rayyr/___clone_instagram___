<?php
session_start();

// الاتصال بقاعدة البيانات
$conn = mysqli_connect('localhost', 'root', '', 'mini_instagram');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

// عند الضغط على زر التسجيل
if (isset($_POST['submit'])) {

    // تحقق من أن البريد تم التحقق منه من الواجهة السابقة
    if (!isset($_SESSION['verified_email'])) {
        $error = "You must verify your email first!";
    } else {
        // الإيميل الذي تم التحقق منه من الجلسة السابقة
        $email = $_SESSION['verified_email'];

        // القيم المدخلة من المستخدم
        $username = trim($_POST['username']);
        $password = trim($_POST['password']); // سيتم حفظها كنص صريح كما طلبت

        // التحقق من أن الحقول ليست فارغة
        if (!empty($username) && !empty($password)) {

            // التحقق إذا كان اليوزرنيم مستخدم مسبقًا أو الإيميل موجود مسبقًا
           // $checkQuery = "SELECT id FROM users WHERE email = ? OR username = ?";
            $checkQuery = "SELECT email FROM users WHERE email = ? OR username = ?";

            if ($stmtCheck = mysqli_prepare($conn, $checkQuery)) {
                mysqli_stmt_bind_param($stmtCheck, "ss", $email, $username);
                mysqli_stmt_execute($stmtCheck);
                mysqli_stmt_store_result($stmtCheck);
                $count = mysqli_stmt_num_rows($stmtCheck);
                mysqli_stmt_close($stmtCheck);

                if ($count > 0) {
                    $error = "This email or username is already taken!";
                } else {
                    // **حفظ كلمة المرور كنص صريح** (غير موصى به أمنيًا)
                    $insertQuery = "INSERT INTO users (email, username, password, email_verified) VALUES (?, ?, ?, 1)";
                    if ($stmtIns = mysqli_prepare($conn, $insertQuery)) {
                        mysqli_stmt_bind_param($stmtIns, "sss", $email, $username, $password);
                        if (mysqli_stmt_execute($stmtIns)) {
                            // إزالة بيانات التحقق من الجلسة بعد التسجيل
                            mysqli_stmt_close($stmtIns);
                            unset($_SESSION['verified_email']);
                            header('Location: ../login/login.php');
                            exit;
                        } else {
                            $error = "Error while registering. Please try again.";
                            mysqli_stmt_close($stmtIns);
                        }
                    } else {
                        $error = "Database error (prepare failed).";
                    }
                }
            } else {
                $error = "Database error (prepare failed).";
            }
        } else {
            $error = "Please fill in all required fields!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up • Instagram</title>

    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="signup_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="signup-box">
        <img class="instagram-written-text-logo" src="../assets/login_register_page_logos/instagram_text.png" alt="instagram text logo">
        <p class="desc">Sign up to see photos and videos from your friends</p>

        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>

            <p class="terms">
                People who use our Service may have uploaded your contact information to Instagram.
                <a href="#">Learn more</a>
            </p>
            <p class="terms">
                By registering, you agree to <a href="#">Terms</a>,
                <a href="#">Privacy Policy</a>, and
                <a href="#">Cookies</a>.
            </p>

            <button type="submit" name="submit" class="signup-btn">Sign Up</button>
        </form>

        <?php if (!empty($error)): ?>
            <p class="error" style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>

    <div class="login-box">
        <p>Do you have an account? <a href="../login/login.php">Log in</a></p>
    </div>
</div>
</body>
</html>
