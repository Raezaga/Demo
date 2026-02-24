<?php
include "config.php";

$name = $_POST['name'];
$company = $_POST['company'];
$review = $_POST['review'];

$query = "INSERT INTO reviews (name, company, review)
          VALUES ($1, $2, $3)";

$result = pg_query_params($conn, $query, array($name, $company, $review));

if($result){
    echo "success";
}else{
    echo "error";
}

?>