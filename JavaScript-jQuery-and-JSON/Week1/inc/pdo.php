<?php 

  $hostname = "localhost";
  $dbname   = "misc";
  $username = "root";
  $password = "";

  try {
    $pdo = new PDO("mysql:host=".$hostname.";dbname=".$dbname, $username, $password);
    // Set PDO Error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }catch(PDOException $e) {
    echo $e->getMessage();
  }

?>
