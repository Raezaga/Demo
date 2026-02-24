<?php

$host = "YOUR_NEON_HOST";
$dbname = "YOUR_NEON_DATABASE";
$user = "YOUR_NEON_USERNAME";
$password = "YOUR_NEON_PASSWORD";
$port = "5432";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password port=$port");

if(!$conn){
    die("Database connection failed.");
}

?>