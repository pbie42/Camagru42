<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['type']) && isset($_POST['user'])) {
  $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
  $query_count1 = $db_conx2->prepare("SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1");
  $query_count1->execute();
  $exist_count = $query_count1->fetchColumn();
  if ($exist_count < 1) {
    $db_conx2 = null;
    echo "user does not exist.";
    exit();
  }
  if ($_POST['type'] == "friend") {
    $query_count2 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$user' AND accepted='1' OR user2='$user' AND accepted='1'");
    $query_count2->execute();
    $friend_count = $query_count2->fetch(PDO::FETCH_NUM);
    $query_count3 = $db_conx2->prepare("SELECT COUNT(id) FROM blockedusers WHERE blocker='$user' AND blockee='$log_username' LIMIT 1");
    $query_count3->execute();
    $blockcount1 = $query_count3->fetch(PDO::FETCH_NUM);
    $query_count4 = $db_conx2->prepare("SELECT COUNT(id) FROM blockedusers WHERE blocker='$log_username' AND blockee='$user' LIMIT 1");
    $query_count4->execute();
    $blockcount2 = $query_count4->fetch(PDO::FETCH_NUM);
    $query_count5 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1' LIMIT 1");
    $query_count5->execute();
    $row_count1 = $query_count5->fetch(PDO::FETCH_NUM);
    $query_count6 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1");
    $query_count6->execute();
    $row_count2 = $query_count6->fetch(PDO::FETCH_NUM);
    $query_count7 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='0' LIMIT 1");
    $query_count7->execute();
    $row_count3 = $query_count7->fetch(PDO::FETCH_NUM);
    $query_count8 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='0' LIMIT 1");
    $query_count8->execute();
    $row_count4 = $query_count8->fetch(PDO::FETCH_NUM);
    if ($blockcount1[0] > 0) {
      $db_conx2 = null;
      echo "You are currently blocked by $user, we can not proceed";
      exit();
    } else if ($blockcount2[0] > 0) {
      $db_conx2 = null;
      echo "You must first unblock $user in order to proceed";
      exit();
    } else if ($row_count1[0] > 0 || $row_count2[0] > 0) {
      $db_conx2 = null;
      echo "You are already friends with $user.";
      exit();
    } else if ($row_count3[0] > 0) {
      $db_conx2 = null;
      echo "You have already sent a pending friend request to $user";
      exit();
    } else if ($row_count4[0] > 0) {
      $db_conx2 = null;
      echo "$user has alredy requested to be your friend. Please check your friend requests.";
      exit();
    } else {
      $query_add_friend = $db_conx2->prepare("INSERT INTO friends(user1, user2, datemade) VALUES('$log_username','$user',now())");
      $query_add_friend->execute();
      $db_conx2 = null;
      echo "friend_request_sent";
      exit();
    }
  } else if ($_POST['type'] == "unfriend") {
    $query_count9 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1' LIMIT 1");
    $query_count9->execute();
    $row_count1 = $query_count9->fetch(PDO::FETCH_NUM);
    $query_count10 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1");
    $query_count10->execute();
    $row_count2 = $query_count10->fetch(PDO::FETCH_NUM);
    if ($row_count1[0] > 0) {
      $query_unfriend1 = $db_conx->prepare("DELETE FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1' LIMIT 1");
      $query_unfriend1->execute();
      $db_conx2 = null;
      echo "unfriend_ok";
      exit();
    } else if ($row_count2[0] > 0) {
      $query_unfriend2 = $db_conx2->prepare("DELETE FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1");
      $query_unfriend2->execute();
      $db_conx2 = null;
      echo "unfriend_ok";
      exit();
    } else {
      $db_conx2 = null;
      echo "No friendship was found between your account and $user";
      exit();
    }
  }
}
?>
<?php
if (isset($_POST['action']) && isset($_POST['reqid']) && isset($_POST['user1'])) {
  $reqid = preg_replace('#[^0-9]#', '', $_POST['reqid']);
  $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user1']);
  $query_count_request = $db_conx2->prepare("SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1");
  $query_count_request->execute();
  $exist_count = $query_count_request->fetch(PDO::FETCH_NUM);
  if ($exist_count[0] < 1) {
    $db_conx2 = null;
    echo "$user does not exist.";
    exit();
  }
  if ($_POST['action'] == "accept") {
    $query_count_request1 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1' LIMIT 1");
    $query_count_request1->execute();
    $row_count1 = $query_count_request1->fetch(PDO::FETCH_NUM);
    $query_count_request2 = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1");
    $query_count_request2->execute();
    $row_count2 = $query_count_request2->fetch(PDO::FETCH_NUM);
    if ($row_count1[0] > 0 || $row_count2[0] > 0) {
      $db_conx2 = null;
      echo "You are already friends with $user";
      exit();
    } else {
      $query_accept_friend = $db_conx2->prepare("UPDATE friends SET accepted='1' WHERE id='$reqid' AND user1='$user' AND user2='$log_username' LIMIT 1");
      $query_accept_friend->execute();
      $db_conx2 = null;
      echo "accept_ok";
      exit();
    }
  } else if ($_POST['action'] == "reject") {
    $query_reject_friend = $db_conx2->prepare("DELETE FROM friends WHERE id='$reqid' AND user1='$user' AND user2='$log_username' AND accepted='1' LIMIT 1");
    $query_reject_friend->execute();
    $db_conx2 = null;
    echo "reject_ok";
    exit();
  }
}
?>
