<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure these paths match your PHPMailer folder name on Render
require 'PHPMailer-7.0.2/src/Exception.php';
require 'PHPMailer-7.0.2/src/PHPMailer.php';
require 'PHPMailer-7.0.2/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzloiokit.dev@gmail.com'; // Your sender email
        $mail->Password   = 'wvyj ortg gzdi dxqa';      // Your new App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Identity
        $mail->setFrom('renzloiokit.dev@gmail.com', 'Portfolio Contact');
        
        // RECEIVER EMAIL (Updated as per your request)
        $mail->addAddress('renzokit@gmail.com'); 

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Message from " . htmlspecialchars($_POST['contact_name']);
        $mail->Body    = "<h3>New Contact Request</h3>
                          <p><b>Name:</b> " . htmlspecialchars($_POST['contact_name']) . "</p>
                          <p><b>Email:</b> " . htmlspecialchars($_POST['contact_email']) . "</p>
                          <p><b>Message:</b><br>" . nl2br(htmlspecialchars($_POST['message'])) . "</p>";

        $mail->send();
        
        // Redirect back to index with success message
        header("Location: index.php?status=success#contact");
        exit();
        
    } catch (Exception $e) {
        // If it fails, we go back with an error status
        header("Location: index.php?status=error#contact");
        exit();
    }
}
