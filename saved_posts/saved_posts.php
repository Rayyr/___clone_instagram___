<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
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

//get the logged in user
//$user_id = $_SESSION['user_id'];
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;


// Handle like submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];

    try {
        //initially we need to check if the post is saved via this user if yes then if btn was clicked then the save icon will be removed , else ....

        if( isSaved($post_id, $user_id, $pdo)==true) {//then we will remove it (unsave)
            // remove save
            $stmt = $pdo->prepare("DELETE FROM  saved_posts WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$post_id, $user_id]);

            // Update savings count in posts table
            $stmt = $pdo->prepare("UPDATE posts SET savings_count = savings_count - 1 WHERE post_id = ?");
            $stmt->execute([$post_id]);
        }
        else {
            // Insert save
            $stmt = $pdo->prepare("INSERT INTO saved_posts (post_id, user_id,saved_at) VALUES (?, ?,NOW())");
            $stmt->execute([$post_id, $user_id]);

            // Update savings count in posts table
            $stmt = $pdo->prepare("UPDATE posts SET savings_count = savings_count + 1 WHERE post_id = ?");
            $stmt->execute([$post_id]);
        }

        echo '<script>
             window.location.href = "../home/home.php?user_id=' . $user_id . '";  // JavaScript redirect
                 </script>';
        exit();


    } catch (PDOException $e) {
        $error = "Error saving a post : " . $e->getMessage();
        echo $error;
    }

}



function isSaved($post_id, $user_id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as savings_count FROM saved_posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $result = $stmt->fetch();

        return $result['savings_count'] > 0;

    } catch(PDOException $e) {
        error_log("Error checking saving status : " . $e->getMessage());
        return false;
    }
}
?>
