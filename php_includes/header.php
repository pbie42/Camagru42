<?php
$headerOptions = "";
$envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
if ($user_ok == true) {
  $sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  $notescheck = $row[0];
  $sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $numrows = mysqli_num_rows($query);
  if ($numrows == 0) {
    $envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
  } else {
    $envelope = '<li id="alerts" class="logo_font menuitem"><a href="notifications.php" style="color:white;"><img style="height:25px;width:25px;" src="resources/notify2.png" alt="" /></a></li>';
  }
}
?>

<header>
  <div id="header_top">
    <div id="brand">
      <a href="index.php">
        <h1 class="logo_font">Camagru</h1>
      </a>
      <a href="index.php">
        <img id="logo_header" src="resources/retrocam.png" alt="" />
      </a>
    </div> <!-- brand -->

    <div id="menu">

<?php
  if ($_SESSION['username'] != "")
  {
?>
      <input class="menu-btn" type="checkbox" id="menu-btn" />
      <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
      <ul id="menu_bottom" class="menu">
        <?php echo $envelope; ?>
        <li class="logo_font menuitem"><a href="user.php?u=<?php echo $_SESSION['username']; ?>"><img id="usericon" style="height:27px;width:20px;" src="resources/user.png" alt="" /></a></li>
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
