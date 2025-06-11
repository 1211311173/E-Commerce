<?php
    include_once('./includes/headerNav.php');
    include_once('./includes/restriction.php');
 ?>
    <h1>PRODUCT CATEGORIES</h1>
<hr>


<div class="table-cont">
    <table class="table">

  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Category</th>
      <th scope="col">No. of Posts</th>
    </tr>
  </thead>

  <tbody class="table-group-divider">

<?php
  include "includes/config.php";

  // todo: work with those categories catagory
$catagory_list = ['men', 'women', 'kids', 'electronics', 'home', 'sports', 'beauty', 'furniture', 'books', 'stationary', 'grocery', 'other'];

for($i=0; $i<sizeof($catagory_list); $i++){
    $sn = $i+1;
    $catagory = $catagory_list[$i];

    // Validate and sanitize category
    $safe_category = InputValidator::sanitizeString($catagory, 50);
    if (empty($safe_category)) {
        continue; // Skip invalid categories
    }

    // Get product count for this category
    $product_count = get_product_count_by_category($safe_category);
?>
<tr>
      <th scope="row"><?php echo $sn ?></th>
      <td><?php echo $safe_category ?></td>
      <td><?php echo $product_count ?></td>
</tr>
<?php } //loop end ?>

  </tbody>
</table>
</div>
<br>

