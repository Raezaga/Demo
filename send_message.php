<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. CONFIGURATION (Using your new Resend API Key)
    $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMH Hux'; 
    $toEmail = 'renzokit@gmail.com'; 
    
    // 2. COLLECT DATA FROM YOUR FORM
    $name    = htmlspecialchars($_POST['contact_name']);
    $email   = htmlspecialchars($_POST['contact_email']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    // 3. THE EMAIL PAYLOAD
    // Note: 'from' must stay as 'onboarding@resend.dev' until you add a custom domain.
    $data = [
        'from'     => 'Portfolio <onboarding@resend.dev>',
        'to'       => [$toEmail],
        'subject'  => 'New Message from ' . $name,
        'html'     => "<strong>From:</strong> $name ($email)<br><br><strong>Message:</strong><br>$message",
        'reply_to' => $email
    ];

    // 4. SEND VIA CURL (This uses Port 443, which Render does NOT block)
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 5. REDIRECT BACK TO YOUR PORTFOLIO
    if ($httpCode === 200 || $httpCode === 201) {
        // Success! This will trigger your "Success" pop-up
        header("Location: index.php?status=success#contact");
    } else {
        // Log the error for you to see in Render's dashboard
        error_log("Resend API Error: " . $response);
        header("Location: index.php?status=error#contact");
    }
    exit();
}
