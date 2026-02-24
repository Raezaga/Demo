<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $review = trim($_POST['review'] ?? '');

    if (empty($name) || empty($company) || empty($review)) {
        die("All fields are required.");
    }

    $query = "INSERT INTO reviews (name, company, review)
              VALUES ($1, $2, $3)";

    $result = pg_query_params($conn, $query, array($name, $company, $review));

    if (!$result) {
        die("Insert failed: " . pg_last_error($conn));
    }

    // Redirect back to page after saving
    header("Location: index.php#reviews");
    exit();
}
?>
