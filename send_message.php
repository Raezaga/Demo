<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Paths for your 'PHPMailer' folder in GitHub
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
        
        // Use the REAL account that owns the App Password
        $mail->Username   = 'renzokit@gmail.com'; 
        $mail->Password   = 'wvyjortggzdidxqa'; // No spaces for better reliability
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Identity (The 'From' address MUST match the 'Username' email)
        $mail->setFrom('renzokit@gmail.com', 'Portfolio Contact');
        
        // Destination (Sending the contact form data to yourself)
        $mail->addAddress('renzokit@gmail.com'); 

        // Optional: Let you reply directly to the person who filled out the form
        $mail->addReplyTo($_POST['contact_email'], $_POST['contact_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Message from " . htmlspecialchars($_POST['contact_name']);
        $mail->Body    = "<h3>New Inquiry from Portfolio</h3>
                          <p><b>Name:</b> " . htmlspecialchars($_POST['contact_name']) . "</p>
                          <p><b>Email:</b> " . htmlspecialchars($_POST['contact_email']) . "</p>
                          <p><b>Message:</b><br>" . nl2br(htmlspecialchars($_POST['message'])) . "</p>";

        $mail->send();
        
        // Back to index with success pop-up
        header("Location: index.php?status=success#contact");
        exit();
        
    } catch (Exception $e) {
        // Log the error to your Render dashboard if it fails
        error_log("Mailer Error: " . $mail->ErrorInfo);
        header("Location: index.php?status=error#contact");
        exit();
    }
}
