<?php
require_once 'includes/session_helper.php';

$total_cart_items = getCartItemCount();

// Get total order count for logged-in user
$total_orders = 0;
if (isLoggedIn()) {
    include_once('./includes/config.php');
    $customer_id = getCurrentUserId();
    $order_count_query = "SELECT COUNT(*) as total_orders FROM orders WHERE customer_id = ?";
    $stmt = $conn->prepare($order_count_query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_orders = $row['total_orders'];
    }
    $stmt->close();
}

$favorites_count = 0;
if (isLoggedIn()) {
    $customer_id = getCurrentUserId();
    $query = "SELECT COUNT(*) as count FROM favorites WHERE customer_id = ?";
    $result = $secureDB->select($query, [$customer_id], 'i');
    if ($result && $row = $result->fetch_assoc()) {
        $favorites_count = $row['count'];
    }
}
?>
<div class="header-top">
  <div class="container">
    <ul class="header-social-container">
      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-facebook"></ion-icon>
        </a>
      </li>

      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-twitter"></ion-icon>
        </a>
      </li>

      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-instagram"></ion-icon>
        </a>
      </li>

      <li>
        <a href="#" class="social-link">
          <ion-icon name="logo-linkedin"></ion-icon>
        </a>
      </li>
    </ul>

    <div class="header-alert-news">
      <p>
        <b>Free Shipping</b>
        This Week Order Over - $55
      </p>
    </div>

    <div class="header-top-actions">
      <select name="currency">
        <option value="usd">USD &dollar;</option>
        <option value="eur">EUR &euro;</option>
      </select>

      <select name="language">
        <option value="en-US">English</option>
        <option value="es-ES">Espa&ntilde;ol</option>
        <option value="fr">Fran&ccedil;ais</option>
      </select>
    </div>
  </div>
</div>

<div class="header-main">
  <div class="container">
    <!-- logo section -->
    <a href="./index.php?id=<?php echo isLoggedIn() ? getCurrentUserId() : 'unknown'; ?>"
      class="header-logo" style="color: hsl(0, 0%, 13%);">

      <h1 style="text-align: center;">

        <img src="admin/upload/<?php echo getSessionValue('web-img', 'default-logo.png'); ?>" alt="logo" width="200px">

      </h1>

    </a>

    <!-- search input -->
    <div class="header-search-container">
      <form class="search-form" method="post" action="./search.php">
        <input type="search" name="search" class="search-field" placeholder="Enter your product name..." required
          oninvalid="this.setCustomValidity('Enter product name...')" oninput="this.setCustomValidity('')" />

        <button class="search-btn" type="submit" name="submit">
          <ion-icon name="search-outline"></ion-icon>
        </button>
      </form>
    </div>
    <div class="header-user-actions"> <!-- Favourite Counter -->
      <button class="action-btn heart-icon" data-product-id="0" title="Favorites">
        <a href="favorites.php">
          <ion-icon name="heart-outline" title=""></ion-icon>
        </a>
        <span class="count"><?php echo $favorites_count; ?></span>
      </button>

      <!-- Cart Button -->
      <button class="action-btn" title="Shopping Cart">
        <a href="./cart.php">
          <ion-icon name="bag-handle-outline" title=""></ion-icon>
        </a>
        <span class="count">
          <?php echo $total_cart_items; ?>
        </span>
      </button>
        <!-- Order History Button -->
      <?php if (isLoggedIn()): ?>
        <button class="action-btn" title="Order History">
          <a href="./order_history.php">
            <ion-icon name="receipt-outline" title=""></ion-icon>
          </a>
          <span class="count">
            <?php echo $total_orders; ?>
          </span>
        </button>
      <?php endif; ?>

      <!-- Login/Logout Button -->
      <?php if (isLoggedIn()): ?>
        <button id="lg-btn" class="action-btn" title="Logout">
          <a href="logout.php" id="a" role="button">
            <ion-icon name="log-out-outline" title=""></ion-icon>
          </a>
        </button>
        <!-- TODO: This script doesnot execute: Work o this, Directly logout user -->
        <script src="./js/logout.js"></script>
      <?php else: ?>
        <!-- Login Button -->
        <button class="action-btn" title="Login">
          <a href="./login.php" id="a">
            <ion-icon name="person-outline" title=""></ion-icon>
          </a>
        </button>
      <?php endif; ?>
    </div>
  </div>
</div>