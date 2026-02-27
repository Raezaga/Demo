<?php
// Prevent any accidental output before the redirect
ob_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Modern way to sanitize (FILTER_SANITIZE_STRING is replaced by htmlspecialchars)
    // We use the ?? operator to provide an empty string if the key is missing
    $name    = htmlspecialchars(trim($_POST['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['contact_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    // 2. Validation
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit;
    }

    // 3. Email Settings
    $to = "renzloiokit.dev@email.com"; // YOUR EMAIL HERE
    $email_subject = "Portfolio Inquiry: " . ($subject ?: "General Inquiry");
    
    $body = "--- New Message from Portfolio ---\n\n";
    $body .= "Name: $name\n";
    $body .= "Email: $email\n";
    $body .= "Subject: $subject\n\n";
    $body .= "Message:\n$message\n\n";
    $body .= "----------------------------------";

    $headers = "From: $to\r\n"; // Using $to as 'From' to improve deliverability
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 4. Send and Redirect
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

ob_end_flush();
?>
