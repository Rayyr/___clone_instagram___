<?php

session_start();


// الاتصال بقاعدة البيانات
$conn = mysqli_connect('localhost','root','','mini_instagram');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mini_instagram';

// Get current user data(current logged in user logically who make the post)
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $story_id = $_POST['story_id'];
    $user_id = $_POST['user_id'];

    try {
        $query = $pdo->prepare("INSERT INTO story_views (story_id, viewer_id, viewed_at) VALUES (?, ?, NOW())");
        $query->execute([$story_id, $user_id]);


    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}

?>




