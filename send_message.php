<?php
// Prevent PHP from outputting errors that might break the JS response
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. CONFIGURATION
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMHHux'; 
    $toEmail = 'renzokit@gmail.com'; 
    
    // 2. DATA COLLECTION
    $name    = htmlspecialchars($_POST['contact_name']);
    $email   = htmlspecialchars($_POST['contact_email']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    $data = [
        'from'     => 'Portfolio <onboarding@resend.dev>',
        'to'       => [$toEmail],
        'subject'  => 'New Message from ' . $name,
        'html'     => "<strong>From:</strong> $name ($email)<br><br><strong>Message:</strong><br>$message",
        'reply_to' => $email
    ];

    // 3. THE API CALL
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false 
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 4. THE JAVASCRIPT-FRIENDLY RESPONSE
    // Instead of header(), we just echo the result
    if ($httpCode === 200 || $httpCode === 201) {
        echo "success"; 
    } else {
        echo "error";
    }
    exit();
}
