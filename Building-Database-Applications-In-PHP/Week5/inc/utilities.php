<?php 

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
