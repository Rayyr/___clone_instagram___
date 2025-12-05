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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     $user_id=$_POST['follower_id'];
    $user_will_be_followed=$_POST['followee_id'];

    $stmt  = $pdo->prepare("INSERT INTO follows (follower_id,followee_id,start_following_at) VALUES (?,?,NOW())");
    $stmt->execute([$user_id,$user_will_be_followed]);

    //update the followers count for the followee
    $stmt=$pdo->prepare("UPDATE users SET followers_count=followers_count+1 WHERE user_id=?");
    $stmt->execute([$user_will_be_followed]);

    //update the followings count for the follower
    $stmt=$pdo->prepare("UPDATE users SET following_count=following_count+1 WHERE user_id=?");
    $stmt->execute([$user_id]);

header("Location: ../explore_users/explore_users.php?user_id=$user_id");
}
?>