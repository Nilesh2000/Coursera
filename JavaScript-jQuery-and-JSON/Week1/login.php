<?php 

  session_start();

  require_once 'pdo.php';

  if(isset($_POST['logout'])) {
    header("Location: logout.php");
    return;
  }

  $salt        = 'XyZzy12*_';
  $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // Password is php123

  $error = false;

  if(isset($_SESSION['error'])) {
    $error = htmlentities($_SESSION['error']);
    unset($_SESSION['error']);
  }

  if(isset($_POST['email']) && isset($_POST['pass'])) {
    if(strlen($_POST['email']) == 0 || strlen($_POST['pass']) == 0) {
      $_SESSION['error'] = "User name and password are required";
      header("Location: login.php");
      return;
    }

    $email    = htmlentities($_POST['email']);
    $password = htmlentities($_POST['pass']);

    $check = hash('md5', $salt.$_POST['pass']);
    $sql   = "SELECT user_id, name FROM users WHERE email = :email and password = :password";
    $stmt  = $pdo->prepare($sql);
    $stmt->execute([
      ':email' => $email, 
      ':password' => $check,
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($row != false) {
      $_SESSION['name']    = $row['name'];
      $_SESSION['user_id'] = $row['user_id'];
      header("Location: index.php");
      return;
    }

    $_SESSION['error'] = "Incorrect password";
    header("Location: login.php");
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
    if($error != false) {
      echo "<p style='color: red;' class='col-sm-10 col-sm-offset-2'>$error</p>";
    }

    ?>
    <form method="POST">
        
        <label for="email">Email</label>
        <input type="text" name="email" id="email" class="form-control col-sm-3">
        <label for="password">Password</label>
        <input type="password" name="pass" id="password" class="form-control col-sm-3">

        <div class="form-group mt-2">
          <input type="submit" value="Log In" onclick="return doValidate();" class="btn btn-primary">
          <input type="submit" value="Cancel" class="btn btn-dark" name="logout">
        </div>

    </form>
    <p>For a password hint, view source and find an account and password in the HTML comments</p>
    <!-- Account : umsi@umich.edu -->
    <!-- Password : php123 -->
  </div>
</body>
<script>

  function doValidate() {
    console.log("Validating...");
    try {
      addr = document.getElementById('email').value;
      pw = document.getElementById('password').value;
      console.log("Validating addr="+addr+" pw="+pw);

      if(addr == null || addr == "" || pw == null || pw == "") {
        alert("Both fields must be filled out");
        return false;
      }
      if(addr.indexOf('@') == -1) {
        alert("Invalid email address");
        return false;
      }
      return true;
    } catch(e) {
      return false;
    }
    return false;
  }

</script>
</html>
