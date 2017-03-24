<?php

require_once('db_config.php');
session_start();
if (!$user->is_logged_in())
{
	$user->redirect('login.php');
} 

?>

<html>
	<head>
	</head>
	<body>

	


	<h1>Logged in <?php echo $_SESSION['name']; ?></h1>


	<a href="logout.php">Logout</a>
	</body>
</html>

