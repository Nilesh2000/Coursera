<?php 

	session_start();

	require_once 'inc/pdo.php';

  $logged_in = false;
  $profiles  = array();

  if( isset($_SESSION['name']) ) {
		$logged_in = true;
		$status = false;

		if(isset($_SESSION['status'])) {
			$status 			= htmlentities($_SESSION['status']);
			$status_color = htmlentities($_SESSION['color']);

			unset($_SESSION['status']);
			unset($_SESSION['color']);
		}
	}

	$sql 			= "SELECT * FROM profile";
	$stmt 		= $pdo->query($sql);
	$profiles = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<style>
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
	</style>

  <title>Nilesh D</title>
</head>

<body>
  <div class="container">
  	<h1>Nilesh D's Resume Registry</h1>

		<?php if(!$logged_in) : ?>

			<p><a href="login.php">Please log in</a></p>
			<?php if(empty($profiles)) : ?>
				<p>No rows found</p>

			<?php else: ?>

				<table>
					<thead>
						<tr>
							<th>Name</th>
							<th>Headline</th>
						</tr>
					</thead>
					<tbody>

						<?php foreach($profiles as $profile) : ?>
							<tr>
								<td>
									<a href="view.php?profile_id=<?= $profile['profile_id']; ?>">
										<?php echo $profile['first_name'] . ' ' . $profile['last_name']; ?>
									</a>
								</td>
								<td>
									<?= $profile['headline']; ?>
								</td>
							</tr>
						<?php endforeach; ?>

					</tbody>
				</table>

			<?php endif; ?>

		<?php else : ?>

			<?php 
				if($status != false) {
      		echo('<p style="color: '. $status_color. ';" class="col-sm-10 col-sm-offset-2">'.htmlentities($status)."</p>\n");
				}
			?>

			<p>
				<a href="logout.php">Logout</a>
			</p>

			<?php if(empty($profiles)) : ?>
				<p>No rows found</p>
				
			<?php else : ?>

				<div class="row">
					<div class="col-sm-8">

						<table>
							<thead>
								<tr>
									<th>Name</th>
									<th>Headline</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach($profiles as $profile) : ?>
									<tr>

										<td>
											<a href="view.php?profile_id=<?= $profile['profile_id']; ?>">
												<?php echo $profile['first_name'] . ' ' . $profile['last_name']; ?>
											</a>
										</td>

										<td>
											<?= $profile['headline']; ?>
										</td>

										<td>
											<a href="edit.php?profile_id=<?= $profile['profile_id']; ?>">
												Edit
											</a>
											/
											<a href="delete.php?profile_id=<?= $profile['profile_id']; ?>">
												Delete
											</a>
										</td>

									</tr>
								<?php endforeach; ?>

							</tbody>
						</table>

					</div>
				</div>

			<?php endif; ?>  
			
			<p>
				<a href="add.php">Add New Entry</a>
			</p>
			
		<?php endif; ?>
		
  </div>
</body>
</html>
