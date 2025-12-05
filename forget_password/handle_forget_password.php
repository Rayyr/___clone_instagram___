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

    $phone_number=$_POST['phone_number'];
    $new_password=$_POST['password'];


     $stmt=$pdo->prepare("UPDATE users SET password=? WHERE phone=?");
    $stmt->execute([$new_password,$phone_number]);

  echo ' <script>
     alert("Password Changed Successfully ' . $phone_number . '");
    window.location.href = "../login/login.php";
</script>';

}
?>