<?php

$db_url = getenv("DATABASE_URL");

if (!$db_url) {
    die("DATABASE_URL not found in environment variables.");
}

$connection = parse_url($db_url);

$host = $connection["host"];
$user = $connection["user"];
$password = $connection["pass"];
$dbname = ltrim($connection["path"], "/");
$port = $connection["port"] ?? 5432;

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if (!$conn) {
    die("Database connection failed.");
}

?>
