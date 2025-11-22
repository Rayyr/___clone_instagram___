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

//logged in user
//$user_id = $_SESSION['user_id'];
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $comment_content = trim($_POST['comment_content']);

    // Validate input
    if (!empty($comment_content) && !empty($post_id)) {
        try {
            // Insert comment into post_comments table
            $stmt = $pdo->prepare("INSERT INTO post_comments (post_id, user_id, comment_content,created_at) VALUES (?, ?,?, NOW())");
            $stmt->execute([$post_id, $user_id, $comment_content]);

            // Update comments count in posts table
            $stmt = $pdo->prepare("UPDATE posts SET comments_count = comments_count + 1 WHERE post_id = ?");
            $stmt->execute([$post_id]);


            echo '<script>
            window.location.href = "../home/home.php?user_id=' . $user_id . '";  // JavaScript redirect
                 </script>';
            exit();


        } catch (PDOException $e) {
            $error = "Error posting comment: " . $e->getMessage();
            echo $error;
        }
    }
}
