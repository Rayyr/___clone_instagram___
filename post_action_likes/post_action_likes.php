<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

$user_id = $_SESSION['user_id'];

// Handle like submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];

        try {
            //initially we need to check if the post is liked via this user if yes then if btn was clicked then the like will be removed , else ....

           if( isLiked($post_id, $user_id, $pdo)==true) {//then we will remove it (unlike)
               // remove like
               $stmt = $pdo->prepare("DELETE FROM  post_likes WHERE post_id = ? AND user_id = ?");
               $stmt->execute([$post_id, $user_id]);

               // Update likes count in posts table
               $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count - 1 WHERE post_id = ?");
               $stmt->execute([$post_id]);
           }
            else {
                // Insert like
                $stmt = $pdo->prepare("INSERT INTO post_likes (post_id, user_id,liked_at) VALUES (?, ?,NOW())");
                $stmt->execute([$post_id, $user_id]);

                // Update likes count in posts table
                $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE post_id = ?");
                $stmt->execute([$post_id]);
            }

            echo '<script>
                window.location.href = "../home/home.php";  // JavaScript redirect
                 </script>';
            exit();


        } catch (PDOException $e) {
            $error = "Error liking a post : " . $e->getMessage();
            echo $error;
        }

}


function isLiked($post_id, $user_id, $pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $result = $stmt->fetch();

        return $result['like_count'] > 0;

    } catch (PDOException $e) {
        error_log("Error checking like status: " . $e->getMessage());
        return false;
    }
}
