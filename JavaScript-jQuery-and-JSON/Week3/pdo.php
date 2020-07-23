<?php 

$hostname = "localhost";
$dbname   = "misc";
$username = "root";
$password = "";

try {
  $pdo = new PDO("mysql:host=".$hostname.";dbname=".$dbname, $username, $password);
  // Set PDO Error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e) {
  echo $e->getMessage();
}

?>
