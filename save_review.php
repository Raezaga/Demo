<?php
include "config.php";

$name = $_POST['name'];
$company = $_POST['company'];
$review = $_POST['review'];

$stmt = $pdo->prepare("INSERT INTO reviews (name, company, review)
                      VALUES (:name, :company, :review)");

$stmt->execute([
    ':name' => $name,
    ':company' => $company,
    ':review' => $review
]);

header("Location: index.php#reviews");
exit();
?>
