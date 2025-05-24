<?php session_start();
include_once 'includes/config.php';
//  all functions
require_once 'functions/functions.php';

//run whenever this file is used no need of isset or any condition to get website image footer etc
$sql5 = "SELECT * FROM  settings;";
$result5 = $conn->query($sql5);
$row5 = $result5->fetch_assoc();
$_SESSION['web-name'] = $row5['website_name'];
$_SESSION['web-img'] = $row5['website_logo'];
$_SESSION['web-footer'] = $row5['website_footer'];

// Initialize error and success message arrays
$errors = array();
$success_message = '';

//1st step(i.e connection) done through config file
if (isset($_POST['login'])) {

  if (empty($_POST['email'])) {
    $errors[] = "Email is required";
  }

  if (empty($_POST['pwd'])) {
    $errors[] = "Password is required";
  }

  // Validate and sanitize input
  $email = InputValidator::validateEmail($_POST['email']);
  $password = $_POST['pwd'];

  if (!$email && !empty($_POST['email'])) {
    $errors[] = "Please enter a valid email address";
  } else if (!empty($errors)) {
    // Don't proceed if we already have validation errors
  } else {
    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM customer WHERE customer_email = ?";
    $result = $secureDB->select($sql, [$email], 's');

    if ($result->num_rows == 1) { //if any one data found go inside it
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['customer_pwd'])) {
        //session will be created only if users email and passwords matched
        session_start();
        $_SESSION['id'] = $row['customer_id'];
        $_SESSION['customer_role'] = $row['customer_role'];

        header("location:profile.php?id={$_SESSION['id']}");
        // put exit after a redirect as header() does not stop execution
        exit;
      } else {
        $errors[] = "Incorrect password. Please try again.";
      }
    } else {
      if ($_POST['email']) { //it means it will run if email field is filled
        $errors[] = "Account not found. Please sign up first.";
      }
    }
  }
}//end of 1st ifstatement
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/customer-login.css">    
  <title>Login</title>
</head>

<body>

  <?php
  if (!(isset($_SESSION['id']))) {
    ?>    <form action="<?php echo $_SERVER['PHP_SELF']; ?> " method="post">
      <div class="logo-box">
        <img src="admin/upload/<?php echo $_SESSION['web-img']; ?>" alt="logo" width="200px" />
      </div>
      
      <?php 
      // Display error messages
      if (!empty($errors)) {
        echo '<div class="error-message">';
        foreach ($errors as $error) {
          echo htmlspecialchars($error) . '<br>';
        }
        echo '</div>';
      }
      
      // Display success messages
      if (!empty($success_message)) {
        echo '<div class="success-message">' . htmlspecialchars($success_message) . '</div>';
      }
      ?>
      
      <div class="row mb-3">
        <!-- <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label> -->
        <div class="col-sm-12">
          <input id="inputEmail" name="email" type="email" class="form-control" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
        </div>
      </div>
      <div class="row mb-3">
        <!-- <label for="inputPassword3" class="col-sm-2 col-form-label"
          >Password</label
        > -->
        <div class="col-sm-12">
          <input id="inputPassword" name="pwd" type="password" class="form-control" placeholder="Password" />
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-10">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheck1" />
            <label class="form-check-label" for="gridCheck1">
              Remember Me
            </label>
          </div>
        </div>
      </div>
      <div style="float: right">
        <a href="./signup.php" class="btn btn-primary" id="signup-btn">
          Sign up
        </a>
        <button type="submit" class="btn btn-primary" name="login">
          Sign in
        </button>
      </div>
    </form>
  <?php } ?>

</body>

</html>