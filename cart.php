<?php include_once('./includes/headerNav.php'); ?>

<div class="overlay" data-overlay></div>

<!-- HEADER -->
<header>
  <!-- Top head action, search etc -->
  <?php require_once './includes/topheadactions.php'; ?>
  <!-- Desktop navigation -->
  <?php require_once './includes/desktopnav.php'; ?>
  <!-- Mobile navigation -->
  <?php require_once './includes/mobilenav.php'; ?>
  <!-- Styles -->
  <link rel="stylesheet" href="css/cart-styles.css">
</header>

<!-- MAIN CONTENT -->
<main>
  <div class="cart-container">
    <!-- Cart Header -->
    <div class="cart-header">
      <h1 class="cart-title">Shopping Cart</h1>
      <p class="cart-subtitle">Review your items before checkout</p>
    </div>

    <?php if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])): ?> <!-- Cart Items -->
      <div class="cart-items">
        <?php
        $totalItems = 0;
        $totalPrice = 0;
        foreach ($_SESSION['mycart'] as $key => $item):
          $totalItems += $item['product_qty'];
          $totalPrice += ($item['price'] * $item['product_qty']);
          ?>
          <div class="cart-item">
            <div class="cart-item-content">
              <!-- Product Image -->
              <div class="cart-item-image-container">
                <img class="cart-item-image" src="./admin/upload/<?php echo htmlspecialchars($item['product_img']); ?>"
                  alt="<?php echo htmlspecialchars($item['name']); ?>">
              </div>

              <!-- Product Details -->
              <div class="cart-item-details">
                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                <p class="cart-item-description">Premium quality product with excellent features</p>
              </div>

              <!-- Price -->
              <div class="cart-item-price">
                $<?php echo htmlspecialchars($item['price']); ?>
              </div>

              <!-- Quantity -->
              <div class="cart-item-quantity">
                Qty: <?php echo htmlspecialchars($item['product_qty']); ?>
              </div>

              <!-- Remove Button -->
              <div class="cart-item-remove">
                <form action="manage_cart.php" method="POST">
                  <input type="hidden" name="product_id_to_remove"
                    value="<?php echo htmlspecialchars($item['product_id']); ?>">
                  <button type="submit" name="remove_from_cart" class="btn-remove">
                    Remove
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div> <!-- Cart Summary -->
      <div class="cart-summary">
        <h2 class="cart-summary-title">Order Summary</h2>
        <div class="cart-summary-details">
          <div class="summary-row">
            <span class="summary-label">Items (<?php echo $totalItems; ?>):</span>
            <span class="summary-value">$<?php echo number_format($totalPrice, 2); ?></span>
          </div>
          <div class="summary-row summary-total">
            <span class="summary-label">Total:</span>
            <span class="summary-value">$<?php echo number_format($totalPrice, 2); ?></span>
          </div>
        </div>

        <?php if (!isset($_SESSION['id']) || empty($_SESSION['id'])): ?>
          <div class="login-notice">
            <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 15px; font-size: 0.95rem;">
              <ion-icon name="information-circle-outline" style="margin-right: 5px;"></ion-icon>
              Please login to proceed with checkout
            </p>
          </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" style="text-align: center;">
          <?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])): ?>
            <button type="submit" name="proceed_to_checkout_action" class="btn-checkout">
              Proceed to Checkout
            </button>
          <?php else: ?>
            <a href="login.php?redirect=cart.php" class="btn-checkout" style="text-decoration: none; display: block;">
              Login to Checkout
            </a>
          <?php endif; ?>
        </form>
      </div>

    <?php else: ?>
      <!-- Empty Cart -->
      <div class="empty-cart">
        <div class="empty-cart-icon">
          <ion-icon name="bag-outline"></ion-icon>
        </div>
        <h2 class="empty-cart-message">Your cart is empty</h2>
        <p class="empty-cart-subtitle">Looks like you haven't added any items to your cart yet</p>
        <a href="index.php" class="btn-continue-shopping">Continue Shopping</a>
      </div>
    <?php endif; ?>
  </div>
</main>

<script src="https://js.stripe.com/v3/"></script>
<?php require_once './includes/footer.php'; ?>