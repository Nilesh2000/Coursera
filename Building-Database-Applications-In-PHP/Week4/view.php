<?php 

session_start();

if(!isset($_SESSION['name'])) {
  die("Not logged in");
}

require_once 'pdo.php';

$autos = array();
$stmt = $pdo->query("SELECT * FROM db");

  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $autos[] = $row;
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  
  <title>Nilesh D</title>
</head>
<body>
  <div class="container">

    <h1>Tracking Autos for <?php echo $_SESSION['name'] ?></h1>
    
    <?php
    if(isset($_SESSION['success'])) {
      echo('<p style="color: green" class="col-sm-10 col-sm-offset-2">'.$_SESSION['success']."</p>\n");
      unset($_SESSION['success']);
    }
    
    if(!empty($autos)) : ?>
      <h2>Automobiles</h2>
      <ul>
        <?php foreach($autos as $auto) : ?>
          <li>
            <?php echo $auto['year']; ?> <?php echo $auto['make']; ?> <?php echo $auto['mileage']; ?>
          </li>  
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    
    <p><a href="add.php">Add New</a> | <a href="logout.php">Logout</a>
  </div>
</body>
</html>