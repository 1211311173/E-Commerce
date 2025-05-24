<?php
    include_once('./includes/headerNav.php');
    include_once('./includes/restriction.php');

    //this will provide previous user value before updating
    include "includes/config.php";
    $sql = "SELECT * FROM customer where customer_id={$_GET['id']}";
    $result = $conn->query($sql);
    // output data of each row
    $row = $result->fetch_assoc();
    $_SESSION['previous_name'] = $row['customer_fname'];
    $_SESSION['previous_phone'] = $row['customer_phone'];
    $_SESSION['previous_address'] = $row['customer_address'];
    $_SESSION['previous_role'] = $row['customer_role'];
    $conn->close();
 ?>
 <head>
     <style>
        .content-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
         .update{
            border: 1px solid black;
            width: 60%;
            padding: 25px;
            border-radius: 16px;
            background-color: #f1f1f1;
         }

     </style>
 </head>

 <div class="content-box">
    <div class="update">
<h1>Update User Details</h1>
    <form class="row g-3" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
      <div class="col-md-6">
        <label for="inputEmail4" class="form-label">Name</label>
        <input
          name="name"
          type="text"
          class="form-control"
          value="<?php echo $_SESSION['previous_name']?>"
        />
      </div>
      <div class="col-md-6">
        <label for="inputPassword4" class="form-label">Phone</label>
        <input
          type="number"
          name="phone"
          class="form-control"
          value="<?php echo $_SESSION['previous_phone'] ?>"
        />
      </div>
      <div class="col-12">
        <label for="inputAddress" class="form-label">Address</label>
        <input
          type="text"
          name="address"
          class="form-control"
          placeholder="1234 Main St"
          value="<?php echo $_SESSION['previous_address'] ?>"
        />
      </div>
      <div class="col-md-4">
        <label for="inputState" class="form-label">Role</label>
        <select id="role_update" name="role" class="form-select">
          <?php
       if($_SESSION['previous_role']=='admin'){
           ?>
          <option value="admin" selected>Admin</option>
          <option value="normal">Normal</option>
          <?php  } else{?>
          <option value="admin">Admin</option>
          <option value="normal" selected>Normal</option>
          <?php } ?>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary" name="update">
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

    // Validate the customer ID
    $customer_id = InputValidator::validateInt($_GET['id'], 1);

    if ($customer_id === false) {
        header("Location:users.php?error=invalid_id");
        exit();
    }

    // Validate and sanitize input data
    $name = InputValidator::sanitizeString($_POST['name'], 100);
    $phone = InputValidator::validatePhone($_POST['phone']);
    $address = InputValidator::sanitizeString($_POST['address'], 255);
    $role = InputValidator::sanitizeString($_POST['role'], 20);

    if ($phone === false || empty($name) || empty($address) || empty($role)) {
        header("Location:update-user.php?id={$customer_id}&error=invalid_data");
        exit();
    }

    // Validate role (only allow specific values)
    $allowed_roles = ['admin', 'normal', 'customer'];
    if (!in_array($role, $allowed_roles)) {
        header("Location:update-user.php?id={$customer_id}&error=invalid_role");
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $sql1 = "UPDATE customer SET customer_fname = ?, customer_phone = ?, customer_address = ?, customer_role = ? WHERE customer_id = ?";
    $result = $secureDB->update($sql1, [$name, $phone, $address, $role, $customer_id], 'ssssi');

    if ($result) {
        header("Location:update-user.php?id={$customer_id}&succesfullyUpdated");
    } else {
        header("Location:update-user.php?id={$customer_id}&error=update_failed");
    }
    exit();
   }
?>