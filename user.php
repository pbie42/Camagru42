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
  $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
  if (mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0) {
    $ownerBlockViewer = true;
  }
  //This part is to check if the viewer has blocked the owner of the profile
  //that they are viewing
  $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
  if (mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0) {
    $viewerBlockOwner = true;
  }
}
?>
<?php
//We are now going to evalute the results of the block checks
//The first thing we do is create two disabled buttons so anyone who is
//not a member of the site will see the buttons as disable as well as the
//owner of the page.
$friend_button = '<button class="request_button" disabled>Request As Friend</button>';
$block_button = '<button class="request_button" disabled>Block This User</button>';
//Logic for Friend Button
if ($isFriend == true) {
  $friend_button = '<button class="request_button" onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
} else if ($user_ok == true && $u != $log_username && $ownerBlockViewer == false) {
  $friend_button = '<button class="request_button" onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
}
//Logic for Block Button
if ($viewerBlockOwner == true) {
  $block_button = '<button class="request_button" onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
} elseif ($user_ok == true && $u != $log_username) {
  $block_button = '<button class="request_button" onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
}
?>
<?php
//TODO Need to finish this part of the video at 14:50
$friendsHTML = '';
$friends_view_all_link = '';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$user' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if ($friend_count < 1) {
  $friendsHTML = $u." has no friends yet";
} else {
  $max = 10;
  $all_friends = array();
  $sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    array_push($all_friends, $row["user1"]);
  }
  $sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    array_push($all_friends, $row["user2"]);
  }
  $friendArrayCount = count($all_friends);
  if ($friendArrayCount > $max) {
    array_splice($all_friends, $max);
  }
  if ($friend_count > $max) {
    $friends_view_all_link = '<a href="view_friends.php?u='.$u.'">View All Friends</a>';
  }
  //This part below is a loop to create an extended OR logic in mysqli syntax.
  //The foreach loop will separate the array all_friends by each individual and
  //append or concatenate the username to put into a mysqli query.
  $orLogic = '';
  foreach ($all_friends as $key => $user) {
    $orLogic .= "username='$user' OR ";
  }
  $sql = "SELECT username, avatar FROM users WHERE $orLogic";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $friend_username = $row["username"];
    $friend_avatar = $row["avatar"];
    if ($friend_avatar != "") {
      $friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
    } else {
      $friend_pic = 'resources/user.png';
    }
    $friendsHTML .= '<a href="user.php?='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"</a>';
  }
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
    <script type="text/javascript" src="js/user.js"></script>
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
            <p>Number of Friends: <?php echo $friend_count; ?></p>
            <hr />
            <p><span id="friendBtn" class="userspan"><?php echo $friend_button; ?></span></p>
            <p><span id="blockBtn" class="userspan"><?php echo $block_button; ?></span></p>
            <hr />
            <p><?php echo $friendsHTML; ?></p>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
  </body>
</html>
