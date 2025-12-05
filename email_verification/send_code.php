<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require '../vendor/autoload.php';


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



//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_POST['email'];

    //check if this email is regostered previouslly for other user
    $isValid = validOrNot($user_email, $pdo);

    if ($isValid) {
        //6 digits length
        $generated_code = rand(100000, 999999);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'khasaatirayaa@gmail.com';                     //SMTP username , sender email , the email we will send from it
            $mail->Password = 'ddrmbesqsoaqytti';                               // password ( not the gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           //Enable implicit TLS encryption
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('khasaatirayaa@gmail.com', 'Mailer'); //sender
            $mail->addAddress($_POST['email']);     //Add a recipient (who you are sending the email to).

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Instagram-Email Verification Code';
            $mail->Body = "<p style='font-size: large'>Please verify your identity , use the following code : <b>$generated_code</b> </p> ";


            $mail->send();
            // echo 'Message has been sent';
            header('Location: ../email_verification/check_code/write_code.php?code=' . $generated_code . '&email=' . $user_email);
        }
        catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    else {
        echo "<script>
        alert('Sorry, this email is associated with existed Instagram user , try with different email !');
        window.location.href = 'verify_email.php';
    </script>";
    }
}
else {
    header('Location: ../login/login.php');
    exit();
}



function validOrNot($user_email,$pdo){
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE users.email = ?");
    $stmt->execute([$user_email]);

    if($stmt->fetchColumn()>0){
        return false;
    }
    return true;
}
?>

