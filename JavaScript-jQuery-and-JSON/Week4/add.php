<?php 

  require_once 'inc/pdo.php';
  
  session_start();

  if(!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
  }

  if(isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
  }

  $status = false;

  if(isset($_SESSION['status'])) {
    $status = $_SESSION['status'];
    $status_color = $_SESSION['color'];

    unset($_SESSION['status']);
    unset($_SESSION['color']);
  }

  $_SESSION['color'] = "red";

  if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
    && isset($_POST['headline']) && isset($_POST['summary'])) {

    if(strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 
      || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0) {

      $_SESSION['status'] = "All fields are required";
      header("Location: add.php");
      return;
    }

    if(strpos($_POST['email'], '@') === false) {
      $_SESSION['status'] = "Email address must contain @";
      header("Location: add.php");
      return;
    }

    $first_name = htmlentities($_POST['first_name']);
    $last_name  = htmlentities($_POST['last_name']);
    $email      = htmlentities($_POST['email']);
    $headline   = htmlentities($_POST['headline']);
    $summary    = htmlentities($_POST['summary']);

    $sql = "INSERT INTO profile(user_id, first_name, last_name, email, headline, summary)
            VALUES(:uid, :fn, :ln, :em, :he, :su)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':uid' => $_SESSION['user_id'],
      ':fn'  => $first_name,
      ':ln'  => $last_name,
      ':em'  => $email,
      ':he'  => $headline,
      ':su'  => $summary,
    ]);

    $profile_id = $pdo->lastInsertId();

    $rank = 1;

    for($i = 1 ; $i <= 9 ; $i++) {
      if(!isset($_POST['year'.$i])) continue;
      if(!isset($_POST['desc'.$i])) continue;

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

    $rank = 1;
    
    for($i = 1 ; $i <= 9 ; $i++) {
      if(!isset($_POST['edu_year'.$i])) continue;
      if(!isset($_POST['edu_school'.$i])) continue;

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
        $sql = "INSERT INTO institution(name)
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

    $_SESSION['status'] = "Profile added";
    $_SESSION['color'] = "green";

    header("Location: index.php");
    return;
  }

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
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php 
    if($status != false) {
      echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
    }
    ?>

    <form method="POST">

      <div class="form-group row">
        <label for="first_name" class="col-form-label col-sm-2">First Name:</label>
        <div class="col-sm-5">
          <input type="text" name="first_name" id="first_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="last_name" class="col-form-label col-sm-2">Last Name:</label>
        <div class="col-sm-5">
          <input type="text" name="last_name" id="last_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-form-label col-sm-2">Email:</label>
        <div class="col-sm-5">
          <input type="text" name="email" id="email" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="headline" class="col-form-label col-sm-2">Headline:</label>
        <div class="col-sm-5">
          <input type="text" name="headline" id="headline" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="summary" class="col-form-label col-sm-2">Summary:</label>
        <div class="col-sm-5">
          <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"></textarea>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-2">Education: </label>
        <div class="col-sm-5">
          <button id="addEdu" class="btn btn-secondary">+</button>
        </div>
      </div>

      <div id="edu_fields">

      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-2">Position: </label>
        <div class="col-sm-5">
          <button id="addPos" class="btn btn-secondary">+</button>
        </div>
      </div>

      <div id="position_fields">

      </div>

      <div class="form-group mt-2">
        <input type="submit" value="Add" class="btn btn-primary">
        <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">
      </div>

    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous">
  </script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
    integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous">
  </script>

  <script>
    countPos = 0;
    countEdu = 0;

    $(document).ready(function() {
      window.console && console.log('Document ready called');

      $('#addPos').click(function(event) {
        event.preventDefault();
        if (countPos >= 9) {
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
        window.console && console.log("Adding education"+countEdu);

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
</body>

</html>
