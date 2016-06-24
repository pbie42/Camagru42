<?php
include_once '../php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
if (isset($_POST['photoid']) && isset($_POST['liker']) && isset($_POST['username']) && isset($_POST['action'])) {
  $photoid = preg_replace('#[^0-9]#', '', $_POST['photoid']);
  $liker = preg_replace('#[^a-z0-9 ]#i', '', $_POST['liker']);
  $username = preg_replace('#[^a-z0-9 ]#i', '', $_POST['username']);
  if ($_POST['action'] == "like") {
    $sqllike = "INSERT INTO likes(osid, username, liker, likes) VALUES('$photoid','$username','$liker',1)";
    $querylike = mysqli_query($db_conx, $sqllike);
    $sqlphotolike = "UPDATE photos SET likes=likes+1 WHERE id='$photoid'";
    $queryphotolike = mysqli_query($db_conx, $sqlphotolike);
    $app = "Post Like";
    $note = '<span class="username">'.$liker.'</span> liked your post!<br /><a href="feed.php#post_'.$photoid.'">Click here to view the post</a>';
    mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$username','$liker','$app','$note',now())");
    echo "like_ok";
  } else if ($_POST['action'] == "unlike") {
    $sqlunlike = "DELETE FROM likes WHERE osid='$photoid' AND liker='$liker'";
    $queryunlike = mysqli_query($db_conx, $sqlunlike);
    $sqlphotounlike = "UPDATE photos SET likes=likes-1 WHERE id='$photoid'";
    $queryphotounlike = mysqli_query($db_conx, $sqlphotounlike);
    $app = "Post Like";
    mysqli_query($db_conx, "DELETE FROM notifications WHERE username='$username' AND initiator='$liker' AND app='$app'");
    echo "unlike_ok";
  }
  //TODO Set notification for when your photo is liked
  mysqli_close($db_conx);
  exit();
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "status_post") {
  //Make sure the post data is not empty
  if (strlen($_POST['data']) < 1) {
    mysqli_close($db_conx);
    echo "data_empty";
    exit();
  }
  //Next we make sure the post is a type a or c
  if ($_POST['type'] != "a" && $_POST['type'] != "c") {
    mysqli_close($db_conx);
    echo "type_unknown";
    exit();
  }
  //Then we sanatize the $_POST variables that we will be given
  $type = preg_replace('#[^a-z]#', '', $_POST['type']);
  $account_name = preg_replace('#[^a-z0-9 ]#i', '', $_POST['user']);
  //As with the status_reply section below we are using htmlentites to prevent malicious code.
  $data = htmlentities($_POST['data']);
  $data = mysqli_real_escape_string($db_conx, $data);
  //Now we make sure the account name exists like we did in the status_reply section
  $sql = "SELECT COUNT(id) FROM users WHERE username='$account_name' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  if ($row[0] < 1) {
    mysqli_close($db_conx);
    echo "account_no_exit";
    exit();
  }
  //We then insert the status post into the database
  $sql = "INSERT INTO status(account_name, author, type, data, postdate) VALUES('$account_name','$log_username','$type','$data',now())";
  $query = mysqli_query($db_conx, $sql);
  $id = mysqli_insert_id($db_conx);
  mysqli_query($db_conx, "UPDATE status SET osid='$id' WHERE id='$id' LIMIT 1");
  //Then we count posts of type a for the person posting and evalute the count.
  $sql = "SELECT COUNT(id) FROM status WHERE author='$log_username' AND type='a'";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  //This part below is for if I want to have a system that auto flushes the oldest comments.
  //This can also be used to auto flush type c and b as well. I will leave this part
  //commented out because I will want to keep all of the posts. But good to look at in the future.
  /*if ($row[0] > 9) {
    $sql = "SELECT id FROM status WHERE author='log_username' AND type='a' ORDER BY id ASC LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $oldest = $row[0];
    //This deletes all the posts associated with the original status ID
    //similar to deleting all the posts in a long forum thread.
    mysqli_query($db_conx, "DELETE FROM status WHERE osid='$oldest'");
  }*/
  //Last we are going to send notifcations to all of the friends of the post author.
  //TODO need to decide whether I will allow this or not. Probably should for a bonus
  //Can also use this for things other than comments such as whenever someone makes a
  //new photo post. All I would have to do is adapt this for my photo_system parsing.
  $friends = array();
  $query = mysqli_query($db_conx, "SELECT user1 FROM friends WHERE user2='$log_username' AND accepted='1'");
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    array_push($friends, $row["user1"]);
  }
  $query = mysqli_query($db_conx, "SELECT user2 FROM friends WHERE user1='$log_username' AND accepted='1'");
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    array_push($friends, $row["user2"]);
  }
  for ($i=0; $i < count($friends); $i++) {
    $friend = $friends[$i];
    $app = "Status Post";
    $note = $log_username.' posted on: <br /><a href="user.php?u='.$account_name.'#status_'.$id.'">'.$account_name.'&#39;s Profile</a>';
    mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$friend','$log_username','$app','$note',now())");
  }
  mysqli_close($db_conx);
  echo "post_ok|$id";
  exit();
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "status_reply") {
  //First we make sure the data is not empty
  if (strlen($_POST['data']) < 1) {
    mysqli_close($db_conx);
    echo "data_empty";
    exit();
  }
  //If the data is not empty we need to clean the posted variables to make sure they are acceptable
  $osid = preg_replace('#[^0-9]#', '', $_POST['sid']);
  $account_name = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
  //htmlentities is a php function we are using to purge the reply of any kind of hack using javascript
  $data = htmlentities($_POST['data']);
  $data = mysqli_real_escape_string($db_conx, $data);
  //Next we make sure the account name exists (the profile being posted on)
  $sql = "SELECT COUNT(id) FROM users WHERE username='$account_name' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  if ($row[0] < 1) {
    mysqli_close($db_conx);
    echo "account_no_exit";
    exit();
  }
  //If everything is oke we are then going to insert the status reply post into the database
  $sql = "INSERT INTO status(osid, account_name, author, type, data, postdate) VALUES('$osid','$account_name','$log_username','b','$data',now())";
  $query = mysqli_query($db_conx, $sql);
  //We are using the mysqli_insert_id function because we want the reply id. We
  //send it back to ajax so that the user can instantly delete the reply if they
  //would like without having to page refresh. The post delete will requre this id number.
  $id = mysqli_insert_id($db_conx);
  //Insert notifications for everybody in the conversation except the author
  $sql = "SELECT author FROM status WHERE osid='$osid' AND author!='$log_username' GROUP BY author";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $participant = $row["author"];
    $app = "Post Comment";
    $note = '<span class="username">'.$log_username.'</span> commented here:<br /><a href="feed.php#post_'.$osid.'">Click here to view the conversation</a>';
    mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$participant','$log_username','$app','$note',now())");
  }
  $nsql = "SELECT user FROM photos WHERE id='$osid' LIMIT 1";
  $nquery = mysqli_query($db_conx, $nsql);
  while ($nrow = mysqli_fetch_array($nquery, MYSQLI_ASSOC)) {
    $tonotify = $nrow["user"];
  }
  if ($tonotify != $log_username) {
    $asql = "SELECT email FROM users WHERE username='$tonotify' LIMIT 1";
    $aquery = mysqli_query($db_conx, $asql);
    while ($arow = mysqli_fetch_array($aquery, MYSQLI_ASSOC)) {
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

  mysqli_close($db_conx);
  echo "reply_ok|$id";
  exit();
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "delete_status") {
  if (!isset($_POST['statusid']) || $_POST['statusid'] == "") {
    mysqli_close($db_conx);
    echo "status id is missing";
    exit();
  }
  $statusid = preg_replace('#[^0-9]#', '', $_POST['statusid']);
  //Check to make sure this logged in user actually owns the comment they wish to delete
  $query = mysqli_query($db_conx, "SELECT account_name, author FROM status WHERE id='$statusid' LIMIT 1");
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $account_name = $row["account_name"];
    $author = $row["author"];
  }
  if ($author == $log_username || $account_name == $log_username) {
    mysqli_query($db_conx, "DELETE FROM status WHERE osid='$statusid'");
    mysqli_close($db_conx);
    echo "delete_ok";
    exit();
  }
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == "delete_reply") {
  if (!isset($_POST['replyid']) || $_POST['replyid'] == "") {
    mysqli_close($db_conx);
    exit();
  }
  $replyid = preg_replace('#[^0-9]#', '', $_POST['replyid']);
  //Check to make sure the person deleting this reply is either the account owner or the person who wrote it.
  $query = mysqli_query($db_conx, "SELECT osid, account_name, author FROM status WHERE id='$replyid' LIMIT 1");
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $osid = $row["osid"];
    $account_name = $row["account_name"];
    $author = $row["author"];
  }
  if ($author == $log_username || $account_name == $log_username) {
    mysqli_query($db_conx, "DELETE FROM status WHERE id='$replyid' LIMIT 1");
    mysqli_close($db_conx);
    echo "delete_ok";
    exit();
  }
}
?>
