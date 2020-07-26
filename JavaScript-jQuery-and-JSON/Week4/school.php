<?php 

  session_start();

  require_once 'inc/pdo.php';
  // require_once 'inc/logged_in.php'; - Do not include this script. Don't ask me why

  // add.php/edit.php sends a GET request to school.php with a term parameter
  if(isset($_GET['term'])) {
    $sql  = "SELECT name FROM institution WHERE name LIKE :prefix";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':prefix' => $_GET['term']."%"]);
    $schools = array();
    // Do not use fetchAll here. Don't ask me why. It just doesn't work.
    while($row = $stmt->fetch()) {
      $schools[] = $row['name'];
    }
    echo (json_encode($schools, JSON_PRETTY_PRINT));
  }

?>
