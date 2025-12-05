<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mini_instagram';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST')  {

    $user_email=$_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $profile_image_url=$_FILES['profile_image_url']['name'];
    $bio=$_POST['bio'];

    if(empty($bio))
        $bio="No bio yet !" ;//can later be modified when he edit his profile

    $stmt=$pdo->prepare("INSERT INTO users (username,email,phone,password,profile_picture_url,bio,followers_count,following_count,created_at,updated_at,email_verified)
               VALUES (?,?,?,?,?,?,0,0,NOW(),NOW(),1)");
    $stmt->execute([$username, $user_email, $phone_number,$password, $profile_image_url, $bio]);

    //redirect him to log in page
    header("location:../login/login.php");

//    echo $user_email;
//    echo $username;
//    echo $password;
//    echo $phone_number;
//    echo $profile_image_url;
//    echo $bio;
}



?>

