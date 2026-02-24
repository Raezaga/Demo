<?php

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("DATABASE_URL is not set.");
}

$db = parse_url($databaseUrl);

$host = $db["host"];
$port = $db["port"] ?? "5432";
$dbname = ltrim($db["path"], "/");
$username = $db["user"];
$password = $db["pass"];

try {

    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {

    die("Database connection failed: " . $e->getMessage());

}

?>
