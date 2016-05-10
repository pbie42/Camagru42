<?php
session_start();
  include_once 'config/setup.php';
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
     <link rel="stylesheet" href="css/camagru.css" charset="utf-8" />
     <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
   </head>
   <body>
     <div id="container">
        <?php include_once("php_includes/header.php"); ?>
        <div id="body">
          <?php
            if ($_SESSION['username'] == "" && isset($_POST['submit']) && $_POST['submit'] == signup)
            {
              include_once 'signup.php';
              if ($_POST['username'] == "" && isset($_POST['username']))
                include_once 'logsignerror.php';
            }
            else if ($_SESSION['username'] == "")
            {
              include_once 'login.php';
              //include_once 'php_includes/video.php';
              if ($_POST['username'] == "" && isset($_POST['username']))
                include_once 'php_includes/logsignerror.php';
            }
            else if ($_SESSION['username'] != "") {
              echo "fuck it dog life's a risk";
              include_once 'php_includes/video.php';
              include_once 'php_includes/feed.php';
            }
          ?>
        </div> <!-- Body -->
        <div class="clearfooter"></div> <!-- Clearfooter -->
        <?php
            if ($_SESSION['logged_on_user'] == "")
            {
              include_once ('php_includes/footer.php');
            }
         ?>
     </div> <!-- Body Container -->
     <script>

     </script>
   </body>
 </html>
