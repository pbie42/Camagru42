<?php
include_once 'php_includes/check_login_status.php';
//Initialize any variables that the page might echo
$u = "";
$fname = "";
$lname = "";
$email = "";
$country = "";
$joindate = "";
$lastsession = "";
//Make sure the _GET username is set, and sanitize it
if (isset($_GET["u"])) {
  $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
  header("location: localhost:8080/42/camagru/index.php");
  exit();
}
//Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
//Make sure that the user exists in the table
$numrows = mysqli_num_rows($user_query);
if ($numrows < 1) {
  echo "That user does not exist or is not yet activated.";
  exit();
}
//Check to see if the viewer is the account owner
$isOwner = "no";
if ($u == $log_username && $user_ok == true) {
  $isOwner = "yes";
}
//Get the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
  $profile_id = $row["id"];
  $fname = $row["firstname"];
  $lname = $row["lastname"];
  $email = $row["email"];
  $country = $row["country"];
  $userlevel = $row["userlevel"];
  $signup = $row["signup"];
  $lastlogin = $row["lastlogin"];
  $joindate = strftime("%b %d, %Y", strtotime($signup));
  $lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
}

//TODO Will need to customize id tags for this page as well.
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="css/camagru.css" media="screen" title="no title" charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Oswald|Damion|Nunito|Comfortaa' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="js/camagru.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
  </head>
  <body>
    <div id="container">
      <?php include_once 'php_includes/header.php'; ?>
      <div id="body">
        <div id="message_section">
          <div class="main_area_user welcome_font">
            <h3><?php echo $u; ?></h3>
            <p>Is the viewer the page owner? <b><?php echo $isOwner; ?></b></p>
            <p>First Name: <?php echo $fname; ?></p>
            <p>Last Name: <?php echo $lname; ?></p>
            <p>Email: <?php echo $email; ?></p>
            <p>Country: <?php echo $country; ?></p>
            <p>User Level: <?php echo $userlevel; ?></p>
            <p>Join Date: <?php echo $joindate; ?></p>
            <p>Last Session: <?php echo $lastsession; ?></p>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
  </body>
</html>
