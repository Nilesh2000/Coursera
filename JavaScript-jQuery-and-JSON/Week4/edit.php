<?php 

  session_start();

  require_once 'pdo.php';

  if( !isset($_SESSION['user_id']) ) {
    die("ACCESS DENIED");
  }

  if( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
  }

  $status = false;

  if( isset($_SESSION['status']) ) {
    $status       = $_SESSION['status'];
    $status_color = $_SESSION['color'];

    unset($_SESSION['status']);
    unset($_SESSION['color']);
  }

  $_SESSION['color'] = "red";

  if( !isset($_GET['profile_id']) ) {
    $_SESSION['status'] = "Missing profile_id";
    header("Location: index.php");
    return;
  }

  $profile_id = htmlentities($_GET['profile_id']);
  
  if( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
    && isset($_POST['headline']) && isset($_POST['summary']) ) {

    if( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 
      || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {

      $_SESSION['status'] = "All fields are required";
      header("Location: edit.php?profile_id=".$profile_id);
      return;
    }

    if(strpos($_POST['email'], '@') === false) {
      $_SESSION['status'] = "Email address must contain @";
      header("Location: edit.php?profile_id=".$profile_id);
      return;
    }

    $first_name = htmlentities($_POST['first_name']);
    $last_name  = htmlentities($_POST['last_name']);
    $email      = htmlentities($_POST['email']);
    $headline   = htmlentities($_POST['headline']);
    $summary    = htmlentities($_POST['summary']);

    $sql  = "UPDATE profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':fn'  => $first_name,
      ':ln'  => $last_name,
      ':em'  => $email,
      ':he'  => $headline,
      ':su'  => $summary,
      ':pid' => $profile_id,
    ]);

    $sql  = "DELETE from position WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);

    $sql  = "DELETE FROM education WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);

    $rank = 1;

    for($i = 1 ; $i <= 9; $i++) {
      if( !isset($_POST['year'.$i]) ) continue;
      if( !isset($_POST['desc'.$i]) ) continue;

      $year = htmlentities($_POST['year'.$i]);
      $desc = htmlentities($_POST['desc'.$i]);

      $sql  = "INSERT INTO position (profile_id, rank, year, description)
               VALUES(:pid, :rank, :year, :description)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':pid'         => $profile_id,
        ':rank'        => $rank,
        ':year'        => $year,
        ':description' => $desc,
      ]);

      $rank++;
    }

    $rank = 1;

    for($i = 1 ; $i <= 9 ; $i++) {
      if( !isset($_POST['edu_year'.$i]) ) continue;
      if( !isset($_POST['edu_school'.$i]) ) continue;

      $edu_year   = htmlentities($_POST['edu_year'.$i]);
      $edu_school = htmlentities($_POST['edu_school'.$i]);

      // Check if entered school already exists in the table
      $sql  = "SELECT * FROM institution WHERE name = :edu_school LIMIT 1";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':edu_school' => $edu_school]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

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

      $sql  = "INSERT INTO education(profile_id, institution_id, rank, year)
               VALUES(:profile_id, :institution_id, :rank, :year)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':profile_id'     => $profile_id,
        ':institution_id' => $institution_id,
        ':rank'           => $rank,
        ':year'           => $edu_year,
      ]);
      
      $rank++;
    }

    $_SESSION['status'] = "Profile updated";
    $_SESSION['color']  = "green";

    header("Location: index.php");
    return;
  }

  $sql     = "SELECT * FROM profile WHERE profile_id=:pid";
  $stmt    = $pdo->prepare($sql);
  $stmt->execute([':pid' => $profile_id]);
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);

  $sql  = "SELECT * FROM education LEFT JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id=:pid ORDER BY rank";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':pid' => $profile_id]);
  $schools = array();
  
  while($school = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $schools[] = $school;
  }

  $sql  = "SELECT * FROM position WHERE profile_id=:pid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':pid' => $profile_id]);

  $position = array();

  while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    $position[] = $row;
  }

  $numOfPositions = count($position);
  $numOfSchools   = count($schools);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <!-- Stylesheet for jQuery UI -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

  <title>Nilesh D</title>
</head>

