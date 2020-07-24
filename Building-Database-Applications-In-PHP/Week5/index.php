<?php 

  session_start();

  $logged_in = false;
  $autos = array();

  if(isset($_SESSION['name'])) {
    $logged_in = true;
    $status = false;

    if(isset($_SESSION['status'])) {
      $status = htmlentities($_SESSION['status']);
      $status_colour = htmlentities($_SESSION['color']);

      unset($_SESSION['status']);
      unset($_SESSION['color']);
    }

    require_once 'pdo.php';

    $stmt = $pdo->query("SELECT * FROM db");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $autos[] = $row;
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />

    <title>Nilesh D</title>

    <style>

      table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      }

    </style>

  </head>

  <body>
    <div class="container">
      
      <h1>Welcome to the Automobiles Database</h1>

      <?php if( !$logged_in ) : ?>
        
        <p><a href="login.php">Please log in</a></p>
        <p>Attempt to <a href="add.php">Add Data</a> without logging in</p>
      
      <?php else : ?>

        <?php 
          if($status != false) {
            echo('<p style="color: '.$status_colour.';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
          }
        ?>

        <?php if(empty($autos)) : ?>
          <p>No autos found</p>
        
        <?php else : ?>
          <table>
            
            <thead>
              <tr>
                <th>Make</th>
                <th>Model</th>
                <th>Year</th>
                <th>Mileage</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach($autos as $auto) : ?>

                <tr>
                  <td><?php echo $auto['make']; ?></td>
                  <td><?php echo $auto['model']; ?></td>
                  <td><?php echo $auto['year']; ?></td>
                  <td><?php echo $auto['mileage']; ?></td>
                  <td>
                    <a href="edit.php?autos_id=<?php echo $auto['auto_id']; ?>">Edit</a>
                    / 
                    <a href="delete.php?autos_id=<?php echo $auto['auto_id']; ?>">Delete</a>
                  </td>
                </tr>

              <?php endforeach; ?>

            </tbody>
          </table>
      
        <?php endif; ?>
    
        <p>
          <a href="add.php">Add New Entry</a>
        </p>

        <p>
          <a href="logout.php">Logout</a>
        </p>
      
      <?php endif; ?>

    </div>
  </body>
</html>
