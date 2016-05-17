<?php
  //If user is already logged in I header them away
  if (isset($_SESSION["username"])) {
    //header("location: localhost:8080/camagru/user.php?u=".$_SESSION["username"]);
    exit();
  }
?>
<?php
if (isset($_POST["logincheck"])) {
  include_once 'php_includes/db_conx.php';
  $username = preg_replace('#[^a-z0-9]#i', '', $_POST['logincheck']);
  $sql = "SELECT id FROM users WHERE username='$username' AND activated='1' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $login_check = mysqli_num_rows($query);
  if ($login_check < 1) {
    echo '<strong style="color:#F00;">Account not yet activated!</strong>';
    exit();
  }
  else {
    exit();
  }
}
 ?>
<?php
  if (isset($_POST["u"])) {
    //Connect to the database
    include_once 'php_includes/db_conx.php';
    //Gather the posted data into local variables and sanitize
    $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
    $p = md5($_POST['p']);
    //Get user IP address
    $ip = preg_replace('#[^0-9]#', '', getenv('REMOTE_ADDR'));
    //Form data error handling
    if ($u == "" || $p == "") {
      echo "login_failed";
      exit();
    } else {
      //End form data error handling
      $sql = "SELECT id, username, password FROM users WHERE username='$u' AND password='$p' AND activated='1' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
      $row = mysqli_fetch_row($query);
      $db_id = $row[0];
      $db_username = $row[1];
      $db_pass_str = $row[2];
      if ($p != $db_pass_str){
        echo "login_failed";
        exit();
      } else {
      //Create their sessions and cookies
      $_SESSION['userid'] = $db_id;
      $_SESSION['username'] = $db_username;
      $_SESSION['password'] = $db_pass_str;
      setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
      setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
      setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE);
      //Update their "IP" and "LASTLOGIN" fields
      $sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
      echo $db_username;
      exit();
      }
    }
    exit();
  }
?>

<div id="login_section">
  <div class="main_area">
    <h1 class="welcome_font">Welcome to</h1>
    <h1 id="login_logo" class="logo_font">Camagru</h1>
    <h3 class="welcome_font">Please log in to see photos<br/> from you and your friends</h3>
    <form id="login_form" action="index.php" method="post">
      <!--TODO Need to clean this part up when the user hasn't activated their account-->
      <input id="username" class="login_input" type="text" onfocus="" onblur="logincheck()" name="username" placeholder="Username" required><br />
      <span id="lognamestatus"></span>
      <input id="password" class="login_input" type="password" onfocus="" name="password" placeholder="Password" required><br>
      <button id="login_button" class="welcome_font" onclick="login()" type="submit" value="login" name="submit">Log In</button>
    </form>
    <h4 id="login_forgot"><a href="forgot_pass.php">Forgot your username or password?</a></h4>
  </div>
  <div class="">
    <span id="loginstatus"></span>
  </div>
  <div id="login_signup">
    <h4 class="welcome_font">Don't have an Account?</h4>
    <form class="" action="index.php" method="post">
      <button id="signupbtn" class="welcome_font" type="submit" value="signup" name="submit">Sign Up</button>
    </form>
  </div>
</div>
