<?php
$headerOptions = "";
$envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
if ($user_ok == true) {
  $query_note_check = $db_conx2->prepare("SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1");
  $query_note_check->execute();
  $row = $query_note_check->fetch(PDO::FETCH_NUM);
  $notescheck = $row[0];
  $query_notifications = $db_conx2->prepare("SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1");
  $query_notifications->execute();
  $numrows = $query_notifications->fetchColumn();
  if ($numrows == 0) {
    $envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
  } else {
    $envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php" style="color:white;"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
  }
}
?>

<header>
  <div id="header_top">
    <a href="index.php">
    <div id="brand">
      <?php
        include_once 'php_includes/check_login_status.php';
        if ($user_ok == true || $log_username != "") {
        ?>
        <a href="feed.php">
          <h1 class="logo_font">Camagru</h1>
        </a>
        <a href="feed.php">
          <img id="logo_header" src="resources/retrocam.png" alt="" />
        </a>
      <?php
        } else {
      ?>
        <a href="index.php">
          <h1 class="logo_font">Camagru</h1>
        </a>
        <a href="index.php">
          <img id="logo_header" src="resources/retrocam.png" alt="" />
        </a>
      <?php
        }
      ?>





    </div> <!-- brand -->

    <div id="menu">
    </a>
<?php
  if ($_SESSION['username'] != "")
  {
?>
      <input class="menu-btn" type="checkbox" id="menu-btn" />
      <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
      <ul id="menu_bottom" class="menu">
        <?php echo $envelope; ?>
        <li class="logo_font menuitem"><a href="user.php?u=<?php echo $_SESSION['username']; ?>"><img id="usericon" style="height:27px;width:20px;" src="resources/user.png" alt="" /></a></li>
        <li id="feed" class="logo_font menuitem"><a href="feedpublic.php"><img style="height:27px;width:27px;" src="resources/feed.png" alt="" /></a></li>

        <li id="logout" class="logo_font menuitem" onclick="home()"><a href="logout.php"><img style="height:27px;width:27px;" src="resources/logout.png" alt="" /></a></li>
<?php
  }
  else
  {
?>
<?php
  }
 ?>
      </ul>

    </div> <!-- menu -->
  </div> <!-- header_top -->
  <script type="text/javascript">
  function home() {
    location.href = "index.php";
  };
  </script>
</header>
