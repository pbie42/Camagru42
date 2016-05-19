<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['type']) && isset($_POST['user'])) {
  $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
  $sql = "SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $exist_count = mysqli_fetch_row($query);
  if ($exist_count < 1) {
    mysqli_close($db_conx);
    echo "user does not exist.";
    exit();
  }
  if ($_POST['type'] == "friend") {
    $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user' AND accepted='1' OR user2='$user' AND accepted='1'";
    $query = mysqli_query($db_conx, $sql);
    $friend_count = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$user' AND blockee='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $blockcount1 = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$log_username' AND blockee='$user' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $blockcount2 = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row_count1 = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row_count2 = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='0' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row_count3 = mysqli_fetch_row($query);
    $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='0' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row_count4 = mysqli_fetch_row($query);
    if ($blockcount1[0] > 0) {
      mysqli_close($db_conx);
      echo "You are currently blocked by $user, we can not proceed";
      exit();
    } else if ($blockcount2[0] > 0) {
      mysqli_close($db_conx);
      echo "You must first unblock $user in order to proceed";
      exit();
    }//TODO Finish this part of video at 21:50
  }
}
?>
