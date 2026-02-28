<?php
// 1. Enable error logging so you can see issues in Render's logs
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep off to not break the redirect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. YOUR CONFIGURATION
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMH Hux'; 
    $toEmail = 'renzokit@gmail.com'; 
    
    // 3. DATA COLLECTION
    $name    = isset($_POST['contact_name']) ? htmlspecialchars($_POST['contact_name']) : 'Anonymous';
    $email   = isset($_POST['contact_email']) ? htmlspecialchars($_POST['contact_email']) : 'No Email';
    $message = isset($_POST['message']) ? nl2br(htmlspecialchars($_POST['message'])) : 'No Message';

    $data = [
        'from'     => 'Portfolio <onboarding@resend.dev>',
        'to'       => [$toEmail],
        'subject'  => 'New Message from ' . $name,
        'html'     => "<strong>From:</strong> $name ($email)<br><br><strong>Message:</strong><br>$message",
        'reply_to' => $email
    ];

    // 4. THE API CALL
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false // Added to ensure Render doesn't block the SSL handshake
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 5. THE REDIRECT (This is what your index.php is looking for)
    if ($httpCode === 200 || $httpCode === 201) {
        // Successful Send
        header("Location: index.php?status=success#contact");
    } else {
        // Failed Send - Log error to Render Console
        error_log("Resend Failed. Code: $httpCode. Response: $response");
        header("Location: index.php?status=error#contact");
    }
    exit();
}
