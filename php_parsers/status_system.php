<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['u']) && isset($_POST['np']) && $_POST['np'] != "" && $_POST['u'] != "") {
  $up = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
  $np = $_POST['np'];
  if ($up == "" || $np == "") {
    echo "There was a problem please try again.";
    exit();
  } elseif ($up != $log_username) {
    echo "This is not your account";
    exit();
  } elseif (strlen($np) < 5) {
    echo "Your new password is not long enough";
    exit();
  } else {
    $new_pass_hache = hash('whirlpool', $_POST['np']);
    $npquery = $db_conx2->prepare("UPDATE users SET password='$new_pass_hache' WHERE username='$log_username'");
    $npquery->execute();
    echo "pass_change_success";
    exit();
  }
  header("location: logout.php");
  exit();
}
?>
<?php
if (isset($_POST['photoid']) && isset($_POST['liker']) && isset($_POST['username']) && isset($_POST['action'])) {
  $photoid = preg_replace('#[^0-9]#', '', $_POST['photoid']);
  $liker = preg_replace('#[^a-z0-9 ]#i', '', $_POST['liker']);
  $username = preg_replace('#[^a-z0-9 ]#i', '', $_POST['username']);
  if ($_POST['action'] == "like") {
    $querylike = $db_conx2->prepare("INSERT INTO likes(osid, username, liker, likes) VALUES('$photoid','$username','$liker',1)");
    $querylike->execute();
    $queryphotolike = $db_conx2->prepare("UPDATE photos SET likes=likes+1 WHERE id='$photoid'");
    $queryphotolike->execute();
    $app = "Post Like";
    $note = '<span class="username">'.$liker.'</span> liked your post!<br /><a href="feed.php#post_'.$photoid.'">Click here to view the post</a>';
    $querylikenotify = $db_conx2->prepare("INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$username','$liker','$app','$note',now())");
    $querylikenotify->execute();
    echo "like_ok";
  } else if ($_POST['action'] == "unlike") {
    $querylikecheck = $db_conx2->prepare("SELECT likes FROM photos WHERE id='$photoid'");
    $querylikecheck->execute();
    $rowlikecheck = $querylikecheck->fetch(PDO::FETCH_ASSOC);
    $likesbefore = $rowlikecheck["likes"];
    //TODO Figure out why this isnt working. This is the last thing you need to fix!!!!!
    if (($likesbefore - 1) < 0) {
      echo "nice_try_asshole";
      exit();
    }
    $queryunlike = $db_conx2->prepare("DELETE FROM likes WHERE osid='$photoid' AND liker='$liker'");
    $queryunlike->execute();
    $queryphotounlike = $db_conx2->prepare("UPDATE photos SET likes=likes-1 WHERE id='$photoid'");
    $queryphotounlike->execute();
    $app = "Post Like";
    $queryunlikenotify = $db_conx2->prepare("DELETE FROM notifications WHERE username='$username' AND initiator='$liker' AND app='$app'");
    $queryunlikenotify->execute();
    echo "unlike_ok";
  }
  //TODO Set notification for when your photo is liked
  $db_conx2 = null;
  exit();
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "status_reply") {
  //First we make sure the data is not empty
  if (strlen($_POST['data']) < 1) {
    $db_conx2 = null;
    echo "data_empty";
    exit();
  }
  //If the data is not empty we need to clean the posted variables to make sure they are acceptable
  $osid = preg_replace('#[^0-9]#', '', $_POST['sid']);
  $account_name = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
  //htmlentities is a php function we are using to purge the reply of any kind of hack using javascript
  $data = htmlentities($_POST['data']);
  //Next we make sure the account name exists (the profile being posted on)
  $query_account = $db_conx2->prepare("SELECT COUNT(id) FROM users WHERE username='$account_name' AND activated='1' LIMIT 1");
  $query_account->execute();
  $row = $query_account->fetch(PDO::FETCH_NUM);
  if ($row[0] < 1) {
    $db_conx2 = null;
    echo "account_no_exit";
    exit();
  }
  //If everything is ok we are then going to insert the status reply post into the database
  $query_insert_reply = $db_conx2->prepare("INSERT INTO status(osid, account_name, author, type, data, postdate) VALUES('$osid','$account_name','$log_username','b','$data',now())");
  $query_insert_reply->execute();
  //We are using the mysqli_insert_id function because we want the reply id. We
  //send it back to ajax so that the user can instantly delete the reply if they
  //would like without having to page refresh. The post delete will requre this id number.
  $id = $db_conx2->lastInsertId();
  //Insert notifications for everybody in the conversation except the author
  $query_author = $db_conx2->prepare("SELECT author FROM status WHERE osid='$osid' AND author!='$log_username' GROUP BY author");
  $query_author->execute();
  while ($row = $query_author->fetch(PDO::FETCH_ASSOC)) {
    $participant = $row["author"];
    $app = "Post Comment";
    $note = '<span class="username">'.$log_username.'</span> commented here:<br /><a href="feed.php#post_'.$osid.'">Click here to view the conversation</a>';
    $query_notify = $db_conx2->prepare("INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$participant','$log_username','$app','$note',now())");
    $query_notify->execute();
  }
  $nquery = $db_conx2->prepare("SELECT user FROM photos WHERE id='$osid' LIMIT 1");
  $nquery->execute();
  while ($nrow = $nquery->fetch(PDO::FETCH_ASSOC)) {
    $tonotify = $nrow["user"];
  }
  if ($tonotify != $log_username) {
    $aquery = $db_conx2->prepare("SELECT email FROM users WHERE username='$tonotify' LIMIT 1");
    $aquery->execute();
    while ($arow = $aquery->fetch(PDO::FETCH_ASSOC)) {
      $e = $arow["email"];
    }
    $to = "$e";
    $from = "pbiecamagru42@gmail.com";
    $subject = "Camagru Notification PLEASE DO NOT RESPOND";
    $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Camagru Message</title><link href="https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa" rel="stylesheet" type="text/css"></head><body style="margin:0px; font-family:"Nunito", sans-serif;"><div style="padding:10px; background:rgb(117, 52, 52); font-size:24px; color:#CCC;"><span style="font-family:"Damion", cursive; font-size:30px;">Camagru </span><a href="http://localhost:8080/camagru/index.php"></a>Account Notification</div><div style="padding:24px; font-size:17px;">Hello '.$tonotify.',<br /><br /> '.$log_username.' has just commented on your post! Please log into our site and check your notifications page to see the update! Thank you!<br /><br /><br /><br /></b></div></body></html>';
    $headers = "From: $from\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
    mail($to, $subject, $message, $headers);
  }

  $db_conx2 = null;
  echo "reply_ok|$id";
  exit();
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "delete_reply") {
  if (!isset($_POST['replyid']) || $_POST['replyid'] == "") {
    $db_conx2 = null;
    exit();
  }
  $replyid = preg_replace('#[^0-9]#', '', $_POST['replyid']);
  //Check to make sure the person deleting this reply is either the account owner or the person who wrote it.
  $query_owner = $db_conx2->prepare("SELECT osid, account_name, author FROM status WHERE id='$replyid' LIMIT 1");
  $query_owner->execute();
  while ($row = $query_owner->fetch(PDO::FETCH_ASSOC)) {
    $osid = $row["osid"];
    $account_name = $row["account_name"];
    $author = $row["author"];
  }
  if ($author == $log_username || $account_name == $log_username) {
    $query_delete_reply = $db_conx2->prepare("DELETE FROM status WHERE id='$replyid' LIMIT 1");
    $query_delete_reply->execute();
    $db_conx2 = null;
    echo "delete_ok";
    exit();
  }
}
?>
