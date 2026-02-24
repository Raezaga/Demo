<?php

$db_url = getenv("DATABASE_URL");

if (!$db_url) {
    die("DATABASE_URL not found.");
}

$connection = parse_url($db_url);

$host = $connection["host"];
$port = $connection["port"] ?? 5432;
$dbname = ltrim($connection["path"], "/");
$user = $connection["user"];
$password = $connection["pass"];

try {

    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
