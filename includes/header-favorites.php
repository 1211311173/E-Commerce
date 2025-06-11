<?php
// Get favorites count for the current user
$favorites_count = 0;
if (isset($_SESSION['id'])) {
    $customer_id = $_SESSION['id'];
    $query = "SELECT COUNT(*) as count FROM favorites WHERE customer_id = ?";
    $result = $secureDB->select($query, [$customer_id], 'i');
    if ($result && $row = $result->fetch_assoc()) {
        $favorites_count = $row['count'];
    }
}
?>

<button class="action-btn heart-icon" data-product-id="0" title="Favorites">
    <a href="favorites.php">
        <ion-icon name="heart-outline"></ion-icon>
    </a>
</button> 