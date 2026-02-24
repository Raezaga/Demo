<?php

// Get DATABASE_URL from Render environment variables
$db_url = getenv("DATABASE_URL");

if (!$db_url) {
    die("DATABASE_URL not set. Please add it in Render environment variables.");
}

// Parse the connection string
$connection = parse_url($db_url);

$host = $connection["host"] ?? null;
$port = $connection["port"] ?? 5432;
$dbname = ltrim($connection["path"], "/");
$user = $connection["user"] ?? null;
$password = $connection["pass"] ?? null;

// Ensure values exist
if (!$host || !$user || !$dbname) {
    die("Invalid DATABASE_URL format.");
}

// Connect to PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";

$conn = pg_connect($conn_string);

if (!$conn) {
    die("Database connection failed: " . pg_last_error());
}

// Optional: Uncomment to test connection
// echo "Database connected successfully!";

?>
