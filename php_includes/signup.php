<?php
session_start();
//If user is logged in, header them away
if (isset($_SESSION["username"])) {

}
?>
<?php
//Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])) {
  include_once 'config/database.php';
  $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
  $sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
  $query = mysqli_query($db, $sql);
  $uname_check = mysqli_num_rows($query);
  if (strlen($username) < 3 || strlen($username) > 16) {
    ;
    exit();
  }
  if (is_numeric($username[0])) {
    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
    exit();
  }
  if ($uname_check < 1) {
    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
    exit();
  } else {
    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>'
    exit();
  }
}
?>
<?php
 //Ajax calls this REGISTRATION code to execute
 if(isset($_POST["u"])) {
   //Connect to the database
   include_once 'config/database.php';
   //Gather the posted data into local variables
   $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
   $e = mysqli_real_escape_string($db, $_POST['e']);
   $p = $_POST['p'];
   $c = preg_replace('#[^a-z]#i', '', $_POST['c']);
   //Get user IP address
   $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
   //Duplicate data checks for username and email
   $sql = "SELECT id FROM users WHERE username='$u' LIMIT 1"
   $query = mysqli_query($db, $sql);
   $u_check = mysqli_num_rows($query);
   //-------------------------------------------------
   $sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
   $query = mysqli_query($db, $sql);
   $e_check = mysqli_num_rows($query);
   //Form data error handling
   if ($u == "" || $e == "" || $p == "" || $c == "") {
     echo "The form has not been completed";
     exit();
   } else if ($u_check > 0) {
     echo "The username you entered is already taken";
     exit();
   } else if ($e_check > 0) {
     echo "The email address you entered is already in use";
     exit();
   } else if (strlen($u) < 3 || strlen($u) > 16) {
     echo "Username must be between 3 and 16 characters";
     exit();
   } elseif (is_numeric($u[0])) {
     echo "Username must begin with a letter";
     exit();
   } else {
     //End form data error handling
        //Begin insertion of data into the database
        //Has the password and apply salt
      //Change this to something more secure!!!!!!!!!!!
      $cryptpass = crypt($p);
      include_once 'php_includes/randStrGen.php';
      $p_hash = randStrGen(20)."$cryptpass".randStrGen(20);
      //Add user info into the deeatabase table for the main site table
      
   }

 }
?>

<div id="signup_section">
  <div id="signupform" class="main_area_signup">
    <h1 class="welcome_font">Welcome to</h1>
    <h1 id="login_logo" class="logo_font">Camagru</h1>
    <h3 id="signup_welcome" class="welcome_font">Please sign up to see photos<br/> from you and your friends</h3>
    <form name="signupform" id="signupform" onsubmit="return false;" action="index.php" method="post">
      <input id="username" class="login_input" type="text" onfocus="emptyElement('status')" onblur="checkusername()" onkeyup="restrict('username')" name="username" maxlength="15" placeholder="Username"><br />
      <span id="unamestatus"></span>
      <input id="email" class="login_input" type="text" onfocus="emptyElement('status')" name="email" placeholder="Email"><br>
      <input id="firstname" class="login_input" type="text" onfocus="emptyElement('status')" name="firstname" placeholder="First Name"><br>
      <input id="lastname" class="login_input" type="text" onfocus="emptyElement('status')" name="lastname" placeholder="Last Name"><br>
      <input id="pass1" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Password"><br>
      <input id="pass2" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Verify Password"><br>
      <?php include_once 'resources/countries.php'; ?>
      <button id="signupbtn" class="welcome_font" type="submit" name="submit" value="signup">Sign Up</button>
      <span id="status"></span>
    </form>
  </div>
  <div id="login_signup">
    <h4 class="welcome_font">Already have an Account?</h4>
    <form class="" action="index.php" method="post">
      <button id="login_button" class="welcome_font" type="submit" onclick="signup()" name="submit" value="login">Log In</button>
    </form>

  </div>
</div>
