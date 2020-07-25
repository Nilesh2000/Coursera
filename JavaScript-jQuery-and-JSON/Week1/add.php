<?php 

  require_once 'pdo.php';

  session_start();

  if( !isset($_SESSION['user_id']) ) {
    die("Not logged in");
  }

  if( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
  }

  $status = false;

  if( isset($_SESSION['status']) ) {
    $status       = $_SESSION['status'];
    $status_color = $_SESSION['color'];

    unset($_SESSION['status']);
    unset($_SESSION['color']);
  }

  $_SESSION['color'] = "red";

  if( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
    && isset($_POST['headline']) && isset($_POST['summary']) ) {

    if( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 
      || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {

      $_SESSION['status'] = "All fields are required";
      header("Location: add.php");
      return;
    }

    if( strpos($_POST['email'], '@') === false ) {
      $_SESSION['status'] = "Email address must contain @";
      header("Location: add.php");
      return;
    }

    $first_name = htmlentities($_POST['first_name']);
    $last_name  = htmlentities($_POST['last_name']);
    $email      = htmlentities($_POST['email']);
    $headline   = htmlentities($_POST['headline']);
    $summary    = htmlentities($_POST['summary']);

    $sql  = "INSERT INTO profile(user_id, first_name, last_name, email, headline, summary)
            VALUES(:uid, :fn, :ln, :em, :he, :su)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':uid' => $_SESSION['user_id'],
      ':fn'  => $first_name,
      ':ln'  => $last_name,
      ':em'  => $email,
      ':he'  => $headline,
      ':su'  => $summary,
    ]);

    $_SESSION['status'] = "Profile added";
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
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php 
      if($status != false) {
        echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
      }
    ?>

    <form method="POST">

      <div class="form-group row">
        <label for="first_name" class="col-form-label col-sm-2">First Name:</label>
        <div class="col-sm-5">
          <input type="text" name="first_name" id="first_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="last_name" class="col-form-label col-sm-2">Last Name:</label>
        <div class="col-sm-5">
          <input type="text" name="last_name" id="last_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-form-label col-sm-2">Email:</label>
        <div class="col-sm-5">
          <input type="text" name="email" id="email" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="headline" class="col-form-label col-sm-2">Headline:</label>
        <div class="col-sm-5">
          <input type="text" name="headline" id="headline" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="summary" class="col-form-label col-sm-2">Summary:</label>
        <div class="col-sm-5">
          <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"></textarea>
        </div>
      </div>

      <div class="form-group">
        <input type="submit" value="Add" class="btn btn-primary">
        <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">
      </div>

    </form>
    
  </div>  
</body>
</html>
