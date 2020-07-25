<?php

  require_once 'inc/pdo.php';

  // Check for a GET parameter
  if( !isset($_GET['name']) || strlen($_GET['name']) == 0 ) {
    die("Name paramter missing.");
  }

  if( strpos($_GET['name'], '@') === false ) {
      die('Name parameter is wrong');
  }

  // If user wishes to logout
  if(isset($_POST['logout'])) {
    header('Location: index.php');
    return;
  }

  $name = htmlentities($_GET['name']);

  $status        = false;
  $status_colour = "red";

  if( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {

    if(strlen($_POST['make']) == 0) {
      $status = "Make is required";
    } 
    
    else if( !is_numeric($_POST['mileage']) || !is_numeric($_POST['year']) ) {
      $status = "Mileage and year must be numeric";
    }
    
    else {
      $make    = htmlentities($_POST['make']);
      $year    = htmlentities($_POST['year']);
      $mileage = htmlentities($_POST['mileage']);

      $sql  = "INSERT INTO db(make, year, mileage)
               VALUES(:make, :year, :mileage)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':make'    => $make,
        ':year'    => $year,
        ':mileage' => $mileage,
      ]);
      
      $status        = 'Record inserted';
      $status_colour = 'green';
    }
  }

  $autos = array();
  $sql   = "SELECT * FROM db";
  $stmt  = $pdo->query($sql);
  $autos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <title>Nilesh D</title>
</head>

<body>
  <div class="container">
    <h1>Tracking Autos For <?= $name; ?></h1>
    <br><br>

    <?php 
      if($status != false) {
        echo('<p style="color: ' .$status_colour. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
      }
    ?>

    <form method="POST">

      <div class="form-group row">
        <label for="make" class="col-form-label col-sm-2">Make :</label>
        <div class="col-sm-5">
          <input type="text" name="make" id="make" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="year" class="col-form-label col-sm-2">Year : </label>
        <div class="col-sm-2">
          <input type="year" name="year" id="year" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="mileage" class="col-form-label col-sm-2">Mileage : </label>
        <div class="col-sm-1">
          <input type="mileage" name="mileage" id="mileage" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <input type="submit" value="Add" class="btn btn-primary">
        <input type="submit" value="Logout" name="logout" class="btn btn-danger">
      </div>

    </form>

    <?php if(!empty($autos)) : ?>

      <h2>Automobiles</h2>

      <ul>
        <?php foreach($autos as $auto) : ?>
          <li>
            <?= $auto['year']; ?> <?= $auto['make']; ?> <?= $auto['mileage']; ?>
          </li>
        <?php endforeach; ?>
      </ul>

    <?php endif; ?>

  </div>
</body>

</html>
