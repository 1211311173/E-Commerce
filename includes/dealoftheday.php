<?php
// Get all deals of the day
$deals_of_the_day = get_deal_of_day();
?>
<!-- Deal of the day -->
<div class="product-featured">
  <h2 class="title">Deal of the day</h2>

  <div class="deal-slider-container">
    <div class="deal-slider-wrapper">
      <!-- display data from db -->
      <?php
      while ($row = mysqli_fetch_assoc($deals_of_the_day)) {
        $sold = (int)$row['sold_deal'];
        $available = (int)$row['available_deal'];
        $total = $sold + $available;
        $percent = $total > 0 ? ($sold / $total) * 100 : 0;
        ?>

        <div class="deal-slide">
          <div class="showcase-container">
            <div class="showcase">
              <div class="showcase-banner">
                <img src="./admin/upload/<?php echo htmlspecialchars($row['deal_image']); ?>"
                  alt="<?php echo htmlspecialchars($row['deal_title']); ?>" class="showcase-img" />
              </div>

              <div class="showcase-content">
                <div class="showcase-rating">
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                </div>

                <a href="./viewdetail.php?id=<?php echo $row['deal_id'] ?>&category=<?php echo "deal_of_day" ?>">
                  <h3 class="showcase-title">
                    <?php echo htmlspecialchars($row['deal_title']); ?>
                  </h3>
                </a>

                <p class="showcase-desc">
                  <?php echo htmlspecialchars($row['deal_description']) ?>
                </p>

                <div class="price-box">
                  <p class="price">$ <?php echo $row['deal_discounted_price'] ?> </p>

                  <del>$<?php echo  $row['deal_net_price']?></del>
                </div>

                <button class="add-cart-btn">Premium</button>

                <div class="showcase-status">
                  <div class="wrapper">
                    <p>already sold: <b><?php echo $row['sold_deal'] ?></b></p>
                    <p>available: <b><?php echo $row['available_deal'] ?></b></p>
                  </div>

                  <div class="showcase-status-bar">
                    <div class="progress-bar" style="width: <?php echo $percent; ?>%"></div>
                  </div>
                </div>

                <div class="countdown-box">
                  <p class="countdown-desc">Hurry Up! Offer ends in:</p>

                  <div class="countdown" data-end-time="<?php echo $row['deal_end_time']; ?>">
                    <div class="countdown-content">
                      <p class="display-number days">00</p>
                      <p class="display-text">Days</p>
                    </div>

                    <div class="countdown-content">
                      <p class="display-number hours">00</p>
                      <p class="display-text">Hours</p>
                    </div>

                    <div class="countdown-content">
                      <p class="display-number minutes">00</p>
                      <p class="display-text">Min</p>
                    </div>

                    <div class="countdown-content">
                      <p class="display-number seconds">00</p>
                      <p class="display-text">Sec</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
      }
      ?>
    </div>

    <!-- Navigation Controls -->
    <button class="deal-nav-btn prev" aria-label="Previous deal">
      <span>‹</span>
    </button>
    <button class="deal-nav-btn next" aria-label="Next deal">
      <span>›</span>
    </button>

    <!-- Dots Indicator -->
    <div class="deal-dots-container"></div>
  </div>
</div>