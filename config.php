<?php
$host = "YOUR_SUPABASE_HOST";
$port = "5432";
$dbname = "postgres";
$username = "postgres";
$password = "YOUR_SUPABASE_PASSWORD";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
