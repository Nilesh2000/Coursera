<?php 

// If the cancel button is clicked
if(isset($_POST['logout'])) {
  header('Location: index.php');
  return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // Password is php123

$error = false; // If POST data is incorrectly formatted

if(isset($_POST['who']) && isset($_POST['pass'])) {

  if(strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1) {
  $error = "User name and password are required";
  }

  else {
    $email = $_POST['who'];
    $password = $_POST['pass'];

    if(strpos($email, '@') === false) {
      $error = "Email must have an at-sign (@)";
    }

    else {
      $check = hash('md5', $salt.$password);
      if($check == $stored_hash) {
        error_log("Login success : ".$email);
        header("Location: autos.php?name=".urlencode($email));
        return;
      }
      else {
        $error = "Incorrect password";
        error_log("Login fail : ".$email."$check");
      }
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
    if($error !== false) // Note the use of triple equals and not double equals
    {
      echo('<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.$error."</p>\n");
    }
    ?>
  
    <form method="POST">

      <label for="email">E-Mail ID:</label>
      <input type="text" class="col-sm-3 form-control" name="who" id="email" placeholder="Enter E-Mail">
      <label for="password">Password:</label>
      <input type="text" class="col-sm-3 form-control" name="pass" id="password" placeholder="Enter Password">

      <div class="form-group">
        <input type="submit" value="Log In" class="mt-2 btn btn-primary">
        <input type="submit" value="Cancel" name="logout" class="mt-2 btn btn-dark"> 
      </div>

    </form>
    <p>
      For a password hint, view source and find a password in the HTML comments.
      <!-- Hint: The password is name of the language followed by 123 -->
    </p>
  </div>

</body>
</html>
