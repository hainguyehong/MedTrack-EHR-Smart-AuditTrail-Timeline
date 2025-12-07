<?php 
session_start();
$host = "160-191-244-99.cprapid.com";
$user = "aerinidv_khanhhuyen";
$password = "khanhhuyen2412";
$db = "aerinidv_pms_db";
// $host = "localhost";
// $user = "root";
// $password = "";
// $db = "pms_db";
try {

  $con = new PDO("mysql:dbname=$db;port=3306;host=$host", 
  	$user, $password);
  // set the PDO error mode to exception
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: ".
   $e->getMessage();
  echo $e->getTraceAsString();
  exit;
}

//24 minutes default idle time
// if(isset($_SESSION['ABC'])) {
// 	unset($_SESSION['ABC']);
// }

?>