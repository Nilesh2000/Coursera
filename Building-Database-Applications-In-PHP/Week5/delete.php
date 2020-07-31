<?php 

  session_start();

  require_once 'inc/pdo.php';
  require_once 'inc/logged_in.php';

  if( isset($_GET['autos_id']) ) {
    $auto_id = htmlentities($_GET['autos_id']);

    if( isset($_POST['delete']) ) {
      $sql  = "DELETE FROM db WHERE auto_id=:auto_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':auto_id' => $auto_id]);

      $_SESSION['status'] = "Record deleted";
      $_SESSION['color']  = "green";

      header("Location: index.php");
      return;
    }

    $sql  = "SELECT make FROM db WHERE auto_id=:auto_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':auto_id' => $auto_id]);

    $row = $stmt->fetch();
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

    <p>
      Confirm: Deleting <?= $row['make']; ?>
    </p>

    <form method="POST">

      <div class="form-group">
        <input type="submit" value="Delete" name="delete" class="btn btn-primary">
        <a href="index.php" class="btn btn-default">Cancel</a>
      </div>
      
    </form>

  </div> 
</body>
</html>
