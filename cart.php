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
  <div class="product-container">
    <div class="container">
      <table>
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])): ?>
            <?php foreach ($_SESSION['mycart'] as $key => $item): ?>
              <tr>
                <td>
                  <img class="cart-product-image" src="./admin/upload/<?php echo htmlspecialchars($item['product_img']); ?>"
                    alt="<?php echo htmlspecialchars($item['name']); ?>">
                </td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo "$" . htmlspecialchars($item['price']); ?></td>
                <td><?php echo htmlspecialchars($item['product_qty']); ?></td>
                <td>
                  <form action="manage_cart.php" method="POST">
                    <input type="hidden" name="product_id_to_remove"
                      value="<?php echo htmlspecialchars($item['product_id']); ?>">
                    <button type="submit" name="remove_from_cart" class="btn-remove">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">No items available in cart.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])): ?>
    <div class="child-register-btn">
      <form action="checkout.php" method="POST">
        <button type="submit" name="proceed_to_checkout_action" id="proceed-to-checkout-btn" style="color: #FFFFFF;">
          Proceed To CheckOut
        </button>
      </form>
    </div>
  <?php endif; ?>
</main>

<script src="https://js.stripe.com/v3/"></script>
<?php require_once './includes/footer.php'; ?>