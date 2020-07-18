<?php 

session_start();

if(!isset($_SESSION['name'])) {
  die("ACCESS DENIED");
}

if(isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

$status = false;

if(isset($_SESSION['status'])) {
  $status = htmlentities($_SESSION['status']);
  $status_colour = htmlentities($_SESSION['color']);

  unset($_SESSION['status']);
  unset($_SESSION['color']);
}

require_once 'pdo.php';

$name = htmlentities($_SESSION['name']);

$_SESSION['color'] = 'red';

if(isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])) {
  
  if(strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
    $_SESSION['status'] = "All fields are required";
    header("Location: add.php");
    return;
  }

  if(!is_numeric($_POST['year'])) {
      $_SESSION['status'] = "Year must be an integer";
      header("Location: add.php");
      return;
  }

  if(!is_numeric($_POST['mileage'])) {
      $_SESSION['status'] = "Mileage must be an integer";
      header("Location: edit.php?autos_id=".htmlentities($_GET['autos_id']));
      return;
  }

  $make = htmlentities($_POST['make']);
  $model = htmlentities($_POST['model']);
  $year = htmlentities($_POST['year']);
  $mileage = htmlentities($_POST['mileage']);

  
  $sql = "INSERT INTO db VALUES(AUTO_ID, :make, :model, :year, :mileage)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
                  ':make' => $make,
                  ':model' => $model,
                  ':year' => $year,
                  ':mileage' => $mileage,
                  ]);
                  
  $_SESSION['status'] = "Record added";
  $_SESSION['color'] = "green";

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

  <?php 

    if($status != false) {
      echo('<p style="color: '.$status_colour.';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
    }

  ?>

    <form method="POST">

      <label for="make">Make</label>
      <input type="text" name="make" id="make" class="form-control col-sm-3">

      <label for="model">Model</label>
      <input type="text" name="model" id="model" class="form-control col-sm-3">

      <label for="year">Year</label>
      <input type="text" name="year" id="year" class="form-control col-sm-3">

      <label for="mileage">Mileage</label>
      <input type="text" name="mileage" id="mileage" class="form-control col-sm-3">

      <div class="form-group mt-2">
        <input type="submit" value="Add" class="btn btn-primary">
        <input type="submit" value="Cancel" name="cancel" class="btn btn-dark">
      </div>

    </form>
  </div>
</body>
</html>
