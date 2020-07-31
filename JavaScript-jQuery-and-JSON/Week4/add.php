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

  if( isset($_POST['first_name']) && isset($_POST['last_name']) && 
     isset($_POST['email']) && isset($_POST['headline']) && 
     isset($_POST['summary']) ) {
      
      $msg = validateProfile();
      if( is_string($msg) ) {
        $_SESSION['status'] = $msg;
        header("Location: add.php");
        return;
      }

      $first_name = htmlentities($_POST['first_name']);
      $last_name  = htmlentities($_POST['last_name']);
      $email      = htmlentities($_POST['email']);
      $headline   = htmlentities($_POST['headline']);
      $summary    = htmlentities($_POST['summary']);

      $sql  = "INSERT INTO profile(user_id, first_name, last_name, email, headline, summary)
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

      insertPositions($pdo, $profile_id);
      insertEdu($pdo, $profile_id);

      $_SESSION['status'] = "Profile added";
      $_SESSION['color']  = "green";

      header("Location: index.php");
      return;
  }

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
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php flashMessage(); ?>

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

      <input type="submit" value="Add" class="btn btn-primary">
      <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">

    </form>
  </div>

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
