<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nilesh D</title>
</head>
<body>

  <h1>Welcome to my guessing game</h1>
  <?php 

    if(!isset($_GET['guess'])) {
      echo "Missing guess parameter";
      return;
    }

    if(empty($_GET['guess'])) {
      echo "Your guess is too short";
      return;
    }

    $guess = $_GET['guess'];

    if(!is_numeric($guess)) {
      echo "Your guess is not a number";
      return;
    }

    if($guess > 79) {
      echo "Your guess is too high";
    } else if($guess < 79) {
      echo "Your guess is too low";
    } else {
      echo "Congratulations - You are right";
    }

  ?>

</body>
</html>

