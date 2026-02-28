<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $company = htmlspecialchars($_POST['company']);
    $review = htmlspecialchars($_POST['review']);

    try {
        // 1. Database Insert
        $stmt = $pdo->prepare("INSERT INTO reviews (name, company, review) VALUES (?, ?, ?)");
        $stmt->execute([$name, $company, $review]);

        // 2. Notification Logic
        $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMHhux'; 
        
        $data = [
            'from'    => 'Portfolio <onboarding@resend.dev>', // MUST be this for free tier
            'to'      => ['renzokit@gmail.com'], 
            'subject' => '🚨 New Review: ' . $name,
            'html'    => "<strong>$name</strong> from <strong>$company</strong> just left a review.<br><br>Content: $review"
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
            // This bypasses SSL issues on some local servers
            CURLOPT_SSL_VERIFYPEER => false 
        ]);

        curl_exec($ch);
        curl_close($ch);

        // Redirect back to index
        header("Location: index.php?status=success#reviews");
        exit();

    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: index.php?status=error#reviews");
    }
}
