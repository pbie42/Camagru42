<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['type']) && isset($_POST['blockee'])) {
  $blockee = preg_replace('#[^a-z0-9]#i', '', $_POST['blockee']);
  $sql = "SELECT COUNT(id) FROM users WHERE username='$blockee' AND activated=1' LIMIT 1'";
  $query = mysqli_query($db_conx, $sql);
  $exist_count = mysqli_fetch_row($query);
  if ($exist_count[0] < 1) {
    //TODO research the necessity of closing the database connection and if I feel it is necessary I need to go back through all of my code where I exit() and add a mysqli_close
    mysqli_close($db_conx);
    echo "$blockee does not exist";
    exit();
  }
  $sql = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$blockee' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $numrows = mysqli_num_rows($query);
  if ($_POST['type'] == "block") {
    if ($numrows > 0) {
      mysqli_close($db_conx);
      echo "You already have this member blocked";
      exit();
    } else {
      $sql = "INSERT INTO blockedusers(blocker, blockee, blockdate) VALUES('$log_username','$blockee',now())";
      $query = mysqli_query($db_conx, $sql);
      mysqli_close($db_conx);
      echo "blocked_ok";
      exit();
    }
  } else if () {
    //TODO finish this part of the video 17:40
    # code...
  }
}
?>
