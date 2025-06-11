<?php
// get best sellers
$best_sellers = get_best_sellers();

// Get categories
$categories = get_categories();
$clothes = get_clothes_category();
$footwears = get_footwear_category();
$jewelries = get_jewelry_category();
$perfumes = get_perfume_category();
$cosmetics = get_cosmetics_category();
$glasses = get_glasses_category();
$bags = get_bags_category();

?>


<!--
          - SIDEBAR
        -->

<div class="sidebar has-scrollbar" data-mobile-menu>
  <div class="sidebar-category">
    <div class="sidebar-top">
      <h2 class="sidebar-title">Category</h2>

      <button class="sidebar-close-btn" data-mobile-menu-close-btn>
        <ion-icon name="close-outline"></ion-icon>
      </button>
    </div>

    <ul class="sidebar-menu-category-list">
      <!-- get data from categories table -->
      <?php
      while ($row = mysqli_fetch_assoc($categories)) {
      ?>
      
        <li class="sidebar-menu-category">
          <button class="sidebar-accordion-menu" data-accordion-btn>
            <div class="menu-title-flex">
              <img src="./images/icons/<?php echo $row['img'] ?>" alt="clothes" width="20" height="20" class="menu-title-img" />

              <p class="menu-title"><?php echo $row['name'] ?></p>
            </div>

            <div>
              <ion-icon name="add-outline" class="add-icon"></ion-icon>
              <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
            </div>
          </button>

          <ul class="sidebar-submenu-category-list" data-accordion>
            <!-- get category data from table -->
            <!-- clothes category -->
            <?php
            if ($row['name'] == "Clothes" || $row['name'] == "clothes") {
              while ($clothrow = mysqli_fetch_assoc($clothes)) {
                // Map the category name to match database subcategories
                $category_name = $clothrow['cloth_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>    
                </li>
            <?php
              }
            }
            ?>
            <!-- Foot wear  category --> 
            <?php
            if ($row['name'] == "Footwear" || $row['name'] == "footwear") {
              while ($footwearrow = mysqli_fetch_assoc($footwears)) {
                // Map the category name to match database subcategories
                $category_name = $footwearrow['footwear_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>    
                </li>
            <?php
              }
            }
            ?>
            <!--  -->
            <!-- jewelry  category -->
            <?php
            if ($row['name'] == "Jewelry" || $row['name'] == "jewelry") {
              while ($jewelryrow = mysqli_fetch_assoc($jewelries)) {
                // Map the category name to match database subcategories
                $category_name = $jewelryrow['Jewelry_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>                      
                </li>
            <?php
              }
            }
            ?>
            <!--  -->
            <!-- perfume  category -->
            <?php
            if ($row['name'] == "Perfume" || $row['name'] == "perfume") {
              while ($perfumesrow = mysqli_fetch_assoc($perfumes)) {
                // Map the category name to match database subcategories
                $category_name = $perfumesrow['perfume_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>                  
                </li>
            <?php
              }
            }
            ?>
            <!--  -->
            <!--  -->
            <!-- cosmetics  category -->
            <?php
            if ($row['name'] == "Cosmetics" || $row['name'] == "cosmetics") {
              while ($cosmeticsrow = mysqli_fetch_assoc($cosmetics)) {
                // Map the category name to match database subcategories
                $category_name = $cosmeticsrow['cosmetics_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>                  
                </li>
            <?php
              }
            }
            ?>
            <!--  -->
            <!--  -->
            <!-- cosmetics  category -->
            <?php
            if ($row['name'] == "Glasses" || $row['name'] == "glasses") {
              while ($glassesrow = mysqli_fetch_assoc($glasses)) {
                // Map the category name to match database subcategories
                $category_name = $glassesrow['glasses_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>                  
                </li>
            <?php
              }
            }
            ?>
            <!--  -->
            <!--  -->
            <!-- cosmetics  category -->
            <?php
            if ($row['name'] == "Bags" || $row['name'] == "bags") {
              while ($bagsrow = mysqli_fetch_assoc($bags)) {
                // Map the category name to match database subcategories
                $category_name = $bagsrow['bags_category_name'];
                $product_count = get_product_count_by_category($category_name);
            ?>
                <li class="sidebar-submenu-category">
                  <form class="search-form" method="post" action="./search.php">
                    <input type="hidden" name="search" value="<?php echo $category_name ?>" />
                        <button type="submit" name="submit" class="sidebar-submenu-title">
                          <p class="product-name">
                            <?php echo $category_name ?> (<?php echo $product_count ?>)
                          </p>
                        </button>
                  </form>                  
                </li>
            <?php
              }
            }
            ?>
            <!--  -->


          </ul>
        </li>



      <?php
      }
      ?>
      <!--  -->






      <!--  -->
    </ul>
  </div>
  <!-- Best Sellers -->
  <div class="product-showcase">
    <h3 class="showcase-heading">best sellers</h3>

    <div class="showcase-wrapper">
      <div class="showcase-container">
        <!-- display data form best seller table -->
        <?php
        while ($row = mysqli_fetch_assoc($best_sellers)) {

        ?>

          <div class="showcase">
            <!-- sending two variables in url -->
            <a href="./viewdetail.php?id=<?php
                                          echo $row['product_id'];
                                          ?>&category=<?php
                                echo $row['category_id'];
                                ?>" class="showcase-img-box">
              <img src="./admin/upload/<?php
                                                  echo $row['product_img']
                                                  ?>" alt="best sellers img" width="75" height="75" class="showcase-img" />
            </a>

            <div class="showcase-content">
              <!-- sending two variables in url -->
              <a href="./viewdetail.php?id=<?php
                                            echo $row['product_id'];
                                            ?>&category=<?php
                                echo $row['category_id'];
                                ?>">
                <h4 class="showcase-title">
                  <?php echo $row['product_title'] ?>
                </h4>
              </a>

              <div class="showcase-rating">
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
              </div>

              <div class="price-box">
                <del>$<?php
                      echo $row['product_price'] ?></del>
                <p class="price">$<?php
                                  echo $row['product_price']
                                  ?></p>
              </div>
            </div>
          </div>

        <?php

        }
        ?>

      </div>
    </div>
  </div>
</div>