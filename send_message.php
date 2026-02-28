<?php
// 1. Load the PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Turn on error reporting so we see EVERYTHING
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name    = htmlspecialchars($_POST['contact_name'] ?? '');
    $email   = filter_var($_POST['contact_email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 2; // This will print the full conversation with Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzokit@gmail.com';
        $mail->Password   = 'ctuxosamfwlbldyw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('renzokit@gmail.com', 'Portfolio Contact');
        $mail->addAddress('renzokit@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Inquiry: $subject";
        $mail->Body    = "Name: $name <br> Message: $message";

        $mail->send();
        echo "<h1>SUCCESS! The email was sent.</h1>";
        
    } catch (Exception $e) {
        echo "<h1>ERROR DETECTED:</h1>";
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    exit;
}
