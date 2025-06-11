<?php include_once('./includes/headerNav.php'); ?>

<div class="overlay" data-overlay></div>

<header>
  <?php require_once './includes/topheadactions.php'; ?>
  <?php require_once './includes/desktopnav.php' ?>
  <?php require_once './includes/mobilenav.php'; ?>
</header>

<main>
  <div class="product-container">
    <div class="container">
      <div class="product-box">
        <div class="product-main" style="text-align: center; padding: 50px 20px;">
          <h1 style="font-size: 2.5em; margin-bottom: 20px; color: #333;">Coming Soon!</h1>
          <div style="max-width: 600px; margin: 0 auto;">
            <p style="font-size: 1.2em; color: #666; margin-bottom: 30px;">
              We're working hard to bring you this feature. Please check back soon!
            </p>
            <a href="./index.php" class="modern-slide-btn" style="display: inline-block; text-decoration: none;">
              Return to Homepage
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once './includes/footer.php'; ?> 