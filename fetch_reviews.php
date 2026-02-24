<?php
include "config.php";

$result = pg_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");

while($row = pg_fetch_assoc($result)){
?>

<div class="review-item">
    <h4><?php echo $row['name']; ?>
        <span style="color:#facc15;">
            (<?php echo $row['company']; ?>)
        </span>
    </h4>

    <p style="margin-top:10px;color:#cbd5e1;">
        "<?php echo $row['review']; ?>"
    </p>
</div>

<?php
}
?>