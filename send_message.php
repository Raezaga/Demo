<?php
// 1. Load the PHPMailer classes (based on your 'PHPMailer' folder)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Include your database config if you want to save messages there too
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Collect and Sanitize data from your form
    $name    = htmlspecialchars(trim($_POST['contact_name'] ?? ''));
    $email   = filter_var(trim($_POST['contact_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Quick validation
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit;
    }

    // 3. Setup PHPMailer with your credentials
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzokit@gmail.com';       // Your Gmail
        $mail->Password   = 'ctuxosamfwlbldyw';         // Your App Password (spaces removed)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('renzokit@gmail.com', 'Portfolio Contact');
        $mail->addAddress('renzokit@gmail.com');        // Send the email to yourself
        $mail->addReplyTo($email, $name);              // So you can reply directly to the sender

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Inquiry: $subject";
        $mail->Body    = "
            <div style='font-family: sans-serif; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #333;'>New Message Received</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <hr>
                <p><strong>Message:</strong></p>
                <p style='white-space: pre-wrap;'>$message</p>
            </div>";

        $mail->send();
        
        // 4. Also Save to Database (Optional but recommended)
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
        } catch (Exception $e) { /* DB fail shouldn't stop the success redirect */ }

        header("Location: index.php?status=success#contact");
    } catch (Exception $e) {
        // For debugging online, you can uncomment the next line to see why it failed:
        // echo "Mailer Error: " . $mail->ErrorInfo; exit;
        header("Location: index.php?status=error#contact");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
