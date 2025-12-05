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
//$user_id = $_SESSION['user_id'];


//modified 9-11
// Get user_id from URL parameter o
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;

//impossilple to accur since we will be in this page from home page !!!
if($user_id === -1) {
// If no user_id provided, redirect to login or show error
    header("Location: ../login/login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if file was uploaded , dummy since share post btn will be enabled once the file is being uploaded
   // if (isset($_FILES['media']) ) {

//fetch the uploaded file
        $uploadedFile = $_FILES['media'];

        // Get file details
        $fileName = $uploadedFile['name'];
        $fileTmpName = $uploadedFile['tmp_name'];
        $fileSize = $uploadedFile['size'];
        $fileType = $uploadedFile['type'];//image.jpg , image.* ... , video.*...
        $fileError = $uploadedFile['error'];
        $post_caption=$_POST['caption'];

    // Extract just "image" or "video"
    $mainType = explode('/', $fileType)[0];


        try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, media_url, caption, likes_count, comments_count, created_at, media_type, savings_count) VALUES (? , ? , ? , 0, 0, NOW(), ?, 0)");
        $stmt->execute([$user_id, $fileName, $post_caption, $mainType]);
        }
        catch (PDOException $e) {
            // Handle error - log it and show user message
            error_log("Database error: " . $e->getMessage());

            // You can set a session error message instead of alert
           // $_SESSION['error'] = "Failed to create post. Please try again.";
            echo "<script>
                 alert('Error occured while posting!');
                 </script>";
        }

        header('Location: ../home/home.php?user_id=' . $user_id); //modified 9-11
        exit;
    }




