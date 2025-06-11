<?php
//this all are input validity functions that will provide true/false for error finding 
//in case condition not matched--it executes during signup 

function emptyInputSignup($name, $email,  $number,$address, $pwd,$rpwd){
    $result;
    if (empty($name) ||empty($email) ||empty($number) ||empty($address) ||empty($pwd) ||empty($rpwd) ) {
                 $result = true;   
    }
     else{
                 $result = false;
     }
                 return $result;
}

function invalidPhone($number){
    $result;
    if (strlen($number) < 11) { 
                 $result = true;   
    }
     else{
                 $result = false;
     }
                 return $result;
}

function invalidEmail($email){
    $result;
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {//this return true if var is proper email(built in func)
                 $result = true;   
    }
     else{
                 $result = false;
     }
                 return $result;
}


function pwdMatch($pwd,$rpwd) {
    $result;
    if ($pwd !== $rpwd) {
                 $result = false;   
    }
     else{
                 $result = true;
     }
                 return $result;
}

function createUser($name,$email,$address,$pwd,$number){
    // Get database connection from singleton instance
    global $conn;
    
    // Hash the password using PHP's built-in password_hash function
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    //using prepare statement for preventing injection
    $sql = $conn->prepare("INSERT INTO customer (customer_fname,customer_email,customer_pwd,customer_phone,customer_address) VALUES (?,?,?,?,?)");
 
    $sql->bind_param('sssss',$name,$email,$hashedPwd,$number,$address);
    $sql->execute();
  
    //after saving user data to database redirecting user to add page
    header("location: ../index.php?userSuccessfullycreated!loginNow");
 
    //close prepare statement
    $sql->close();
}
    
