<?php
include_once 'php_includes/check_login_status.php';
$sql = "SELECT username, avatar FROM users WHERE avatar IS NOT NULL ORDER BY RAND()";
$query = mysqli_query($db_conx, $sql);
$userlist = '';
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
  $u = $row["username"];
  $avatar = $row["avatar"];
  $profile_pic = 'user/'.$u.'/'.$avatar;
  $userlist .= '<a href="user.php?u='.$u.'" title="'.$u.'"><img src="'.$profile_pic.'" alt="'.$u.'" style="width:100px;height:100px;margin:10px;"/></a>';
}
$sql = "SELECT COUNT(id) FROM users WHERE activated='1'";
$query = mysqli_query($db_conx, $sql);
$row = mysqli_fetch_row($query);
$usercount = $row[0];
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
