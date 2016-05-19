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
<?php
//The part below is to deal with blocking checks
$isFriend = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
if ($u != $log_username && $user_ok == true) {
  //This part below is to see if the person viewing the profile is logged in and to
  //see if they are friends already with that person
  $friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
  if (mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0) {
    $isFriend = true;
  }
  //This next part checks if the owner of the profile that the viewer is looking
  //at has blocked this viewer or not
  $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' AND accepted='1' LIMIT 1";
  if (mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0) {
    # code...
  }
  //This part is to check if the viewer has blocked the owner of the profile
  //that they are viewing
  $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
  if (mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0) {
    # code...
  }
}
?>
<?php
//We are now going to evalute the results of the block checks
//The first thing we do is create two disabled buttons so anyone who is
//not a member of the site will see the buttons as disable as well as the
//owner of the page.
$friend_button = '<button disabled>Request As Friend</button>';
$block_button = '<button disabled>Block This User</button>';
//Logic for Friend Button
if ($isFriend == true) {
  $friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
} else if ($user_ok == true && $u != $log_username && $ownerBlockViewer == false) {
  $friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
}
//Logic for Block Button
if ($viewerBlockOwner == true) {
  $block_button = '<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
} elseif ($user_ok == true && $u != $log_username) {
  $block_button = '<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
}
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
            <hr />
            <p>Friend Button: <span id="friendBtn"><?php echo $friend_button; ?></span></p>
            <p>Block Button: <span id="blockBtn"><?php echo $block_button; ?></span></p>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
  </body>
</html>
