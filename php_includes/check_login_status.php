<?php
  session_start();
  include_once 'db_conx.php';
  //FIles that include this file at the very top would NOT require
  //connection to database or session_start(), be careful.
  //Initialize some vars
  $user_ok = false;
  $log_id = "";
  $log_username = "";
  $log_password = "";
  //User verify function
  function evalLoggedUser($conx,$id,$u,$p)
  {
    $sql = "SELECT ip FROM users WHERE id='id' AND username='$u' AND username='$u' AND password='$p' AND activated='1' LIMIT 1";
    $query = mysqli_query($conx, $sql);
    $numrows = mysqli_num_rows($query);
    if($numrows > 0) {
      return true;
    }
  }
  if (isset($_SESSION["userid"]) && isset($_SESSION["username"]) && isset($_SESSION["password"])) {
    $log_id = preg_replace('#[^0-9]#', '', $_SESSION['userid']);
    $log_id = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
    $log_id = preg_replace('#[^a-z0-9]#i', '', $_SESSION['password']);
    //Verify the user
    $user_ok = evalLoggedUser($db_conx,$log_id,$log_username,$log_password);
  } else if (isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"])) {
    //TODO Finish this part in video (29:00)

  }
?>
