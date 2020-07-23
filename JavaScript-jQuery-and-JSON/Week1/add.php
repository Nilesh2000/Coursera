<?php 

  session_start();
  require_once 'pdo.php';

  if(!isset($_SESSION['user_id'])) {
    die("Not logged in");
  }

  if(isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
  }

  $status = false;

  if(isset($_SESSION['status'])) {
    $status = $_SESSION['status'];
    $status_color = $_SESSION['color'];

    unset($_SESSION['status']);
    unset($_SESSION['color']);
  }

  $_SESSION['color'] = "red";

  if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
    && isset($_POST['headline']) && isset($_POST['summary'])) {

    if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 
      || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {

      $_SESSION['status'] = "All fields are required";
      header("Location: add.php");
      return;
    }

    if(strpos($_POST['email'], '@') === false) {
      $_SESSION['status'] = "Email address must contain @";
      header("Location: add.php");
      return;
    }

    $first_name = htmlentities($_POST['first_name']);
    $last_name  = htmlentities($_POST['last_name']);
    $email      = htmlentities($_POST['email']);
    $headline   = htmlentities($_POST['headline']);
    $summary    = htmlentities($_POST['summary']);

    $sql = "INSERT INTO profile(user_id, first_name, last_name, email, headline, summary)
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
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php 
    if($status != false) {
      echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
    }
    ?>

    <form action="" method="POST">
      <label for="first_name">First Name:</label>
      <input type="text" name="first_name" id="first_name" class="form-control col-sm-4">
      <label for="last_name">Last Name:</label>
      <input type="text" name="last_name" id="last_name" class="form-control col-sm-4">
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" class="form-control col-sm-5">
      <label for="headline">Headline:</label>
      <input type="text" name="headline" id="headline" class="form-control col-sm-7">
      <label for="summary">Summary</label>
      <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"></textarea>

      <input type="submit" value="Add" class="btn btn-primary mt-2">
      <input type="submit" value="Cancel" class="btn btn-dark mt-2" name="cancel">
    </form>
  </div>  
</body>
</html>
