<?php
// Ensure no whitespace exists before the opening tag
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your verified Resend API Key from the screenshot
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMH Hux'; 
    $toEmail = 'renzokit@gmail.com'; 
    
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

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        // Required for some shared hosting/Render environments
        CURLOPT_SSL_VERIFYPEER => false 
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // This ensures the JavaScript sees 'success' even if there are hidden warnings
    if ($httpCode === 200 || $httpCode === 201) {
        echo "success"; 
    } else {
        // You can check your browser console to see this error detail
        echo "error: " . $response;
    }
    exit();
}
