<?php
  include_once 'config/setup.php';
  include_once 'php_includes/check_login_status.php';
  if ($user_ok == true || $log_username != "") {
    header("location: feed.php");
    exit();
  }
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Camagru</title>
     <script type="text/javascript" src="js/camagru.js"></script>
     <script type="text/javascript" src="js/ajax.js"></script>
     <script type="text/javascript" src="js/autoscroll.js"></script>
     <script type="text/javascript" src="js/signup.js"></script>
     <script type="text/javascript" src="js/login.js"></script>
     <script type="text/javascript" src="js/user.js"></script>
     <link rel="stylesheet" href="css/camagru.css" charset="utf-8" />
     <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
   </head>
   <body>
     <div id="container">
        <?php include_once("php_includes/header.php"); ?>
        <div id="body">
          <?php
            include_once 'login.php';


          ?>
        </div> <!-- Body -->


     </div> <!-- Body Container -->
     <script>

     </script>
   </body>
 </html>
