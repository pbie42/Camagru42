<?php
$message = "No Message";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if ($msg == "activation_failure") {
  $message = '<h2 class="welcome_font">Activation Error</h2> Sorry there seems to have been a problem with your activation. We have notified ourselves of this issue and we will contact you via email when we have solved this problem';
} elseif ($msg == "activation_success") {
  $message = '<h2 class="welcome_font thankyoured">Activation Successful</h2><p class="welcome_font">Your account is now activated. </p><a id="loginbtn" class="welcome_font" href="index.php?submit=login">Click here to login!</a>';
} else {
  $message = $msg;
}
//TODO Need to customize the id tags for this page
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="css/camagru.css" media="screen" title="no title" charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div id="container">
      <?php include_once 'php_includes/header.php'; ?>
      <div id="body">
        <div id="message_section">
          <div class="main_area_message">
            <?php echo $message; ?>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
  </body>
</html>
