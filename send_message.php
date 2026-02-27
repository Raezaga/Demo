<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize input to prevent hacking/XSS
    $name    = filter_var(trim($_POST['contact_name']), FILTER_SANITIZE_STRING);
    $email   = filter_var(trim($_POST['contact_email']), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    // 2. Quick Validation
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit;
    }

    // 3. Email Settings
    $to = "renzokit@email.com"; // <--- CHANGE THIS TO YOUR ACTUAL EMAIL
    $email_subject = "New Portfolio Message: $subject";
    
    // 4. Professional Email Body Formatting
    $body = "--- You have a new message from your portfolio ---\n\n";
    $body .= "Name: $name\n";
    $body .= "Email: $email\n";
    $body .= "Subject: $subject\n\n";
    $body .= "Message:\n$message\n\n";
    $body .= "------------------------------------------------";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 5. Send and Redirect
    if (mail($to, $email_subject, $body, $headers)) {
        header("Location: index.php?status=success#contact");
    } else {
        header("Location: index.php?status=error#contact");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>