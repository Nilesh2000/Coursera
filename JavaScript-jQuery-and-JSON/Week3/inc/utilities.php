<?php 

  function flashMessage() {
    if( isset($_SESSION['status']) ) {
      echo('<p style="color: '. $_SESSION['color']. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($_SESSION['status'])."</p>\n");
      unset($_SESSION['status']);
      unset($_SESSION['color']);
    }
  }

  function validateProfile() {
    if( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || 
        strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 ||
        strlen($_POST['summary']) == 0 ) {
        return "All fields are required";
    }
    
    if( strpos($_POST['email'], '@') === false ) {
      return "Email address must contain @";
    }
    return true;
  }

  function loadPos($pdo, $profile_id) {
    $sql  = "SELECT * FROM position WHERE profile_id=:pid ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);
    $positions = $stmt->fetchAll();
    return $positions;
  }

  function insertPositions($pdo, $profile_id) {
    $rank = 1;

    for($i = 1 ; $i <= 9 ; $i++) {
      if( !isset($_POST['year'.$i]) ) continue;
      if( !isset($_POST['desc'.$i]) ) continue;

      if(!is_numeric($_POST['year'.$i])) {
        $_SESSION['status'] = "Year must be numeric";
        header("Location: add.php");
        return;
      }

      $year = htmlentities($_POST['year'.$i]);
      $desc = htmlentities($_POST['desc'.$i]);

      $sql = "INSERT INTO position (profile_id, rank, year, description)
              VALUES(:pid, :rank, :year, :description)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $year,
        ':description' => $desc,
      ]);

      $rank++;
    }
  }

?>
