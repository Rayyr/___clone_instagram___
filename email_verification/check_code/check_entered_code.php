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

    $user_code = $_POST['code'];
    $sent_code = $_POST['sent_code'];
    $email = $_POST['email'];

    if($user_code == $sent_code){
       // echo "correct";
         header('Location: ../../signup/sign_up.php?email='.$email);
    }
    else {
         echo "<script>
        alert('Sorry, incorrect code!');
        window.location.href = '../verify_email.php';
    </script>";
    }
}

else {
    header('Location: ../login/login.php');
    exit();
}
?>
