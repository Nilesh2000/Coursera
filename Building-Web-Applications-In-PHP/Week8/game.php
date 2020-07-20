<?php 

  if(!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name paramter missing");
  }

  if(isset($_POST['logout'])) {
    header('Location: index.php');
    return;
  }

  $names = array("Rock", "Paper", "Scissors");
  $human = isset($_POST['human']) ? $_POST['human'] + 0 : -1;
  $computer = rand(0,2);

  function check($computer, $human) {
     if($human == $computer) {
      return "Tie";
    } else if(($human == 0 && $computer == 2) || ($human == 1 && $computer == 0) || ($human == 2 && $computer == 1)) {
      return "You Win";
    } else {
      return "You Lose";
    } 
  }

  $result = check($computer, $human);    

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
    <h1>Rock Paper Scissors</h1>
    <p>Welcome : <?= htmlentities($_GET['name']); ?></p>

    <form method="POST">
      <select name="human" class="form-control col-sm-2">
        <option value="-1" selected>Select</option>
        <option value="0">Rock</option>
        <option value="1">Paper</option>
        <option value="2">Scissors</option>
        <option value="3">Test</option>
      </select>
      <div class="form-group mt-2">
        <input type="submit" value="Play" class="btn btn-primary">
        <input class="btn" type="submit" name="logout" value="Logout">
      </div>
    </form>

    <pre>
<?php
      
      if($human == -1) {
        print "Please select a strategy and press Play.\n";
      }

      else if($human == 3) {
        for($c = 0 ; $c < 3 ; $c++) {
          for($h = 0 ; $h < 3 ; $h++) {
            $r = check($c, $h);
            print "Human=$names[$h] Computer=$names[$c] Result=$r";
            echo "<br>";
          }
        }
      }

      else {
        print "Your Play=$names[$human] Computer Play=$names[$computer] Result=$result\n";
      }

    ?>
    </pre>

  </div>
</body>
</html>
