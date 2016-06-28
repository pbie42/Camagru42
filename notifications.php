<?php
include_once 'php_includes/check_login_status.php';
if ($user_ok != true || $log_username == "") {
  header("location: index.php");
  exit();
}
$notification_list = "";
$query_notifications = $db_conx2->prepare("SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC LIMIT 10");
$query_notifications->execute();
$numrows_notifications = $query_notifications->fetchColumn();
if ($numrows_notifications < 1) {
  $notification_list = "<p>You do not have any notifications</p>";
} else {
  $query_notifications2 = $db_conx2->prepare("SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC LIMIT 10");
  $query_notifications2->execute();
  while ($row = $query_notifications2->fetch(PDO::FETCH_ASSOC)) {
    $noteid = $row["id"];
    $initiator = $row["initiator"];
    $app = $row["app"];
    $note = $row["note"];
    $date_time = $row["date_time"];
    $date_time = strftime("%b %d, %Y", strtotime($date_time));
    $notification_list .= "<p>$note</p>";
  }
}
$query_notecheck = $db_conx2->prepare("UPDATE users SET notescheck=now() WHERE username='$log_username' LIMIT 1");
$query_notecheck->execute();
?>
<?php
$friend_requests = "";
$query_friend_requests = $db_conx2->prepare("SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC");
$query_friend_requests->execute();
$numrows_friend_requests = $query_friend_requests->fetchColumn();
if ($numrows_friend_requests < 1) {
  $friend_requests = '<p>You do not have any friend requests</p>';
} else {
  $query_friend_requests2 = $db_conx2->prepare("SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC");
  $query_friend_requests2->execute();
  while ($row = $query_friend_requests2->fetch(PDO::FETCH_ASSOC)) {
    $reqID = $row["id"];
    $user1 = $row["user1"];
    $datemade = $row["datemade"];
    $datemade = strftime("%B %d", strtotime($datemade));
    $thumbquery = $db_conx2->prepare("SELECT avatar FROM users WHERE username='$user1' LIMIT 1");
    $thumbquery->execute();
    $thumbrow = $thumbquery->fetch(PDO::FETCH_NUM);
    $user1avatar = $thumbrow[0];
    $user1pic = '<img src="user/'.$user1.'/'.$user1avatar.'" alt="'.$user1.'" class="user_pic" />';
    if ($user1avatar == NULL) {
      $user1pic = '<img style="height:60px;width:50px;" src="resources/user.png" alt="'.$user1.'" class="user_pic" />';
    }
    $friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
    $friend_requests .= '<a href="user.php?u='.$user1.'">'.$user1pic.'</a>';
    $friend_requests .= '<div class="user_info" id="user_info_'.$reqID.'">'.$datemade.': <a style="color:rgb(117, 52, 52);" href="user.php?u='.$user1.'">'.$user1.'</a> requests friendship<br /><br />';
    $friend_requests .= '<button class="acceptrejectbuttons" onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">Accept</button> or ';
    $friend_requests .= '<button class="acceptrejectbuttons" onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">Reject</button>';
    $friend_requests .= '</div>';
    $friend_requests .= '</div>';
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="css/camagru.css" media="screen" title="no title" charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="js/camagru.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/notifications.js"></script>
  </head>
  <body>
    <div id="container">
      <?php include_once 'php_includes/header.php'; ?>
      <div id="body">
        <div id="message_section">
          <div class="main_area_notes welcome_font">
            <h1 id="notificationtitle" class="welcome_font">Friend Requests</h1>
            <hr>
            <?php echo $friend_requests; ?>
          </div>
          <div id="main_area_notes_notif" class="main_area_notes welcome_font">
            <h1 id="notificationtitle" class="welcome_font">Notifications</h1>
            <hr>
            <?php echo $notification_list; ?>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
  </body>
</html>
