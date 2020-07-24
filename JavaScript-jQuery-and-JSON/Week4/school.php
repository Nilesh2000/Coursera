<?php 

  require_once 'pdo.php';
  session_start();

  if(!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
  }

  // add.php/edit.php sends a GET request to school.php with a term parameter
  if(isset($_GET['term'])) {
    $sql = "SELECT name FROM institution WHERE name LIKE :prefix";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':prefix' => $_GET['term']."%"]);
    $schools = array();
    // Do not use fetchAll here. Don't ask me why. It just doesn't work.
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $schools[] = $row['name'];
    }
    echo json_encode($schools, JSON_PRETTY_PRINT);
  }

?>
