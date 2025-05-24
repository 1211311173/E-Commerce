<?php
include "includes/config.php";

// Validate the product ID
$product_id = InputValidator::validateInt($_GET['id'], 1);

if ($product_id === false) {
    header("Location:post.php?error=invalid_id");
    exit();
}

// Use prepared statement to prevent SQL injection
$sql = "DELETE FROM products WHERE product_id = ?";
$result = $secureDB->delete($sql, [$product_id], 'i');

if ($result) {
    header("Location:post.php?succesfullyDeleted");
} else {
    header("Location:post.php?error=delete_failed");
}
exit();
?>
