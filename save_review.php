<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $company = htmlspecialchars($_POST['company']);
    $review = htmlspecialchars($_POST['review']);

    try {
        // 1. SAVE TO DATABASE (Status defaults to 'pending' automatically)
        $stmt = $pdo->prepare("INSERT INTO reviews (name, company, review) VALUES (?, ?, ?)");
        $stmt->execute([$name, $company, $review]);

        // 2. SEND NOTIFICATION TO YOUR EMAIL
        $apiKey = 're_ixbpDK5A_6achZzFNn68Eith8vbJMHhux'; // Your Resend Key
        $toEmail = 'renzokit@gmail.com'; 

        $data = [
            'from'    => 'Admin <onboarding@resend.dev>',
            'to'      => [$toEmail],
            'subject' => 'New Review Pending Approval: ' . $name,
            'html'    => "
                <h3>New Feedback Received</h3>
                <p><strong>Client:</strong> $name ($company)</p>
                <p><strong>Message:</strong> $review</p>
                <hr>
                <a href='https://yourdomain.com/admin.php' style='padding:10px; background:#facc15; color:black; text-decoration:none; font-weight:bold;'>Go to Admin Dashboard to Approve</a>
            "
        ];

        // API Call
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
        curl_exec($ch);
        curl_close($ch);

        // 3. REDIRECT BACK
        header("Location: index.php?status=review_submitted#reviews");
        exit();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
