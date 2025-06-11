<?php
session_start();
include "includes/config.php";

// Validate the product ID
$product_id = InputValidator::validateInt($_GET['id'], 1);

if ($product_id === false) {
    header("Location:post.php?error=invalid_id");
    exit();
}

// Get product title before deletion for logging
$getProductSql = "SELECT product_title FROM products WHERE product_id = ?";
$productResult = $secureDB->select($getProductSql, [$product_id], 'i');
$productTitle = 'Unknown Product';
if ($productResult && $productResult->num_rows > 0) {
    $productRow = $productResult->fetch_assoc();
    $productTitle = $productRow['product_title'];
}

// Use prepared statement to prevent SQL injection
$sql = "DELETE FROM products WHERE product_id = ?";
$result = $secureDB->delete($sql, [$product_id], 'i');

if ($result) {
    // Log product deletion
    if (isset($_SESSION['id'])) {
        logProductDelete($_SESSION['id'], $product_id, $productTitle);
    }
    header("Location:post.php?succesfullyDeleted");
} else {
    header("Location:post.php?error=delete_failed");
}
exit();
?>
