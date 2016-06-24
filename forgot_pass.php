<?php
include_once 'php_includes/check_login_status.php';
//If user is already logged in we header them away
if ($user_ok == true) {
  header("location: user.php?u=".$_SESSION["username"]);
  exit();
}
 ?>
<?php
 //Ajax calls this code to execute
 if (isset($_POST["e"])) {
   $e = mysqli_real_escape_string($db_conx, $_POST['e']);
   $sql = "SELECT id, username FROM users WHERE email='$e' AND activated='1' LIMIT 1";
   $query = mysqli_query($db_conx, $sql);
   $numrows = mysqli_num_rows($query);
   if ($numrows > 0) {
     while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
       $id = $row["id"];
       $u = $row["username"];
     }
     $emailcut = substr($e, 0, 4);
     $randNum = rand(10000, 99999);
     $tempPass = "$emailcut$randNum";
     $hashTempPass = hash('whirlpool', $temp_pass);
     $sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1";
     $query = mysqli_query($db_conx, $sql);
     $to = "$e";
     $from = "pbiecamagru42@gmail.com";
     $subject = "Camagru Temporary Password PLEASE DO NOT RESPOND";
     $headers = "From: $from\n";
     $headers .= "MIME-Version: 1.0\n";
     $headers .= "Content-type: text/html; charset=iso-8859-1\n";
     //TODO Will need to do something similar to what I will do for the email confirmation so I don't send a password.
     $msg = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Camagru Message</title><link href="https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa" rel="stylesheet" type="text/css"></head><body style="margin:0px; font-family:"Nunito", sans-serif;"><div style="padding:10px; background:rgb(117, 52, 52); font-size:24px; color:#CCC;"><span style="font-family:"Damion", cursive; font-size:30px;">Camagru </span>Temporary Password</div><div style="padding:24px; font-size:17px; font-family:"Nunito", sans-serif;">Hello '.$u.',<br /><br />This is an automated message from Camagru. If you did not recently initiate the Forgot Password process, please disregard this email.<br /><br />You have indicated that you forgot your login password and/or username. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.<br /><br />After you click the link below your password to login will be:<br /><br />'.$tempPass.'<br /><br /><a href="http://localhost:8080/camagru/forgot_pass.php?u='.$u.'&p='.$hashTempPass.'">Click here to apply the temporary password to your account.</a><br /><br />If you do not click the link in this email no changes will be made to your account. In order to set your login password to the temporary password you must click the link above. Please change your password as soon as you log back into the site!!!</div></body></html>';
     if (mail($to, $subject, $msg, $headers)) {
       echo "success";
       exit();
     } else {
       echo "email_send_failed";
       exit();
     }
   } else {
     echo "no_exist";
   }
   exit();
 }
?>
<?php
//Email link click calls this code to execute

if (isset($_GET['u']) && isset($_GET['p'])) {
  $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
  $temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
  if (strlen($temppasshash) < 5) {
    //This part here is in case someone tries to pass an empty variable as that
    //would fit most users in our database who have not needed to reset their passwords.
    //We also give no indication of why the error occured so as not to have
    //anyone think that they have found something out.
    exit();
  }
  $sql = "SELECT id FROM useroptions WHERE username='$u' AND temp_pass='$temppasshash' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $numrows = mysqli_num_rows($query);
  if ($numrows == 0) {
    header("location: message.php?msg=There is no match for that username with that temporary password in the system.");
  } else {
    $row = mysqli_fetch_row($query);
    $id = $row[0];
    $sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    header("location: index.php");
    exit();
  }
}
?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title></title>
     <script type="text/javascript" src="js/camagru.js"></script>
     <script type="text/javascript" src="js/ajax.js"></script>
     <script type="text/javascript" src="js/autoscroll.js"></script>
     <script type="text/javascript" src="js/forgot_pass.js"></script>
     <link rel="stylesheet" href="css/camagru.css" media="screen" title="no title" charset="utf-8">
     <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
   </head>
   <body>
     <div id="container">
       <?php include_once 'php_includes/header.php'; ?>
       <div id="body">
         <div id="login_section">
          <div class="main_area">

            <form id="forgotpass_form" onsubmit="return false;">
              <h1 id="login_logo" class="logo_font">Camagru</h1>
            <h1 id="passwordreset" class="welcome_font">Password Reset</h1>
            <h3 class="welcome_font">Please enter your email in order to<br/> generate a temporary password</h3>
              <!--TODO Need to clean this part up when the user hasn't activated their account-->
              <input id="email" class="login_input" type="text" onfocus="_('status').innerHTML='';" name="email" placeholder="Email" required><br />
              <button id="forgotpassbtn" class="welcome_font" onclick="forgotpass()">Generate Temporary Log In Password</button>

            </form>
            <h4 id="login_forgot"><a href="index.php">Return to Log In Page</a></h4>
            <p id="status"></p>
          </div>
       </div>
       <?php include_once 'php_includes/footer.php'; ?>
     </div>
   </body>
 </html>
