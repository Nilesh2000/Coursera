<?php 

  session_start();

  require_once 'inc/pdo.php';
  require_once 'inc/logged_in.php';
  require_once 'inc/utilities.php';

  if( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
  }

  $_SESSION['color'] = "red";

  if( !isset($_GET['profile_id']) ) {
    $_SESSION['status'] = "Missing profile_id";
    header("Location: index.php");
    return;
  }

  $profile_id = htmlentities($_GET['profile_id']);

  if( isset($_POST['first_name']) && isset($_POST['last_name']) && 
      isset($_POST['email']) && isset($_POST['headline']) && 
      isset($_POST['summary']) ) {

      $msg = validateProfile();
      if( is_string($msg) ) {
        $_SESSION['status'] = $msg;
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
    insertPositions($pdo, $profile_id);

    $sql  = "DELETE FROM education WHERE profile_id=:pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profile_id]);
    insertEdu($pdo, $profile_id);

    $_SESSION['status'] = "Profile updated";
    $_SESSION['color']  = "green";

    header("Location: index.php");
    return;
  }

  $sql     = "SELECT * FROM profile WHERE profile_id=:pid";
  $stmt    = $pdo->prepare($sql);
  $stmt->execute([':pid' => $profile_id]);
  $profile = $stmt->fetch();

  $schools   = loadEdu($pdo, $profile_id);
  $positions = loadPos($pdo, $profile_id);
  
  $numOfSchools   = count($schools);
  $numOfPositions = count($positions);
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php include 'inc/head.php'; ?>

  <title>Nilesh D</title>
</head>

<body>
  <div class="container">
    <h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php flashMessage(); ?>

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
                  <input type="text" name="year<?= $i; ?>" class="form-control" value="<?= $positions[$i-1]['year']; ?>">
                </div>
                <button class="btn btn-danger" onclick="$('#position<?= $i; ?>').remove();return false;">-</button> 
              </div>

              <div class="col-sm-6 p-0">
                <textarea name="desc<?= $i; ?>" rows="8" class="form-control"><?= $positions[$i-1]['description']; ?></textarea>
              </div>

            </div>
            <br>

          <?php endfor; ?>

        <?php endif; ?>

      </div>

      <input type="submit" value="Save" class="btn btn-primary">
      <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">
    </form>
  </div>

  <script>
    countPos = <?= $numOfPositions; ?>;
    countEdu = <?= $numOfSchools; ?>;

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

        var source = $("#position-template").html();
        $('#position_fields').append(source.replace(/@COUNT@/g, countPos));

      });
    
      $('#addEdu').click(function(event) {
        event.preventDefault();
        if(countEdu >= 9) {
          alert("Maximum of nine education entries exceeded");
          return;
        }

        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        var source = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));

        $('.school').autocomplete({
          source: "school.php"
        });

      });

    });
  </script>

  <script id="edu-template" type="text">

    <div id="edu@COUNT@">

      <div class="form-group row">
        <label class="col-sm-label col-sm-2">Year:</label> 
        <div class="col-sm-3"> 
          <input type="text" class="form-control" name="edu_year@COUNT@"> 
        </div>
        <button class="btn btn-danger" onclick="$('#edu@COUNT@').remove();return false;">-</button>
      </div>

      <div class="form-group row">
        <label class="col-sm-label col-sm-2">School:</label> 
        <div class="col-sm-7"> 
          <input type="text" class="form-control school" name="edu_school@COUNT@" />
        </div> 
      </div> 

    </div>

  </script>

  <script id="position-template" type="text">

    <div id="position@COUNT@">

      <div class="form-group row"> 
        <label class="col-form-label col-sm-2">Year:</label> 
        <div class="col-sm-3"> 
          <input class="form-control" type="text" name="year@COUNT@"> 
        </div> 
        <button class="btn btn-danger" onclick="$('#position@COUNT@).remove();return false;">-</button> 
      </div> 
      
      <div class="col-sm-6 p-0"> 
        <textarea class="form-control" name="desc@COUNT@" rows="8"></textarea> 
      </div> 

    </div> 
    <br>

  </script>

</body>

</html>

