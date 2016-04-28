<?php

$server = "mysql1.alwaysdata.com";
$user = "pbie";
$password = "4242";
$dbname = "pbie_camagru";

$db = mysqli_connect($server, $user, $password, $dbname);

if (!$db)
{
	echo "error: " . mysqli_connect_error();
}

?>
