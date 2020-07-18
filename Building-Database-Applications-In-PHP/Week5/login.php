<?php

session_start();

if(isset($_POST['logout'])) {
  header("Location: index.php");
  return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // Password is php123

$error = false;

if(isset($_SESSION['error'])) {
  $error = htmlentities($_SESSION['error']);
  unset($_SESSION['error']);
}

if(isset($_POST['email']) && isset($_POST['pass'])) {
  if(strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
    $_SESSION['error'] = "User name and password are required";
    header("Location: login.php");
    return;
  }

  $email = htmlentities($_POST['email']);
  $pass = htmlentities($_POST['pass']);

  $check = hash("md5", $salt.$pass);

  if($check != $stored_hash) {
    error_log("Login Failure".$pass."$check");
    $_SESSION['error'] = "Incorrect password";

    header("Location: login.php");
    return;
  }
  
  error_log("Login success ".$email);
  $_SESSION['name'] = $email;

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
    <h1>Please Log In</h1>

    <?php 

      if($error !== false) {
        echo('<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.htmlentities($error)."</p>\n");
      }

    ?>

    <form method="POST">

      <label for="email">User Name</label>
      <input type="text" name="email" id="email" class="form-control col-sm-3">
      <label for="password">Password</label>
      <input type="password" name="pass" id="password" class="form-control col-sm-3">

      <input type="submit" value="Log In" class="btn btn-primary mt-2">
      <input type="submit" value="Cancel" class="btn btn-default" name="logout">
    </form>

    <p>
      For a password hint, view source and find a password hint in the HTML comments.
      <!-- Password is three letter scripting language (all lowercase) followed by 123 -->
    </p>

  </div>
</body>
</html>
