<?php
$total_cart_items = 0;
if (isset($_SESSION['mycart'])) {
  $total_cart_items = count($_SESSION['mycart']);
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
    <a href="./index.php?id=<?php echo (isset($_SESSION['customer_name'])) ? $_SESSION['id'] : 'unknown'; ?>"
      class="header-logo" style="color: hsl(0, 0%, 13%);">

      <h1 style="text-align: center;">

        <img src="admin/upload/<?php echo $_SESSION['web-img']; ?>" alt="logo" width="200px">

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
    </div>    <div class="header-user-actions">      <!-- Favourite Counter -->
      <button class="action-btn" title="Favorites">
        <ion-icon name="heart-outline" title=""></ion-icon>
        <span class="count">0</span>
      </button>

      <!-- Cart Button -->
      <button class="action-btn" title="Shopping Cart">
        <a href="./cart.php">
          <ion-icon name="bag-handle-outline" title=""></ion-icon>
        </a>
        <span class="count">
          <?php echo $total_cart_items; ?>
        </span>
      </button>      <!-- Order History Button -->
      <?php if (isset($_SESSION['id'])): ?>
        <button class="action-btn" title="Order History">
          <a href="./order-history.php">
            <ion-icon name="receipt-outline" title=""></ion-icon>
          </a>
        </button>
      <?php endif; ?>

      <!-- Login/Logout Button -->
      <?php if (isset($_SESSION['id'])) { ?>
        <button id="lg-btn" class="action-btn" title="Logout">
          <a href="logout.php" id="a" role="button">
            <ion-icon name="log-out-outline" title=""></ion-icon>
          </a>
        </button>
        <!-- TODO: This script doesnot execute: Work o this, Directly logout user -->
        <script src="./js/logout.js"></script>
      <?php } else { ?>
        <!-- Login Button -->
        <button class="action-btn" title="Login">
          <a href="./login.php" id="a">
            <ion-icon name="person-outline" title=""></ion-icon>
          </a>
        </button>
      <?php } ?>
    </div>
  </div>
</div>