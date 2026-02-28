<?php
// 1. Enable Error Reporting to see the crash
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Absolute Path Check
$basePath = __DIR__ . '/PHPMailer/';

if (!file_exists($basePath . 'Exception.php')) {
    die("Error: PHP cannot find PHPMailer/Exception.php at " . $basePath);
}

require $basePath . 'Exception.php';
require $basePath . 'PHPMailer.php';
require $basePath . 'SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzokit@gmail.com'; 
        $mail->Password   = 'wvyjortggzdidxqa'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;                                    

        $mail->setFrom('renzokit@gmail.com', 'Portfolio System');
        $mail->addAddress('renzokit@gmail.com'); 

        $mail->isHTML(true);
        $mail->Subject = "New Message from " . $_POST['contact_name'];
        $mail->Body    = "Name: " . $_POST['contact_name'] . "<br>Email: " . $_POST['contact_email'] . "<br>Message: " . $_POST['message'];

        $mail->send();
        header("Location: index.php?status=success#contact");
        exit();
        
    } catch (Exception $e) {
        // If SMTP fails, show the error instead of redirecting
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        exit();
    }
}
