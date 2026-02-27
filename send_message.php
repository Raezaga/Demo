<?php
include "config.php"; // Uses your existing DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Collect and Clean Data
    $name    = htmlspecialchars(trim($_POST['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['contact_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    // 2. Validation
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit;
    }

    try {
        // 3. Prepare SQL
        $sql = "INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (:name, :email, :subject, :message)";
        
        $stmt = $pdo->prepare($sql);
        
        // 4. Execute
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);

        // 5. Success!
        header("Location: index.php?status=success#contact");
        exit;

    } catch (PDOException $e) {
        // If DB fails, send error status
        header("Location: index.php?status=error#contact");
        exit;
    }

} else {
    header("Location: index.php");
    exit;
}
?>
