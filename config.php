<?php
$host = "aws-1-ap-northeast-1.pooler.supabase.com";
$port = "5432";
$dbname = "postgres";
$username = "postgres.mwuwjkhvypzvjdrpmtqp";
$password = "Ohana!1210012";

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
