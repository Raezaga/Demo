<?php
// CRITICAL: Ensure there is NO space or line before the <?php tag
ob_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // FIXED: Space removed from the key string
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMHhux'; 
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
        CURLOPT_SSL_VERIFYPEER => false 
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Clear the buffer to ensure only our status word is sent
    ob_clean(); 

    if ($httpCode === 200 || $httpCode === 201) {
        echo "success"; 
    } else {
        // Logically, we should know why it failed
        echo "error_code_" . $httpCode;
    }
    exit();
}
