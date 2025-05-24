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

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM products WHERE product_catag = ?";
    $result = $secureDB->select($sql, [$safe_category], 's');
    $total_post = $result ? $result->num_rows : 0;

// output data of each row
while($row = $result->fetch_assoc()) {
?>
<tr>
      <th scope="row"><?php echo $sn ?></th>
      <td><?php echo $row["product_catag"] ?></td>
      <td><?php echo $total_post?></td>
</tr>
   <?php break; ?>
<?php } }//loop end
?>

  </tbody>
</table>
</div>
<br>

