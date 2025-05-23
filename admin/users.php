
<?php
    include_once('./includes/headerNav.php');
    include_once('./includes/restriction.php');
    if(!(isset($_SESSION['logged-in']))){
      header("Location:login.php?unauthorizedAccess");
    }
 ?>

<h1>Users</h1>
<hr>
<?php
  include "includes/config.php";

        /* define how much data to show in a page from database*/
        $limit = 4;
        if(isset($_GET['page'])){
          $page = $_GET['page'];
          switch($page){
            case 1: $sn = 0; break;
            case 2: $sn = 4;break;
            case 3: $sn = 8; break;
            case 4: $sn = 12; break;
            case 5: $sn = 16; break;
            case 6: $sn = 20; break;
          }
        }else{
          $page = 1;
          switch($page){
            case 1: $sn = 0; break;
            case 2: $sn = 4;break;
            case 3: $sn = 8; break;
            case 4: $sn = 12; break;
            case 5: $sn = 16; break;
            case 6: $sn = 20; break;
          }
        }
        //define from which row to start extracting data from database
        $offset = ($page - 1) * $limit;

        // Validate offset and limit
        $offset = InputValidator::validateInt($offset, 0);
        $limit = InputValidator::validateInt($limit, 1, 100);

        if ($offset === false) $offset = 0;
        if ($limit === false) $limit = 4;

        // Use prepared statement to prevent SQL injection
        $sql = "SELECT * FROM customer LIMIT ?, ?";
        $result = $secureDB->select($sql, [$offset, $limit], 'ii');
if ($result && $result->num_rows > 0) { ?>

    <div class="table-cont">
    <table class="table">
  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Name</th>
      <th scope="col">Phone</th>
      <th scope="col">Address</th>
      <th scope="col">Role</th>
      <th scope="col">Edit</th>
      <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
<?php
// output data of each row
while($row = $result->fetch_assoc()) {
    $sn = $sn+1;
?>
    <tr>
      <th scope="row"><?php echo $sn ?></th>
      <td><?php echo $row["customer_fname"] ?></td>
      <td><?php echo $row["customer_phone"] ?></td>
      <td scope="row"><?php echo $row["customer_address"] ?></td>
      <td><?php echo $row["customer_role"] ?></td>
      <td>
        <a class="fn_link" href="update-user.php?id=<?php echo $row["customer_id"] ?>">
        <i class='fa fa-edit'></i>
        </a>
      </td>
      <td scope="row">
        <a class="fn_link" href="remove-user.php?id=<?php echo $row["customer_id"] ?>">
          <i class='fa fa-trash'></i>
        </a>
      </td>
    </tr>

<?php }}else { echo "0 results"; }
             $conn->close();
             ?>

</table>
</div>

<!--Pagination-->
<?php
    include "includes/config.php";
    // Pagination btn using php with active effects
    $sql1 = "SELECT * FROM customer";
    $result1 = mysqli_query($conn, $sql1) or die("Query Failed.");

    if(mysqli_num_rows($result1) > 0){
        $total_products = mysqli_num_rows($result1);
        $total_page = ceil($total_products / $limit);
?>
    <nav aria-label="..." style="margin-left: 10px;">
      <ul class="pagination pagination-sm">


<?php
        for($i=1; $i<=$total_page; $i++){
            //important this is for active effects that denote in which page you are in current position
            if($page==$i) {
                $active = "active";
            } else {
                $active = "";
            }
        ?>
        <li class="page-item">
            <a class="page-link <?php echo $active; // page number ?>" href="users.php?page=<?php echo $i; // page number ?>">
            <?php echo $i; // page number ?>
            </a>
        </li>
        <?php }} ?>

      </ul>
    </nav>
