<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

$pid = 1;
$sql = "SELECT * FROM tbltourpackages WHERE PackageId=:pid";
$query = $dbh->prepare($sql);
$query->bindParam(':pid', $pid, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$ratingSql = "SELECT AVG(Rating) as avg_rating FROM tblreviews WHERE PackageId=:pid";
$ratingQuery = $dbh->prepare($ratingSql);
$ratingQuery->bindParam(':pid', $pid, PDO::PARAM_STR);
$ratingQuery->execute();
$avgRatingData = $ratingQuery->fetch(PDO::FETCH_OBJ);
$dynamicAvgRating = round($avgRatingData->avg_rating, 1);

if(!$dynamicAvgRating && !empty($results[0]->rating)) {
    $dynamicAvgRating = floatval($results[0]->rating);
}

echo "Success.";
?>
