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

     $user_id= $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $bio=$_POST['bio'];

    $profile_image_url_from_file_chooser=$_FILES['profile_picture_url'];//complete image with path , name ,,,,

if (isset($profile_image_url_from_file_chooser) && $profile_image_url_from_file_chooser['error'] === UPLOAD_ERR_OK) {
    $profile_image_url=$profile_image_url_from_file_chooser['name'];
}
else
    $profile_image_url = $_POST['profile_img_url'];//original one


    $stmt = $pdo->prepare("UPDATE users SET 
    username = ?, 
    password = ?, 
    phone = ?, 
    profile_picture_url = ?, 
    bio = ?,
    updated_at = NOW()
    WHERE user_id = ?");

    $stmt->execute([$username, $password, $phone_number, $profile_image_url, $bio, $user_id]);


    //redirect him back to his profile page

    echo ' <script>
     alert("Changes has been saved successfully !");
    window.location.href = "../profile/profile.php?user_id=' . $user_id . '&user_we_will_visit=-1";
</script>';

}



?>

