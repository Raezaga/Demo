<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure paths are correct based on your GitHub folder named 'PHPMailer'
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // --- HIGH SECURITY SMTP SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzokit@gmail.com'; 
        $mail->Password   = 'wvyjortggzdidxqa'; // Your verified App Password
        
        // SWITCHING TO SSL/PORT 465 (More stable for Render)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;                                    

        // Email Identity
        $mail->setFrom('renzokit@gmail.com', 'Portfolio Contact');
        $mail->addAddress('renzokit@gmail.com'); 
        $mail->addReplyTo($_POST['contact_email'], $_POST['contact_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Inquiry from " . htmlspecialchars($_POST['contact_name']);
        $mail->Body    = "<h3>New Portfolio Message</h3>
                          <p><b>Name:</b> " . htmlspecialchars($_POST['contact_name']) . "</p>
                          <p><b>Email:</b> " . htmlspecialchars($_POST['contact_email']) . "</p>
                          <p><b>Message:</b><br>" . nl2br(htmlspecialchars($_POST['message'])) . "</p>";

        $mail->send();
        header("Location: index.php?status=success#contact");
        exit();
        
    } catch (Exception $e) {
        // This will help you see the EXACT error in your Render logs
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        header("Location: index.php?status=error#contact");
        exit();
    }
}
