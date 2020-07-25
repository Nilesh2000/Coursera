<?php 

  $error = false;

  $salt = 'XyZzy12*_';
  $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // Password is php123

  if(isset($_POST['who']) && isset($_POST['pass'])) {
    if(strlen($_POST['who']) == 0 || strlen($_POST['pass']) == 0) {
      $error = "User name and password are required";
    } else {    
      $password = $_POST['pass'];
      $md5 = hash('md5', $salt.$password);
      if($md5 == $stored_hash) {
        header("Location: game.php?name=".urlencode($_POST['who']));
      } else {
        $error = "Incorrect password";
      }
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
  <h1>Please Log In</h1>

  <?php 

  if($error !== false) { // Note the use of triple equals and not double equals
      echo('<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.$error."</p>\n");
    }
    
  ?>

    <form method="POST">
      <label for="name">User Name</label>
      <input type="text" name="who" class="form-control col-sm-3">

      <label for="password">Password</label>
      <input type="text" name="pass" class="form-control col-sm-3">

      <div class="form-group mt-2">
        <input type="submit" value="Log In" class="btn btn-secondary">
        <a href="index.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
    <p>
      For a password hint, view source and and find a password in HTML comments
      <!-- Password is php123 -->
    </p>
  </div>  
</body>
</html>
