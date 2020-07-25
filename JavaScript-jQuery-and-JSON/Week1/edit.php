<?php

  session_start();

  require_once 'inc/pdo.php';
  require_once 'inc/logged_in.php';

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

  if( !isset($_GET['profile_id']) ) {
    $_SESSION['status'] = "Missing profile_id";
    header("Location: index.php");
    return;
  }

  $profile_id = htmlentities($_GET['profile_id']);

  if( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
    && isset($_POST['headline']) && isset($_POST['summary']) ) {

    if( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 
      || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {

      $_SESSION['status'] = "All fields are required";
      header("Location: edit.php?profile_id=".$profile_id);
      return;
    }

    if( strpos($_POST['email'], '@') === false ) {
      $_SESSION['status'] = "Email address must contain @";
      header("Location: edit.php?profile_id=".$profile_id);
      return;
    }

    $first_name = htmlentities($_POST['first_name']);
    $last_name  = htmlentities($_POST['last_name']);
    $email      = htmlentities($_POST['email']);
    $headline   = htmlentities($_POST['headline']);
    $summary    = htmlentities($_POST['summary']);

    $sql  = "UPDATE profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':fn'  => $first_name,
      ':ln'  => $last_name,
      ':em'  => $email,
      ':he'  => $headline,
      ':su'  => $summary,
      ':pid' => $profile_id,
    ]);

    $_SESSION['status'] = "Profile updated";
    $_SESSION['color']  = "green";

    header("Location: index.php");
    return;
  }

  $sql  = "SELECT * FROM profile WHERE profile_id=:pid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':pid' => $profile_id]);
  $profile = $stmt->fetch();
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
    <h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php 
      if($status != false) {
        echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
      }
    ?>

    <form method="POST">

      <div class="form-group row">
        <label for="first_name" class="col-form-label col-sm-2">First Name:</label>
        <div class="col-sm-5">
          <input type="text" name="first_name" id="first_name" class="form-control" value="<?= $profile['first_name']; ?>">
        </div>
      </div>

      <div class="form-group row">
        <label for="last_name" class="col-form-label col-sm-2">Last Name:</label>
        <div class="col-sm-5">
          <input type="text" name="last_name" id="last_name" class="form-control" value="<?= $profile['last_name']; ?>">
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-form-label col-sm-2">Email:</label>
        <div class="col-sm-5">
          <input type="text" name="email" id="email" class="form-control" value="<?= $profile['email']; ?>">
        </div>
      </div>

      <div class="form-group row">
        <label for="headline" class="col-form-label col-sm-2">Headline:</label>
        <div class="col-sm-5">
          <input type="text" name="headline" id="headline" class="form-control" value="<?= $profile['headline']; ?>">
        </div>
      </div>

      <div class="form-group row">
        <label for="summary" class="col-form-label col-sm-2">Summary:</label>
        <div class="col-sm-5">
          <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"><?= $profile['summary']; ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <input type="submit" value="Save" class="btn btn-primary">
        <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">
      </div>

    </form>
    
  </div>
</body>
</html>