<body>
  <div class="container">
    <h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php 
      if($status != false) {
        echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
      }
    ?>

    <form method="POST">
      
      <div class="form-group row">
        <label for="first_name" class="col-form-label col-sm-2">First Name:</label>
        <div class="col-sm-5">
          <input type="text" name="first_name" value="<?= $profile['first_name']; ?>" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="last_name" class="col-form-label col-sm-2">Last Name:</label>
        <div class="col-sm-5">
          <input type="text" name="last_name" value="<?= $profile['last_name']; ?>" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-form-label col-sm-2">Email:</label>
        <div class="col-sm-5">
          <input type="text" name="email" value="<?= $profile['email']; ?>" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="headline" class="col-form-label col-sm-2">Headline:</label>
        <div class="col-sm-5">
          <input type="text" name="headline" value="<?= $profile['headline']; ?>" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="summary" class="col-form-label col-sm-2">Summary:</label>
        <div class="col-sm-5">
          <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"><?= $profile['summary']; ?></textarea>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-2">Education: </label>
        <div class="col-sm-5">
          <button id="addEdu" class="btn btn-secondary">+</button>
        </div>
      </div>

      <div id="edu_fields">

        <?php if($numOfSchools > 0) : ?>
          
          <?php for($i = 1 ; $i <= $numOfSchools ; $i++) : ?>

            <div id="edu<?= $i; ?>">
            
              <div class="form-group row">
                <div class="col-sm-label col-sm-2">Year:</div>
                <div class="col-sm-3">
                  <input type="text" name="edu_year<?= $i; ?>" class="form-control" value="<?= $schools[$i-1]['year']; ?>">
                </div>
                <button class="btn btn-danger" onclick="$('#edu<?= $i; ?>').remove();return false;">-</button>
              </div>

              <div class="form-group row">
                <label class="col-sm-label col-sm-2">School:</label>
                <div class="col-sm-7">
                  <input type="text" name="edu_school<?= $i; ?>" class="form-control school" value="<?= $schools[$i-1]['name']; ?>">
                </div>
              </div>

            </div>
            <br>

          <?php endfor; ?>

        <?php endif; ?>

      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-2">Position: </label>
        <div class="col-sm-5">
          <button id="addPos" class="btn btn-secondary">+</button>
        </div>
      </div>


      <div id="position_fields">
        
        <?php if($numOfPositions > 0) : ?>
          
          <?php for($i = 1 ; $i <= $numOfPositions ; $i++) : ?>

            <div id="position<?= $i; ?>">

              <div class="form-group row">
                <label class="col-form-label col-sm-2">Year:</label>
                <div class="col-sm-3">
                  <input type="text" name="year<?= $i; ?>" class="form-control" value="<?= $position[$i-1]->year; ?>">
                </div>
                <button class="btn btn-danger" onclick="$('#position<?= $i; ?>').remove();return false;">-</button> 
              </div>

              <div class="col-sm-6 p-0">
                <textarea name="desc<?= $i; ?>" rows="8" class="form-control"><?= $position[$i-1]->description; ?></textarea>
              </div>

            </div>
            <br>

          <?php endfor; ?>

        <?php endif; ?>

      </div>

      <input type="submit" value="Save" class="btn btn-primary mt-2">
      <input type="submit" value="Cancel" class="btn btn-dark mt-2" name="cancel">
    </form>
  </div>
</body>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous">
</script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
    integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous">
</script>

<script>

  countPos = <?= $numOfPositions; ?>;
  countEdu = <?= $numOfSchools; ?>;

  $(document).ready(function() {
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event) {
      event.preventDefault();

      if(countPos >= 9) {
        alert("Maximum of nine position entries exceeded");
        return;
      }
      countPos++;
      window.console && console.log("Adding position "+countPos);

      $('#position_fields').append(
        '<div id="position'+countPos+'"> \
          \
          <div class="form-group row"> \
            <label class="col-form-label col-sm-2">Year:</label> \
            <div class="col-sm-3"> \
              <input class="form-control" type="text" name="year'+countPos+'"> \
            </div> \
            <button class="btn btn-danger" onclick="$(\'#position'+countPos+'\').remove();return false;">-</button> \
          </div> \
          \
          <div class="col-sm-6 p-0"> \
            <textarea class="form-control" name="desc'+countPos+'" rows="8"></textarea> \
          </div> \
        </div> \
        <br>'
      );
    });

    $('#addEdu').click(function(event) {
      event.preventDefault();
      
      if(countEdu >= 9) {
        alert("Maximum of nine education entries exceeded");
        return;
      }
      countEdu++;
      window.console && console.log("Adding education "+countEdu);

      $('#edu_fields').append(
        '<div id="edu'+countEdu+'"> \
          \
          <div class="form-group row"> \
            <label class="col-sm-label col-sm-2">Year:</label> \
            <div class="col-sm-3"> \
              <input type="text" class="form-control" name="edu_year'+countEdu+'"> \
            </div> \
            <button class="btn btn-danger" onclick="$(\'#edu'+countEdu+'\').remove();return false;">-</button> \
          </div> \
          \
          <div class="form-group row"> \
            <label class="col-sm-label col-sm-2">School:</label> \
            <div class="col-sm-7"> \
              <input type="text" class="form-control school" name="edu_school'+countEdu+'" /> \
            </div> \
          </div> \
        </div>'
      );

      $('.school').autocomplete({
        source: "school.php"
      });

    });

  });

</script>

</html>

