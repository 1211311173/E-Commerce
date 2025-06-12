<?php
//making config as we need this everytime we can just use it through include_once
//1st step for database php connection
$serverName = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "db_ecommerce";
$dbPort = 3306;

//Before we can access data in the MySQL database, we need to be able to connect to the server i.e php
$conn = new mysqli($serverName,$dBUsername,$dBPassword,$dBName, $dbPort);

// Check connection
if(!$conn){
    die("Connection failed: ".$conn->connect_error);
}

// Set charset to prevent character set confusion attacks
$conn->set_charset("utf8");

// Include security functions
require_once __DIR__ . '/security.php';
?>