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
  <!-- Order History Styles -->
  <link rel="stylesheet" href="css/order-history-styles.css">
</header>

<!-- MAIN CONTENT -->
<main>
  <div class="order-history-container">
    <!-- Order History Header -->
    <div class="order-history-header">
      <h1 class="order-history-title">Order History</h1>
      <p class="order-history-subtitle">Track your past orders and purchases</p>
    </div>

    <?php if (isset($_SESSION['id'])): ?>
      <!-- User is logged in - show order history -->
      <div class="order-history-content">
        <div class="order-history-card">
          
          <!-- Orders will be displayed here -->
          <div class="empty-orders">
            <div class="empty-orders-icon">
              <ion-icon name="receipt-outline"></ion-icon>
            </div>
            <h3 class="empty-orders-title">No Orders Found</h3>
            <p class="empty-orders-subtitle">You haven't placed any orders yet. Start shopping to see your order history here.</p>
            <a href="index.php" class="btn-start-shopping">Start Shopping</a>
          </div>

          <!-- Future implementation: Order list will go here -->
          <!-- 
          <div class="order-item">
            <div class="order-header">
              <div class="order-info">
                <h4>Order #12345</h4>
                <p class="order-date">Placed on March 15, 2024</p>
              </div>
              <div class="order-status">
                <span class="status-badge status-delivered">Delivered</span>
                <p class="order-total">$89.99</p>
              </div>
            </div>
            <div class="order-footer">
              <p class="order-details">3 items • Delivered to: 123 Main St, City</p>
            </div>
          </div>
          -->

        </div>
      </div>

    <?php else: ?>
      <!-- User is not logged in -->
      <div class="login-required">
        <div class="login-required-card">
          <div class="login-required-icon">
            <ion-icon name="person-outline"></ion-icon>
          </div>
          <h3 class="login-required-title">Login Required</h3>
          <p class="login-required-subtitle">
            Please login to view your order history and track your purchases.
          </p>
          <div class="login-buttons">
            <a href="login.php?redirect=order_history.php" class="btn-login">Login</a>
            <a href="signup.php" class="btn-signup">Sign Up</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</main>

<!-- Order History JavaScript -->
<script src="js/order-history.js"></script>
<?php require_once './includes/footer.php'; ?>
