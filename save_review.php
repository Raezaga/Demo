<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$name = $_POST['name'] ?? null;
$company = $_POST['company'] ?? null;
$review = $_POST['review'] ?? null;

if(!$name || !$company || !$review){
    die("Missing form data");
}

$stmt = $pdo->prepare("INSERT INTO reviews (name, company, review)
VALUES (:name, :company, :review)");

$execute = $stmt->execute([
    ':name' => $name,
    ':company' => $company,
    ':review' => $review
]);

if($execute){
    echo "✅ Insert Successful";
} else {
    echo "❌ Insert Failed";
}

} else {
echo "Form not submitted";
}
?>
