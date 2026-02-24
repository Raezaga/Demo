<?php

// Get database URL from Render environment variables
$db_url = getenv("DATABASE_URL");

if (!$db_url) {
    die("DATABASE_URL not found in environment variables.");
}

// Parse the connection string
$connection = parse_url($db_url);

$host = $connection["host"] ?? null;
$port = $connection["port"] ?? 5432;
$dbname = ltrim($connection["path"], "/");
$user = $connection["user"] ?? null;
$password = $connection["pass"] ?? null;

// Validate values
if (!$host || !$user || !$dbname) {
    die("Invalid DATABASE_URL format.");
}

try {

    // Create PostgreSQL connection using PDO
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

    die("Database connection failed: " . $e->getMessage());

}

?>
