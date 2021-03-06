<?php 

  session_start();

  require_once 'inc/pdo.php';
  require_once 'inc/logged_in.php';
  require_once 'inc/utilities.php';

  if( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
  }

  $name = htmlentities($_SESSION['name']);

  $_SESSION['color'] = 'red';

  if( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
    
    $msg = validateProfile();
    if( is_string($msg) ) {
      $_SESSION['status'] = $msg;
      header("Location: add.php");
      return;
    }

    $make    = htmlentities($_POST['make']);
    $model   = htmlentities($_POST['model']);
    $year    = htmlentities($_POST['year']);
    $mileage = htmlentities($_POST['mileage']);

    $sql = "INSERT INTO db VALUES(AUTO_ID, :make, :model, :year, :mileage)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':make'    => $make,
      ':model'   => $model,
      ':year'    => $year,
      ':mileage' => $mileage,
    ]);
                    
    $_SESSION['status'] = "Record added";
    $_SESSION['color']  = "green";

    header("Location: index.php");
    return;
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

  <h1>Tracking Automobiles for <?= $name; ?></h1>
  <br>

  <?php flashMessage(); ?>

    <form method="POST">

      <div class="form-group row">
        <label for="make" class="col-form-label col-sm-2">Make :</label>
        <div class="col-sm-5">
          <input type="text" name="make" id="make" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="model" class="col-form-label col-sm-2">Model :</label>
        <div class="col-sm-3">
          <input type="text" name="model" id="model" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="year" class="col-form-label col-sm-2">Year :</label>
        <div class="col-sm-2">
          <input type="year" name="year" id="year" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="mileage" class="col-form-label col-sm-2">Mileage :</label>
        <div class="col-sm-1">
          <input type="mileage" name="mileage" id="mileage" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <input type="submit" value="Add" class="btn btn-primary">
        <input type="submit" value="Cancel" name="cancel" class="btn btn-dark">
      </div>

    </form>
  </div>
</body>
</html>
