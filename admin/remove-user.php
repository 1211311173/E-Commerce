<?php
include "includes/config.php";

// Validate the customer ID
$customer_id = InputValidator::validateInt($_GET['id'], 1);

if ($customer_id === false) {
    header("Location:users.php?error=invalid_id");
    exit();
}

// Use prepared statement to prevent SQL injection
$sql = "DELETE FROM customer WHERE customer_id = ?";
$result = $secureDB->delete($sql, [$customer_id], 'i');

if ($result) {
    header("Location:users.php?succesfullyDeleted");
} else {
    header("Location:users.php?error=delete_failed");
}
exit();
?>
