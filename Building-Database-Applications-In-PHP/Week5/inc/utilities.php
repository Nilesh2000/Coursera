<?php 

  function flashMessage() {
    if( isset($_SESSION['status']) ) {
      echo('<p style="color: '. $_SESSION['color']. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($_SESSION['status'])."</p>\n");
      unset($_SESSION['status']);
      unset($_SESSION['color']);
    }
  }

  function validateProfile() {
    if( strlen($_POST['make']) == 0 || strlen($_POST['model']) == 0 || strlen($_POST['year']) == 0 || strlen($_POST['mileage']) == 0 ) {
      return "All fields are required";
    }

    if( !is_numeric($_POST['year']) ) {
      return "Year must be an integer";
    }

    if( !is_numeric($_POST['mileage']) ) {
      return "Mileage must be an integer";
    }
    return true;
  }
  
?>
