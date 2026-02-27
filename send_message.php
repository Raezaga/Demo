<?php
// Debugging version
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Collect data
    $name    = $_POST['contact_name'] ?? null;
    $email   = $_POST['contact_email'] ?? null;
    $subject = $_POST['subject'] ?? null;
    $message = $_POST['message'] ?? null;

    // Validation logic check
    if (empty($name)) { die("Error: Name field is empty or name attribute is wrong."); }
    if (empty($message)) { die("Error: Message field is empty or name attribute is wrong."); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { die("Error: Email is invalid or empty. Received: " . htmlspecialchars($email)); }

    // If it gets here, the data is fine. Let's try to send.
    $to = "renzloiokit.dev@email.com"; // Your email
    $headers = "From: $to\r\nReply-To: $email";
    
    if (mail($to, "Test: $subject", $message, $headers)) {
        header("Location: index.php?status=success#contact");
    } else {
        die("Error: The server's mail() function failed. If you are on XAMPP/Localhost, this is normal.");
    }
}
?>
