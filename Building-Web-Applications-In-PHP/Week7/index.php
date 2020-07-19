<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nilesh D</title>
</head>
<body>

  <h1>MD5 Cracker</h1>
  <p>This application takes an MD5 hash of a four digit pin and check all 10,000 possible four digit PINs to determine the PIN.</p>

  <pre>
  Debug Output:
<?php

      $status = "Not found";

      if(isset($_GET['md5'])) {
        $start_time = microtime(true);
        $md5 = $_GET['md5'];

        $show = 15; // To print first 15 computations

        // Possible first two characters in our pre-hashed pin
        for($iter = 0 ; $iter < 100 ; $iter++) {
          $ch1 = $iter; // The first of the two characters
          if($ch1 < 10) {
            $ch1 = "0".$ch1;
          }
          // Inner Loop
          for($j = 0 ; $j < 100 ; $j++) {
            $ch2 = $j;
            if($ch2 < 10) {
              $ch2 = "0".$ch2;
            }
            $pin = $ch1.$ch2;

            // Run to hash to check if we match
            $check = hash('md5', $pin);
            if($check == $md5) {
              $status = $pin;
              break; // Exit inner loop
            }

            if($show > 0) {
              print "$check $pin\n";
              $show = $show - 1;
            }
          }
        }

        // Compute time elapsed
        $end_time = microtime(true);
        echo "\nEllapsed time: ";
        echo $end_time - $start_time;
        echo "\n";
      }
    ?>
  </pre>

  <!-- Using shorthand syntax -->
  <p>PIN: <?= htmlentities($status); ?></p>

  <form>
    <input type="text" name="md5" size="40">
    <input type="submit" value="Crack MD5">
  </form>

</body>
</html>
