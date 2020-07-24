<?php 

  require_once 'pdo.php';
  session_start();

  if( !isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
  }

  if( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
  }

  $status = false;

  if(isset($_SESSION['status'])) {
    $status        = htmlentities($_SESSION['status']);
    $status_colour = htmlentities($_SESSION['color']);

    unset($_SESSION['status']);
    unset($_SESSION['color']);
  }


  $name = htmlentities($_SESSION['name']);

  $_SESSION['color'] = 'red';

  if( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
    
    if( strlen($_POST['make']) == 0 || strlen($_POST['model']) == 0 || strlen($_POST['year']) == 0 || strlen($_POST['mileage']) == 0 ) {
      $_SESSION['status'] = "All fields are required";
      header("Location: add.php");
      return;
    }

    if( !is_numeric($_POST['year']) ) {
        $_SESSION['status'] = "Year must be an integer";
        header("Location: add.php");
        return;
    }

    if( !is_numeric($_POST['mileage']) ) {
        $_SESSION['status'] = "Mileage must be an integer";
        header("Location: edit.php?autos_id=".htmlentities($_GET['autos_id']));
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

  <h1>Tracking Automobiles for <?php echo $name; ?></h1>
  <br>

  <?php 
    if($status != false) {
      echo('<p style="color: '.$status_colour.';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
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
