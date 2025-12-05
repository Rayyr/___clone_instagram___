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



// Get user_id from URL parameter o
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : -1;


//impossilple to occur since we will be in this page from home page !!!
if($user_id === -1) {
// If no user_id provided, redirect to login or show error
    header("Location: ../login/login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //fetch the uploaded file
    $uploadedFile = $_FILES['media'];

    // Get file details
    $fileName = $uploadedFile['name'];
    $fileTmpName = $uploadedFile['tmp_name'];
    $fileSize = $uploadedFile['size'];
    $fileType = $uploadedFile['type'];//image.jpg , image.* ...
    $fileError = $uploadedFile['error'];



    try {
        $stmt = $pdo->prepare("INSERT INTO stories (user_id, media_url, created_at,expires_at) VALUES (? , ? ,NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY))");
        $stmt->execute([$user_id, $fileName]);
    }
    catch (PDOException $e) {
        // Handle error - log it and show user message
        error_log("Database error: " . $e->getMessage());
        echo "<script>alert('Error occured while sharing!');</script>";
    }

    header('Location: ../home/home.php?user_id=' . $user_id); //modified 9-11
    exit;
}




