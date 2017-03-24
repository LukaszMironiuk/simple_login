<?php

require_once('db_config.php');
session_start();

if (isset($_POST['login']))
{
	//echo "logged";
	$user->login($_POST['name'], $_POST['password']);
}

?>


<form method="post">
	<p>
	<?php 
		if (isset($user->errors)){
			echo "<ul>";
			foreach ($user->errors as $error)
			{
				echo "<li>" . $error . "</li>";
			}
			echo "</ul>";
		}
	?></p>
	<input type="text" name="name" required>
	<input type="password" name="password" required>
	<input type="submit" name="login" value="Log in">
</form>
<a href="signup.php">Sign up</a>