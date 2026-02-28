<?php
// Silent mode: No browser errors to interfere with our JS "success" word
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMH Hux'; 
    $toEmail = 'renzokit@gmail.com'; 
    
    $name    = htmlspecialchars($_POST['contact_name']);
    $email   = htmlspecialchars($_POST['contact_email']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    $data = [
        'from'     => 'Portfolio <onboarding@resend.dev>',
        'to'       => [$toEmail],
        'subject'  => 'Portfolio Message from ' . $name,
        'html'     => "<strong>From:</strong> $name ($email)<br><br><strong>Message:</strong><br>$message",
        'reply_to' => $email
    ];

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

    // Only output the raw word 'success' so JS can read it easily
    if ($httpCode === 200 || $httpCode === 201) {
        echo "success"; 
    } else {
        echo "error";
    }
    exit();
}
