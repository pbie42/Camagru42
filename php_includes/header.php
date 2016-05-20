<?php
$envelope 
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
        <!--<li class="welcome_font menuitem"><a class="" href="account.php"><?php echo htmlspecialchars($_SESSION['logged_on_user']); ?></a></li>-->
        <li class="logo_font menuitem"><a href="user.php?u=<?php echo $_SESSION['username']; ?>"><?php echo $_SESSION['username']; ?></a></li>
        <li id="logout" class="logo_font menuitem" onclick="home()"><a href="logout.php">Sign Out</a></li>
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
