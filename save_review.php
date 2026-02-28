<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $company = htmlspecialchars($_POST['company']);
    $review = htmlspecialchars($_POST['review']);

    try {
        // 1. Database (Status defaults to 'pending' in Supabase)
        $stmt = $pdo->prepare("INSERT INTO reviews (name, company, review) VALUES (?, ?, ?)");
        $stmt->execute([$name, $company, $review]);

        // 2. Notification (Using the logic from your working contact form)
        $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMHhux'; 
        $data = [
            'from'    => 'Portfolio <onboarding@resend.dev>',
            'to'      => ['renzokit@gmail.com'],
            'subject' => '🚨 New Review Pending: ' . $name,
            'html'    => "<h3>New Review for Approval</h3>
                          <p><strong>From:</strong> $name ($company)</p>
                          <p><strong>Content:</strong> $review</p>
                          <br><a href='https://demo-det4.onrender.com/admin.php'>Approve in Admin Panel</a>"
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey, 'Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false 
        ]);
        
        curl_exec($ch);
        curl_close($ch);

        // Tell JavaScript we are done
        echo "success";
        exit();

    } catch (Exception $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
