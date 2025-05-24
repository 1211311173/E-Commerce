<?php
    include_once('./includes/headerNav.php');
    include_once('./includes/restriction.php');

    //this will provide previous user value before updating
    include "includes/config.php";

    // Validate the product ID
    $product_id = InputValidator::validateInt($_GET['id'], 1);

    if ($product_id === false) {
        header("Location:post.php?error=invalid_id");
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $result = $secureDB->select($sql, [$product_id], 'i');

    if (!$result || $result->num_rows === 0) {
        header("Location:post.php?error=product_not_found");
        exit();
    }

    // output data of each row
    $row = $result->fetch_assoc();
    $_SESSION['previous_title'] = $row['product_title'];
    $_SESSION['previous_desc'] = $row['product_desc'];
    $_SESSION['previous_catag'] = $row['product_catag'];
    $_SESSION['previous_price'] = $row['product_price'];
    $_SESSION['previous_discount'] = $row['discounted_price'];
    $_SESSION['previous_no'] = $row['product_left'];
    $_SESSION['previous_img'] = $row['product_img'];
    $conn->close();
 ?>
 <head>
     <style>
        .content-box-post {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
         .update{
            border: 1px solid black;
            width: 80%;
            padding: 25px;
            border-radius: 16px;
            background-color: #f1f1f1;
         }
     </style>
 </head>
<div class="content-box-post">


 <div class="update">
     <h5>Edit post here</h5>
     <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="row g-3">
      <div class="col-12">
        <label for="inputAddress" class="form-label">Title</label>
        <input
          class="form-control"
          type="text"
          name="title"
          value="<?php echo $_SESSION['previous_title'] ?>"
        />
      </div>
      <div class="col-md-6">
        <label for="inputEmail4" class="form-label">Price</label>
        <input
          class="form-control"
          type="number"
          name="price"
          value="<?php echo $_SESSION['previous_price'] ?>"
        />
      </div>
      <div class="col-md-6">
        <label for="inputPassword4" class="form-label">Discount</label>
        <input
          class="form-control"
          type="number"
          name="discount"
          value="<?php echo $_SESSION['previous_discount'] ?>"
        />
      </div>
      <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label"
          >Description</label
        >
        <textarea class="form-control" rows="3" name="desc">
        <?php echo $_SESSION['previous_desc'] ?>
      </textarea
        >
      </div>
      <div class="col-md-6">
        <label for="inputCity" class="form-label">No. of Items</label>
        <input
          class="form-control"
          type="number"
          name="noofitem"
          value="<?php echo $_SESSION['previous_no'] ?>"
        />
      </div>
      <div class="col-md-6">
        <label for="inputState" class="form-label">Category</label>
        <select
          name="catag"
          value="<?php echo $_SESSION['previous_catag'] ?>"
          class="form-select"
        >
          <option value="all" selected>All</option>
          <option value="men">Men</option>
          <option value="women">Women</option>
        </select>
      </div>
      <div class="col-12">
        <label for="inputAddress" class="form-label">Image</label>
        <input
          type="file"
          name="newimg"
          class="form-control"
          required="required"
        />
      </div>
      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="flexRadioDefault"
          id="flexRadioDefault2"
        />
        <label class="form-check-label" for="flexRadioDefault2">
          Available
        </label>
      </div>
      <div class="col-12">
        <button type="submit" name="update" class="btn btn-primary">
          Update
        </button>
      </div>
    </form>
 </div>

</div>


<?php
   if(isset($_POST['update'])){
    //below sql will update user details inside sql table when update is clicked
    include "includes/config.php";

    // Validate the product ID again
    $product_id = InputValidator::validateInt($_GET['id'], 1);

    if ($product_id === false) {
        header("Location:post.php?error=invalid_id");
        exit();
    }

    // Validate and sanitize input data
    $title = InputValidator::sanitizeString($_POST['title'], 255);
    $category = InputValidator::sanitizeString($_POST['catag'], 50);
    $price = InputValidator::validateFloat($_POST['price'], 0);
    $discount = InputValidator::validateFloat($_POST['discount'], 0);
    $description = InputValidator::sanitizeString($_POST['desc'], 1000);
    $image = InputValidator::sanitizeString($_POST['newimg'], 255);
    $noofitem = InputValidator::validateInt($_POST['noofitem'], 0);

    if ($price === false || $discount === false || $noofitem === false) {
        header("Location:update-post.php?id={$product_id}&error=invalid_data");
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $sql1 = "UPDATE products SET product_title = ?, product_catag = ?, product_price = ?, discounted_price = ?, product_desc = ?, product_img = ?, product_left = ? WHERE product_id = ?";
    $result = $secureDB->update($sql1, [$title, $category, $price, $discount, $description, $image, $noofitem, $product_id], 'ssddssii');

    if ($result) {
        header("Location:post.php?succesfullyUpdated");
    } else {
        header("Location:update-post.php?id={$product_id}&error=update_failed");
    }
    exit();
   }
?>