<?php
include 'config/database.php';
$db_conx = mysqli_connect("localhost", "root", "root", "camagru_test");
try {
    $db_conx2 = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
//Evaluate the connection
if (mysqli_connect_errno()) {
  echo mysqli_connect_error();
  exit();
}
 ?>
