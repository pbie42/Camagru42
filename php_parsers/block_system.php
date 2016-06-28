<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['type']) && isset($_POST['blockee'])) {
  $blockee = preg_replace('#[^a-z0-9]#i', '', $_POST['blockee']);
  $query_blockee = $db_conx2->prepare("SELECT COUNT(id) FROM users WHERE username='$blockee' AND activated=1' LIMIT 1'");
  $query_blockee->execute();
  $exist_count = $query_blockee->fetch(PDO::FETCH_NUM);
  if ($exist_count[0] < 1) {
    //TODO research the necessity of closing the database connection and if I feel it is necessary I need to go back through all of my code where I exit() and add a mysqli_close
    $db_conx2 = null;
    echo "$blockee does not exist";
    exit();
  }
  $query_blocked_id = $db_conx2->prepare("SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$blockee' LIMIT 1");
  $query_blocked_id->execute();
  $numrows = $query_blocked_id->fetchColumn();
  if ($_POST['type'] == "block") {
    if ($numrows > 0) {
      $db_conx2 = null;
      echo "You already have this member blocked";
      exit();
    } else {
      $query_insert_block = $db_conx2->prepare("INSERT INTO blockedusers(blocker, blockee, blockdate) VALUES('$log_username','$blockee',now())");
      $query_insert_block->execute();
      $db_conx2 = null;
      echo "blocked_ok";
      exit();
    }
  } else if ($_POST['type'] == "unblock") {
    if ($numrows == 0) {
      $db_conx2 = null;
      echo "You do not have this user blocked";
      exit();
    } else {
      $query_remove_block = $db_conx2->prepare("DELETE FROM blockedusers WHERE blocker='$log_username' AND blockee='$blockee' LIMIT 1");
      $query_remove_block->execute();
      $db_conx2 = null;
      echo "unblocked_ok";
      exit();
    }
  }
}
?>
