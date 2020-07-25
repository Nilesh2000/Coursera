<?php 

  session_start();
  
  require_once 'inc/pdo.php';
  require_once 'inc/logged_in.php';

  if(isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
  }

  if(!isset($_GET['profile_id'])) {
    $_SESSION['status'] = "Missing profile_id";
    $_SESSION['color'] = "red";
    header("Location: index.php");
    return;
  }

  $profile_id = htmlentities($_GET['profile_id']);
 
  if(isset($_POST['delete'])) {  
    $profile_id = $_POST['profile_id'];
    $sql = "DELETE FROM profile WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);

    $_SESSION['status'] = "Record deleted";
    $_SESSION['color'] = "green";

    header("Location: index.php");
    return;
  }

  $sql = "SELECT first_name, last_name FROM profile WHERE profile_id=:pid";
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
    <h1>Deleting Profile</h1>
    <p>First Name: <?= $profile['first_name']; ?></p>
    <p>Last Name: <?= $profile['last_name']; ?></p>

    <form method="POST">
      <input type="hidden" name="profile_id" value=<?= $_GET['profile_id']; ?>>
      <input type="submit" value="Delete" name="delete" class="btn btn-primary">
      <input type="submit" value="Cancel" name="cancel" class="btn btn-dark">
    </form>
    
  </div>  
</body>
</html>
