<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Based on your GitHub screenshot, the files are directly in 'PHPMailer/'
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzloiokit.dev@gmail.com'; 
        $mail->Password   = 'wvyj ortg gzdi dxqa'; // Your verified App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Identity
        $mail->setFrom('renzloiokit.dev@gmail.com', 'Portfolio Contact');
        
        // Target Recipient
        $mail->addAddress('renzokit@gmail.com'); 

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Message from " . htmlspecialchars($_POST['contact_name']);
        $mail->Body    = "<h3>New Contact Request</h3>
                          <p><b>Name:</b> " . htmlspecialchars($_POST['contact_name']) . "</p>
                          <p><b>Email:</b> " . htmlspecialchars($_POST['contact_email']) . "</p>
                          <p><b>Message:</b><br>" . nl2br(htmlspecialchars($_POST['message'])) . "</p>";

        $mail->send();
        
        // Success redirect
        header("Location: index.php?status=success#contact");
        exit();
        
    } catch (Exception $e) {
        // Error redirect
        header("Location: index.php?status=error#contact");
        exit();
    }
}
