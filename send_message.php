<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // MATCHING YOUR INDEX.PHP NAMES:
    $name    = htmlspecialchars(trim($_POST['contact_name'] ?? ''));
    $email   = filter_var(trim($_POST['contact_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validation (Removing 'subject' check)
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'renzokit@gmail.com';
        $mail->Password   = 'ctuxosamfwlbldyw'; // Space-free password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('renzokit@gmail.com', 'Portfolio System');
        $mail->addAddress('renzokit@gmail.com');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Message from $name"; // Generic subject
        $mail->Body    = "
            <div style='font-family: Arial; padding: 20px; border: 1px solid #eee;'>
                <h2 style='color: #facc15;'>New Inquiry</h2>
                <p><strong>From:</strong> $name ($email)</p>
                <hr>
                <p><strong>Message:</strong></p>
                <p style='white-space: pre-wrap;'>$message</p>
            </div>";

        $mail->send();

        // Optional: Also save to DB
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
        } catch (Exception $e) { /* DB backup failed */ }

        header("Location: index.php?status=success#contact");
    } catch (Exception $e) {
        header("Location: index.php?status=error#contact");
    }
    exit;
}
