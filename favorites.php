<?php include_once('./includes/headerNav.php'); ?>

<!-- Check if user is logged in -->
<?php if (!isset($_SESSION['id'])): ?>
    <script>
        window.location.href = 'login.php?redirect=favorites.php';
    </script>
<?php endif; ?>

<div class="overlay" data-overlay></div>

<!-- HEADER -->
<header>
    <!-- Top head action, search etc -->
    <?php require_once './includes/topheadactions.php'; ?>
    <!-- Desktop navigation -->
    <?php require_once './includes/desktopnav.php'; ?>
    <!-- Mobile navigation -->
    <?php require_once './includes/mobilenav.php'; ?>
    <!-- Favorites Styles -->
    <link rel="stylesheet" href="css/favorites-styles.css">
</header>

<!-- MAIN CONTENT -->
<main>
    <?php
    // Get user's favorite products
    if (isset($_SESSION['id'])) {
        include_once('./includes/config.php');
        $customer_id = $_SESSION['id'];

        $sql = "SELECT p.*, f.created_at as favorited_at 
                FROM favorites f 
                JOIN products p ON f.product_id = p.product_id 
                WHERE f.customer_id = ? 
                ORDER BY f.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    ?>

    <div class="favorites-container">
        <!-- Favorites Header -->
        <div class="favorites-header">
            <h1 class="favorites-title">My Favorites</h1>
            <p class="favorites-subtitle">Products you've saved for later</p>
        </div>

        <div class="favorites-content">
            <?php if (isset($result) && $result->num_rows > 0): ?>
                <div class="favorites-grid">
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <div class="favorite-item">
                            <button class="remove-favorite" onclick="removeFromFavorites(<?php echo $product['product_id']; ?>)"
                                title="Remove from favorites">
                                <ion-icon name="heart"></ion-icon>
                            </button>

                            <div class="product-image">
                                <img src="admin/upload/<?php echo $product['product_img']; ?>"
                                    alt="<?php echo htmlspecialchars($product['product_title']); ?>" loading="lazy">
                            </div>

                            <div class="product-info">
                                <h3 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h3>
                                <div class="product-pricing">
                                    <?php if ($product['discounted_price'] && $product['discounted_price'] < $product['product_price']): ?>
                                        <span
                                            class="price discounted">$<?php echo number_format($product['discounted_price'], 2); ?></span>
                                        <span
                                            class="price original">$<?php echo number_format($product['product_price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="favorited-date">Added:
                                    <?php echo date('M j, Y', strtotime($product['favorited_at'])); ?></p>

                                <?php if ($product['product_left'] <= 0): ?>
                                    <p class="out-of-stock">Out of Stock</p>
                                <?php elseif ($product['product_left'] <= 5): ?>
                                    <p class="low-stock">Only <?php echo $product['product_left']; ?> left!</p>
                                <?php endif; ?>
                            </div>

                            <div class="product-actions">
                                <a href="viewdetail.php?id=<?php echo $product['product_id']; ?>&category=<?php echo urlencode($product['product_catag']); ?>"
                                    class="btn-primary">
                                    <ion-icon name="eye-outline"></ion-icon>
                                    View Details
                                </a>

                                <form class="add-to-cart-form" action="manage_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="product_name"
                                        value="<?php echo htmlspecialchars($product['product_title']); ?>">
                                    <input type="hidden" name="product_price"
                                        value="<?php echo $product['discounted_price'] ?: $product['product_price']; ?>">
                                    <input type="hidden" name="product_img" value="<?php echo $product['product_img']; ?>">
                                    <input type="hidden" name="product_category"
                                        value="<?php echo $product['product_catag']; ?>">
                                    <input type="hidden" name="product_qty" value="1">

                                    <button type="button" name="add-to-cart" onclick="addToCartWithNotification(this)" class="btn-secondary">
                                        <ion-icon name="bag-add-outline"></ion-icon>
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-favorites">
                    <div class="empty-favorites-icon">
                        <ion-icon name="heart-outline"></ion-icon>
                    </div>
                    <h3 class="empty-favorites-title">No favorites yet</h3>
                    <p class="empty-favorites-subtitle">Start browsing and add products to your favorites by clicking the
                        heart icon!</p>
                    <a href="index.php" class="btn-start-shopping">
                        Continue Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    function removeFromFavorites(productId) {
        if (confirm('Remove this item from favorites?')) {
            fetch('ajax/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${productId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showToast('Item removed from favorites', 'success');
                        // Reload page to reflect changes
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Failed to remove from favorites', 'error');
                    }
                })
                .catch(error => {
                    showToast('An error occurred while removing from favorites', 'error');
                });
        }
    }

    function showToast(message, type = 'info') {
        // Create a toast notification
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.textContent = message;

        document.body.appendChild(toast);

        // Trigger the show animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 1000);
    }

function addToCartWithNotification(button) {
    const form = button.closest('form');
    const productName = form.querySelector('input[name="product_name"]').value;

    const addToCartInput = document.createElement('input');
    addToCartInput.type = 'hidden';
    addToCartInput.name = 'add_to_cart';
    addToCartInput.value = '1';
    form.appendChild(addToCartInput);

    showToast(productName + ' added to cart!', 'success');

    setTimeout(() => {
        form.submit();
    }, 1000);
}
</script>

<?php require_once './includes/footer.php'; ?>