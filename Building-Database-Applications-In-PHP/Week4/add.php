<?php 

  session_start();

  if(!isset($_SESSION['name'])) {
    die("Not logged in");
  }

  if(isset($_POST['cancel'])) {
    header("Location: view.php");
    return;
  }

  require_once 'pdo.php';

  if(isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {

    if(strlen($_POST['make']) < 1) {
      $_SESSION['error'] = "Make is required";
      header("Location: add.php");
      return;
    }

    else if(!is_numeric($_POST['mileage']) || !is_numeric($_POST['year'])) {
      $_SESSION['error'] = "Mileage and year must be numeric";
      header("Location: add.php");
      return;
    }

    else {
      $make = htmlentities($_POST['make']);
      $year = htmlentities($_POST['year']);
      $mileage = htmlentities($_POST['mileage']);

      $sql = "INSERT INTO db(make, year, mileage)
              VALUES(:make, :year, :mileage)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':make' => $make,
        ':year' => $year,
        ':mileage' => $mileage,
      ]);

      $_SESSION['success'] = "Record inserted";
      header("Location: view.php");
      return;
    }
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
    <h1>Tracking Autos for <?php echo $_SESSION['name']; ?></h1>

    <?php 
      if(isset($_SESSION['error'])) {
        echo('<p style="color: red" class="col-sm-10 col-sm-offset-2">'.$_SESSION['error']."</p>\n");
        unset($_SESSION['error']);
      }
    ?>

    <form method="POST">
      <label for="make">Make :</label>
      <input type="text" name="make" id="make" class="col-sm-6 form-control">
      <label for="year">Year : </label>
      <input type="year" name="year" id="year" class="col-sm-6 form-control">
      <label for="mileage">Mileage : </label>
      <input type="mileage" name="mileage" id="mileage" class="col-sm-6 form-control">

      <div class="form-group">
        <input type="submit" value="Add" class="mt-2 btn btn-primary">
        <input type="submit" value="Cancel" name="cancel" class="mt-2 btn btn-danger">
      </div>
    </form>
  </div>  
</body>
</html>
