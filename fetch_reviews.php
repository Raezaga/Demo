<?php
include "config.php";

$stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
$reviews = $stmt->fetchAll();

foreach ($reviews as $row) {
?>

<div class="review-item">
    <h4>
        <?php echo htmlspecialchars($row['name']); ?>
        <span style="color:#facc15;">
            (<?php echo htmlspecialchars($row['company']); ?>)
        </span>
    </h4>

    <p>"<?php echo htmlspecialchars($row['review']); ?>"</p>
</div>

<?php } ?>
