<?php 

session_start();

// If the cancel button is clicked
if(isset($_POST['logout'])) {
  header('Location: index.php');
  return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // Password is php123

$error = false; // If POST data is incorrectly formatted

if(isset($_POST['email']) && isset($_POST['pass'])) {

  if(strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
  $_SESSION['error'] = "User name and password are required";
  header("Location: login.php");
  return;
  }

  else {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    if(strpos($email, '@') === false) {
      $_SESSION['error'] = "Email must have an at-sign (@)";
      header("Location: login.php");
      return;
    }

    else {
      $check = hash('md5', $salt.$password);
      if($check == $stored_hash) {
        error_log("Login success : ".$email);
        $_SESSION['name'] = $_POST['email'];    
        header("Location: view.php");
        return;   
      }
      else {
        $_SESSION['error'] = "Incorrect password";
        error_log("Login fail : ".$email."$check");
        header("Location: login.php");
        return;
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
    if(isset($_SESSION['error'])) // Note the use of triple equals and not double equals
    {
      echo('<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
    }
    ?>
  
    <form method="POST">

      <label for="email">E-Mail ID:</label>
      <input type="text" class="col-sm-3 form-control" name="email" id="email" placeholder="Enter E-Mail">
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
