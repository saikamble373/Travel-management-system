<?php
session_start();
include('includes/config.php');

header('Content-Type: application/json');

if(strlen($_SESSION['login'])==0) {
    echo json_encode(['status' => 'error', 'msg' => 'Please login to add to wishlist']);
    exit;
}

if(isset($_POST['pid'])) {
    $pid = intval($_POST['pid']);
    $useremail = $_SESSION['login'];

    // Check if already in wishlist
    $checkSql = "SELECT id FROM tblwishlist WHERE PackageId=:pid AND UserEmail=:useremail";
    $query = $dbh->prepare($checkSql);
    $query->bindParam(':pid', $pid, PDO::PARAM_STR);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        // Remove from wishlist
        $delSql = "DELETE FROM tblwishlist WHERE PackageId=:pid AND UserEmail=:useremail";
        $delQuery = $dbh->prepare($delSql);
        $delQuery->bindParam(':pid', $pid, PDO::PARAM_STR);
        $delQuery->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $delQuery->execute();
        
        echo json_encode(['status' => 'success', 'action' => 'removed', 'msg' => 'Removed from wishlist.']);
    } else {
        // Add to wishlist
        $insSql = "INSERT INTO tblwishlist (PackageId, UserEmail) VALUES (:pid, :useremail)";
        $insQuery = $dbh->prepare($insSql);
        $insQuery->bindParam(':pid', $pid, PDO::PARAM_STR);
        $insQuery->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $insQuery->execute();
        
        echo json_encode(['status' => 'success', 'action' => 'added', 'msg' => 'Added to wishlist!']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
}
?>
