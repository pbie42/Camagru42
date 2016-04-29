<?php
if (isset($_GET['id']) && isset($_GET['u']) && isset($_GET['e']) && isset($_GET['p'])) {
  //Connect to database and sanitize incoming $_GET variables
  include_once '../config/database.php';
  $id = preg_replace('#[^0-9]#i', '', $_GET['id']);
  $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
  $e = mysqli_real_escape_string($db, $_GET['e']);
  $p = mysqli_real_escape_string($db, $_GET['p']);
  //Evaluate the lengths of the incoming $_GET variables
  if ($id = "" || strlen($u) < 3 || strlen($e) < 5 || strlen($p) > 1) {
    header('Location: http://localhost:8080/42/camagru/problem.php?msg=activation_string_length_issues');
    exit();
  }
  
  //Check their crednetials against the database

  //Evaluate for a match in the system (0 = no match, 1 = match)

  //Match was found, you can activate them

  //Optional double check to see if activated in fact now = 1

  //Evaluate the double check
} else {
  header('Location: http://localhost:8080/42/camagru/problem.php?msg=missing_GET_variables');
  exit();
}
?>
