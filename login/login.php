<?php
session_start();

// الاتصال بقاعدة البيانات
$conn = mysqli_connect('localhost','root','','mini_instagram');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

// معالجة تسجيل الدخول عند الضغط على زر submit
if(isset($_POST['submit'])){
    $usernameOrEmailOrPhone = $_POST['username_or_email_or_phonenumber'];
    $pass = $_POST['password'];

    // البحث عن المستخدم بالبريد أو اسم المستخدم أو الهاتف
    $select = "SELECT * FROM users 
               WHERE (username='$usernameOrEmailOrPhone' 
                      OR email='$usernameOrEmailOrPhone' 
                      OR phone='$usernameOrEmailOrPhone') 
                 AND password='$pass'";

    $select_user= "SELECT * FROM users 
               WHERE (username='$usernameOrEmailOrPhone' 
                      OR email='$usernameOrEmailOrPhone' 
                      OR phone='$usernameOrEmailOrPhone') ";

    $result_users=mysqli_query($conn,$select_user);
    if(mysqli_num_rows($result_users) == 0) { $error = "There is no such user registered with this username or email or phone number !"; ;}

    else {
        $result = mysqli_query($conn, $select);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // حفظ بيانات المستخدم في session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username_or_email_or_phonenumber'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['profile_picture_url'] = $user['profile_picture_url'];



            header('Location: ../home/home.php?user_id=' . $_SESSION['user_id'] . '&user_we_will_visit=-1');
            exit;

        } else {
            $error = "Incorrect password !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram &bull; Login</title>
    <link rel="icon" href="../assets/login_register_page_logos/insta_icon.ico">
    <link rel="stylesheet" href="login_style.css">
</head>
<div>

<div class="container">
    <!-- LEFT SIDE -->
    <div class="left-side">
        <img src="../assets/login_register_page_logos/entry_logo.png" alt="Instagram logo">
    </div>

    <!-- RIGHT SIDE -->
    <div class="right-side">
        <div class="login-box">
            <img class="instagram-written-text-logo" src="../assets/login_register_page_logos/instagram_text.png" alt="Instagram logo">

            <form method="POST" action="">
                <input type="text" name="username_or_email_or_phonenumber" placeholder="Phone number, username, or email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit">Log in</button>
            </form>

            <?php if ($error != "") { ?>
                <script>
                    alert("<?php echo($error); ?>");
                </script>
            <?php } ?>


            <div class="divider"><span>OR</span></div>

            <a href="../forget_password/forget_password.html" class="forgot">Forgot password?</a>

            <div class="signup">
                Don’t have an account?
                <a href="../verify_email_when_sign_up/verify_email_when_sign_up.html">Sign up</a>
            </div>
        </div>
    </div>
</div> <!-- container end -->
<div class="footer">
    <div class="meta-info">
        © 2025 Instagram from Meta
    </div>
</div>
</div>

</body>
</html>