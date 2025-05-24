<?php include_once('./includes/headerNav.php'); ?>
<?php require_once './includes/topheadactions.php'; ?>
<?php require_once './includes/mobilenav.php'; ?>

<!-- get tables data from db -->
<header>
  <!-- top head action, search etc in php -->
  <?php require_once './includes/topheadactions.php'; ?>
  <!-- desktop navigation -->
  <?php require_once './includes/desktopnav.php' ?>
  <!-- mobile nav in php -->
  <?php require_once './includes/mobilenav.php'; ?>
</header>

<!-- check for table and then get specific data from table -->
<?php
// get values from url
$product_ID = $_GET['id'];
$product_category = $_GET['category'];

$product_name = '';
$product_price = '';

if ($product_category == "deal_of_day") {
  $item = get_deal_of_day_by_id($product_ID);
} else {
  // get specfic item from table
  $item = get_product($product_ID);
}
// get user reviews
// $user_reviews = get_user_reviews();
?>

<div class="overlay" data-overlay></div>

<!-- CATEGORY SIDE BAR MOBILE MENU -->
<!-- Category side bar  -->
<div class="product-container category-side-bar-container">
  <div class="container">
    <?php require_once 'includes/categorysidebar.php' ?>
    <?php
    // Get product data
    $row = mysqli_fetch_assoc($item);
    ?>
    <!-- product card   -->
    <div class="content">
      <form action="manage_cart.php" method="post" class='view-form'>
        <!-- product details container -->
        <div class="product_deatail_container">
          <!-- image is kept hidden for submission -->
          <input type="hidden" name="product_img" value=<?php echo $row['product_img'] ?>>
          <!-- getting image from here with magnify functionality -->
          <?php include_once './product.php'; ?>
          <div class="product_detail_box">
            <h3 class="product-detail-title">
              <?php echo strtoupper($row['product_title']) ?>
            </h3>
            <div class="prouduct_information">
              <div class="product_description">
                <div class="product_title"><strong>Name:</strong></div>
                <div class="product_detail">
                  <?php
                  $product_name = $row['product_title'];
                  $product_price = $row['discounted_price'];
                  echo ucfirst($product_name) ?>
                  <input type="hidden" name='product_name' id='product_name' value="<?php echo $product_name; ?>">
                </div>
              </div>
              <div class="product_description">
                <div class="product_title"><strong>Price:</strong></div>
                <div class="product_detail">
                  <div class="price-box">
                    <p class="price">$<?php echo $product_price; ?></p>
                    <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
                    <input type="hidden" id="product_identity" name="product_id"
                      value="<?php echo $row['product_id']; ?>">
                    <input type="hidden" name="product_category" value="<?php echo $product_category; ?>">
                    <del>$<?php echo $row['product_price']; ?></del>
                  </div>
                </div>
              </div>
              <div class="product_description">
                <div class="product_title"><strong>Rating:</strong></div>
                <div class="product_detail">
                  <div class="showcase-rating">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>
              </div>
            </div>
            <div class="product_counter_box">
              <!-- product counter buttons -->
              <div class="product_counter_btn_box">
                <button type="button" class="btn_product_increment">+</button>
                <input class="input_product_quantity" type="number" style="width: 50px" max="7" min="1" value="1"
                  name="product_qty" id="p_qty" />
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?> " />
                <button type="button" class="btn_product_decrement">-</button>
              </div>              <!-- submit -->
              <div class="buy-and-cart-btn">
                <button type="submit" name="add_to_cart" class="btn_product_cart">
                  Add to Cart
                </button>
                <?php if (isset($_SESSION['id'])): ?>
                  <button type="submit" formaction="checkout.php" name="buy_now_action" class="btn_but_product">Buy</button>
                <?php else: ?>
                  <button type="button" onclick="redirectToLogin()" class="btn_but_product">Buy</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <script src="js/viewdetail.js"></script>
  </div>
  <?php require_once './includes/footer.php'; ?>