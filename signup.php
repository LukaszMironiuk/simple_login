<?php

session_start();
require_once('db_config.php');

if (isset($_POST['signup']))
{
	echo "start reg";
	$user->register($_POST['name'],$_POST['email'],$_POST['password']);
}

if (isset($_GET['id']) && isset($_GET['code']))
{
	if ($user->activate_user($_GET['id'],$_GET['code']))
	{
		echo "user activated";
	} else {
		"user isn't active!";
	}
}

?>

<form method="post">
	<?php
		if (isset($user->errors))
		{
			echo "<ul>";
			foreach($user->errors as $error)
			{
				echo "<li>" . $error . "</li>";
			}
			echo "</ul>";
		}
	?>
	<input type="text" name="name" placeholder="nickname" required>
	<input type="text" name="email" placeholder="email" required>
	<input type="password" name="password" placeholder="password" required>
	<input type="submit" name="signup">
</form>
<a href="login.php">Login</a>
