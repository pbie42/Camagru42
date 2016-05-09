<?php
session_start();
//If user is logged in, header them away
if (isset($_SESSION["username"])) {
  exit();
}
?>
<?php
//Ajax calls this NAME CHECK code to execute
if (isset($_POST["usernamecheck"])) {
  include_once 'php_includes/db_conx.php';
  $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
  $sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $uname_check = mysqli_num_rows($query);
  if (strlen($username) < 5) {
    echo '<strong style="color:#F00;">Usernames must be at least 5 characters</strong>';
    exit();
  }
  if (is_numeric($username[0])) {
    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
    exit();
  }
  if ($uname_check < 1) {
    echo '<strong style="color:#009900;">√</strong>';
    exit();
  } else {
    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
    exit();
  }
}
?>
<?php
 //Ajax calls this REGISTRATION code to execute
 if(isset($_POST["u"])) {
   //Connect to the database
   include_once 'php_includes/db_conx.php';
   //Gather the posted data into local variables
   $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
   $e = mysqli_real_escape_string($db_conx, $_POST['e']);
   $p = $_POST['p'];
   $c = preg_replace('#[^a-z]#i', '', $_POST['c']);
   $f = preg_replace('#[^a-z]#i', '', $_POST['f']);
   $l = preg_replace('#[^a-z]#i', '', $_POST['l']);
   //Get user IP address
   $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
   //Duplicate data checks for username and email
   $sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
   $query = mysqli_query($db_conx, $sql);
   $u_check = mysqli_num_rows($query);
   //-------------------------------------------------
   $sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
   $query = mysqli_query($db_conx, $sql);
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
      //TODO Change this to something more secure!!!!!!!!!!!
      //TODO need to change the activation process. Need to make sure that I don't
      //send the password even if it is ecrypted. Should do a randStrGen activation code
      //that will be used only for the activation process.
      $p_hash = md5($p);
      //Add user info into the database table for the main site table
      //!!!!!!!IMPORTANT!!!!! in the values section never put spaces!!!!!!
      $sql = "INSERT INTO users (username, firstname, lastname, email, password, country, ip, signup, lastlogin, notescheck)
		        VALUES('$u','$f','$l','$e','$p_hash','$c','$ip',now(),now(),now())";
      $query = mysqli_query($db_conx, $sql);
      if (false===$query) {
        echo mysqli_error($db_conx);
      }
      $uid = mysqli_insert_id($db_conx);
      //Establish their row in the useroptions table
      $sql = "INSERT INTO useroptions (id, username) VALUES ('$uid', '$u')";
      $query = mysqli_query($db_conx, $sql);
      //Create directory(folder) to hold each user's files(pics)
      if (!file_exists("user/$u")) {
        mkdir("user/$u", 0755);
      }
      //Email the user their activation link
      //NOTE that I needed to use urlencode when sending the hashed password
      //to ensure we could decode it and get rid of the extra spaces that are
      //sometimes sent
      $to = "$e";
      $from = "pbiecamagru42@gmail.com";
      $subject = "Camagru Account Activation PLEASE DO NOT RESPOND";
      $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Camagru Message</title><link href="https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa" rel="stylesheet" type="text/css"></head><body style="margin:0px; font-family:"Nunito", sans-serif;"><div style="padding:10px; background:rgb(117, 52, 52); font-size:24px; color:#CCC;"><span style="font-family:"Damion", cursive; font-size:30px;">Camagru </span><a href="http://localhost:8080/42/camagru/index.php"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Camera_retro_font_awesome.svg/2000px-Camera_retro_font_awesome.svg.png" width="36" height="36" alt="yoursitename" style="border:none; float:left; color:#CCC"></a>Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://localhost:8080/42/camagru/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.urlencode($p_hash).'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
      $headers = "From: $from\n";
      $headers .= "MIME-Version: 1.0\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1\n";
      mail($to, $subject, $message, $headers);
      echo "signup_success";
      exit();
   }
   exit();
 }
?>

<div id="signup_section">
  <div id="signupform" class="main_area_signup">
    <h1 class="welcome_font">Welcome to</h1>
    <h1 id="login_logo" class="logo_font">Camagru</h1>
    <h3 id="signup_welcome" class="welcome_font">Please sign up to see photos<br/> from you and your friends</h3>
    <form name="signupform" id="signupform" onsubmit="return false;" action="index.php" method="post">
      <input id="username" class="login_input" type="text" onfocus="emptyElement('status')" onblur="checkusername()" onkeyup="restrict('username')" name="username" maxlength="15" placeholder="Username">
      <span id="unamestatus"></span>
      <br />
      <input id="email" class="login_input" type="text" onfocus="emptyElement('status')" name="email" placeholder="Email"><br>
      <input id="firstname" class="login_input" type="text" onfocus="emptyElement('status')" name="firstname" placeholder="First Name"><br>
      <input id="lastname" class="login_input" type="text" onfocus="emptyElement('status')" name="lastname" placeholder="Last Name"><br>
      <input id="pass1" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Password"><br>
      <input id="pass2" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Verify Password"><br>
      <?php include_once 'resources/countries.php'; ?>
      <button id="signupbtn" class="welcome_font" onclick="signup()" type="submit" name="submit" value="signup">Sign Up</button>
      <span id="status"></span>
    </form>
  </div>
  <div id="login_signup">
    <h4 class="welcome_font">Already have an Account?</h4>
    <form class="" action="index.php" method="post">
      <button id="login_button" class="welcome_font" type="submit"  name="submit" value="login">Log In</button>
    </form>
  </div>
</div>
