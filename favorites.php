<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
include_once('./includes/config.php');
include_once('./includes/header-favorites.php'); // Use this header if it exists

$customer_id = $_SESSION['id'];
$favorites = [];

// Fetch favorite product IDs
$query = "SELECT product_id FROM favorites WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$product_ids = [];
while ($row = $result->fetch_assoc()) {
    $product_ids[] = $row['product_id'];
}
$stmt->close();

if (!empty($product_ids)) {
    // Fetch product details
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));
    $query = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites</title>
    <link rel="stylesheet" href="css/style-prefix.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">My Favorite Products</h2>
    <?php if (empty($favorites)): ?>
        <div class="alert alert-info">You have not added any products to your favorites yet.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($favorites as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="admin/upload/<?php echo htmlspecialchars($product['product_img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                            <p class="card-text">Price: $<?php echo htmlspecialchars($product['product_price']); ?></p>
                            <a href="viewdetail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html> 