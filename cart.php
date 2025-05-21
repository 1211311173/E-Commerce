<?php include_once('./includes/headerNav.php'); ?>
<div class="overlay" data-overlay></div>
<!--
  - HEADER
  -->
<header>
  <!-- top head action, search etc in php -->
  <!-- inc/topheadactions.php -->
  <?php require_once './includes/topheadactions.php'; ?>
  <!-- desktop navigation -->
  <!-- inc/desktopnav.php -->
  <?php require_once './includes/desktopnav.php' ?>
  <!-- mobile nav in php -->
  <!-- inc/mobilenav.php -->
  <?php require_once './includes/mobilenav.php'; ?>
  <!-- style -->
  <link rel="stylesheet" href="css/cart-styles.css">
</header>
<!--
  - MAIN
  -->
<main>
  <div class="product-container">
    <div class="container">
      <!--
        - SIDEBAR
       -->
      <table>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
        </tr>
        <?php
        if (isset($_SESSION['mycart'])) {
          foreach ($_SESSION['mycart'] as $value) {
            ?>
            <tr>
              <td>
                <img class="cart-product-image" src="./admin/upload/<?php echo $value['product_img'] ?>" alt="">
              </td>
              <td><?php echo $value['name']; ?></td>
              <td><?php echo "$" . $value['price']; ?></td>
              <td><?php echo $value['product_qty']; ?></td>
            </tr>
            <?php
          }
        } else {
          ?>
          <tr>
            <td colspan='4'>No item available in cart</td>
          </tr>
          <?php
        }
        ?>
      </table>
    </div>
  </div>
  </div>
  <?php
  if (isset($_SESSION['mycart'])) {
    ?>
    <div class="child-register-btn">
      <p> <a href="checkout.php" style="color:#FFFFFF">Proceed To CheckOut</a>
      </p>
    </div>
    <?php
  }
  ?>
</main>
<script src="https://js.stripe.com/v3/"></script>
<?php require_once './includes/footer.php'; ?>