<?php

$db_host = "127.0.0.1";
$db_name = "simple_login";
$db_user = "root";
$db_password = "password";

try {
	$db = new PDO("mysql:host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	echo $e->getMessage();
}

include('class/user.php');
$user = new User ($db);
