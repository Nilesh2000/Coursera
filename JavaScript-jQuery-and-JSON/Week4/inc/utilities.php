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

  function loadEdu($pdo, $profile_id) {
    $sql  = "SELECT * FROM education LEFT JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id=:pid ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);
    $schools = $stmt->fetchAll();
    return $schools;
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

  function insertEdu($pdo, $profile_id) {
    $rank = 1;
    
    for($i = 1 ; $i <= 9 ; $i++) {
      if( !isset($_POST['edu_year'.$i]) ) continue;
      if( !isset($_POST['edu_school'.$i]) ) continue;

      $edu_year = htmlentities($_POST['edu_year'.$i]);
      $edu_school = htmlentities($_POST['edu_school'.$i]);

      // Check if entered school already exists in the table
      $sql = "SELECT * FROM institution WHERE name = :edu_school LIMIT 1";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':edu_school' => $edu_school]);
      $result = $stmt->fetch();

      if($result) {
        // Get the institution ID of the last inserted institution
        $institution_id = $result['institution_id'];
      }

      else {
        // If institution is not present in the table, insert it into the table
        $sql  = "INSERT INTO institution(name)
                 VALUES(:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name' => $edu_school]);
        // Get the ID of the last inserted institution
        $institution_id = $pdo->lastInsertId();
      }

      $sql = "INSERT INTO education(profile_id, institution_id, rank, year)
              VALUES(:profile_id, :institution_id, :rank, :year)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':profile_id' => $profile_id,
        ':institution_id' => $institution_id,
        ':rank' => $rank,
        ':year' => $edu_year,
      ]);

      $rank++;
    }
  }

?>
