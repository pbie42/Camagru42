<?php
include_once 'php_includes/check_login_status.php';
//Initialize any variables that the page might echo
$u = "";
$fname = "";
$lname = "";
$userlevel = "";
$email = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$country = "";
$joindate = "";
$lastsession = "";
//Make sure the _GET username is set, and sanitize it
if (isset($_GET["u"])) {
  $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
  header("location: index.php");
  exit();
}
if ($user_ok != true || $log_username == "") {
  header("location: index.php");
  exit();
}
//Select the member from the users table
$user_query = $db_conx2->prepare("SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1");
$user_query->execute();
//Make sure that the user exists in the table
$numrows = $user_query->fetchColumn();
if ($numrows < 1) {
  header("location: message.php?msg=That user does not exist or is not yet activated.");
  exit();
}
//Check to see if the viewer is the account owner
$isOwner = "no";
if ($u == $log_username && $user_ok == true) {
  $isOwner = "yes";
  $profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Change Profile Picture</a>';
  $avatar_form = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
  $avatar_form .= '<h4 id="change_avatar"> Change your Profile Picture</h4>';
  $avatar_form .= '<input id="choose_avatar" class="inputfile" type="file" name="avatar" data-multiple-caption="{count} files selected" multiple required/><label id="choose_avatar_label" for="choose_avatar"><span>Choose Photo</span></label>';
  $avatar_form .= '<p><input id="change_avatar_btn" class="inputfile" type="submit" value="Upload"/><label id="choose_avatar_label" for="change_avatar_btn">Upload</label></p>';
  $avatar_form .= '</form>';
  $reset_pass_form ='<form id="newpassform" class="" action="logout.php" method="post">
    <input id="newpass1" class="login_input" type="password" name="password" value="" minlength="5" placeholder="New Password">
    <input id="newpass2" class="login_input" type="password" name="password" value="" minlength="5" placeholder="Verify New Password">
    <button id="signupbtn" class="welcome_font" onclick="passreset()" type="submit" name="submit" value="signup">Reset Password</button><br>
  </form>';
}
//Get the user row from the query above
$user_query2 = $db_conx2->prepare("SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1");
$user_query2->execute();
while ($row = $user_query2->fetch(PDO::FETCH_ASSOC)) {
  $profile_id = $row["id"];
  $fname = $row["firstname"];
  $lname = $row["lastname"];
  $email = $row["email"];
  $avatar = $row["avatar"];
  $country = $row["country"];
  $userlevel = $row["userlevel"];
  $signup = $row["signup"];
  $lastlogin = $row["lastlogin"];
  $joindate = strftime("%b %d, %Y", strtotime($signup));
  $lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
}
$profile_pic = '<img id="profile_avatar" src="user/'.$u.'/'.$avatar.'" alt="'.$u.'" />';
if ($avatar == NULL) {
  $profile_pic = '<img class="avatar" src="resources/user.png" alt="'.$user1.'" />';
}
?>
<?php
//The part below is to deal with blocking checks
$isFriend = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
if ($u != $log_username && $user_ok == true) {
  //This part below is to see if the person viewing the profile is logged in and to
  //see if they are friends already with that person
  $friend_check = $db_conx2->prepare("SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1");
  $friend_check->execute();
  $friend_check_num_rows = $friend_check->fetchColumn();
  if ($friend_check_num_rows > 0) {
    $isFriend = true;
  }
  //This next part checks if the owner of the profile that the viewer is looking
  //at has blocked this viewer or not
  $block_check1 = $db_conx2->prepare("SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1");
  $block_check1->execute();
  $block_check1_num_rows = $block_check1->fetchColumn();
  if ($block_check1_num_rows > 0) {
    $ownerBlockViewer = true;
  }
  //This part is to check if the viewer has blocked the owner of the profile
  //that they are viewing
  $block_check2 = $db_conx2->prepare("SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1");
  $block_check2->execute();
  $block_check2_num_rows = $block_check2->fetchColumn();
  if ($block_check2_num_rows > 0) {
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
$friendsHTML = '';
$friends_view_all_link = '';
$query = $db_conx2->prepare("SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'");
$query->execute();
$query_count = $query->fetch(PDO::FETCH_NUM);
$friend_count = $query_count[0];
if ($friend_count < 1) {
  $friendsHTML = $u." has no friends yet";
} else {
  $max = 10;
  $all_friends = array();
  $query_all_friends = $db_conx2->prepare("SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max");
  $query_all_friends->execute();
  while ($row = $query_all_friends->fetch(PDO::FETCH_ASSOC)) {
    array_push($all_friends, $row["user1"]);
  }
  $query_all_friends2 = $db_conx2->prepare("SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max");
  $query_all_friends2->execute();
  while ($row = $query_all_friends2->fetch(PDO::FETCH_ASSOC)) {
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
  $orLogic = chop($orLogic, "OR ");
  $query_friend_info = $db_conx2->prepare("SELECT username, avatar FROM users WHERE $orLogic");
  $query_friend_info->execute();
  while ($row = $query_friend_info->fetch(PDO::FETCH_ASSOC)) {
    $friend_username = $row["username"];
    $friend_avatar = $row["avatar"];
    if ($friend_avatar != "") {
      $friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
    } else {
      $friend_pic = 'resources/user.png';
    }
    $friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"></a><p id="friend_name">'.$friend_username.'</p><br />';
  }
}
?>
<?php
$coverpic = "";
$query_coverpic = $db_conx2->prepare("SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1");
$query_coverpic->execute();
$coverpic_num_rows = $query_coverpic->fetchColumn();
if ($coverpic_num_rows > 0) {
  $query_coverpic2 = $db_conx2->prepare("SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1");
  $query_coverpic2->execute();
  $row = $query_coverpic2->fetch(PDO::FETCH_NUM);
  $filename = $row[0];
  $coverpic = '<img src="user/'.$u.'/'.$filename.'" alt="pic" />';
}
else {
  $coverpic = '<p>'.$u.' has not yet added any photos</p>';
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $u ?></title>
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
            <div id="profile_pic_box">
              <?php echo $avatar_form; ?><?php echo $profile_pic; ?><?php echo $profile_pic_btn; ?>
            </div>
            <h3 id="user_name"><?php echo $u; ?></h3>
            <p>First Name: <?php echo $fname; ?></p>
            <p>Last Name: <?php echo $lname; ?></p>
            <p>Email: <?php echo $email; ?></p>
            <p>Country: <?php echo $country; ?></p>
            <p>Join Date: <?php echo $joindate; ?></p>
            <p>Last Session: <?php echo $lastsession; ?></p>
            <p>Number of Friends: <?php echo $friend_count; ?></p>
            <?php echo $reset_pass_form; ?>
            <span id="statusnew"></span>
            <hr />
            <p><span id="friendBtn" class="userspan"><?php echo $friend_button; ?></p>
            <p><span id="blockBtn" class="userspan"><?php echo $block_button; ?></span></p>
            <hr />
            <h1 id="notificationtitle" class="welcome_font">Friends</h1>
            <?php echo $friendsHTML; ?>
            <hr>

          </div>
          <?php include_once 'feeduser.php'; ?>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
    <script type="text/javascript">
    function passreset() {
      var np1 = _("newpass1").value;
      var np2 = _("newpass2").value;
      var u = "<?php echo $log_username ?>";
      var statusnew = _("statusnew");
      if (np1 == "" || np2 == "") {
        statusnew.innerHTML = "Please fill out all fields";
        statusnew.style.display = "block";
      } else if (np1 != np2) {
        statusnew.innerHTML = "Your passwords do not match";
        statusnew.style.display = "block";
      } else {
        _("signupbtn").style.display = "none";
        //Again in this method below we can replace 'Please wait...' with gif html code
        statusnew.innerHTML = 'Please wait...';
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        ajax.onreadystatechange = function() {
          if (ajaxReturn(ajax) == true) {
            var response = ajax.responseText;
            var cleanresponse = trim1(response);
            if (cleanresponse != "pass_change_success") {
              statusnew.innerHTML = cleanresponse;
              _("signupbtn").style.display = "block";
            } else {
              alert("Password Change Successful! You will now be logged out. Please log back in with your new password.")
              _("signupbtn").style.display = "none";
              statusnew.innerHTML = "";
              _("newpassform").style.display = "none";
            }
          }
        }
        ajax.send("u="+u+"&np="+np1);
      }
    }
      var inputs = document.querySelectorAll( '.inputfile' );
      Array.prototype.forEach.call( inputs, function( input )
      {
      var label	 = input.nextElementSibling,
        labelVal = label.innerHTML;

      input.addEventListener( 'change', function( e )
      {
        var fileName = '';
        if( this.files && this.files.length > 1 )
          fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
        else
          fileName = e.target.value.split( '\\' ).pop();

        if( fileName ) {
          var len = fileName.length;
          var dots = '';
          if (len > 10) {
            dots = '...';
          }
          label.querySelector( 'span' ).innerHTML = fileName.substring(0, 10) + dots;
        } else
          label.innerHTML = labelVal;
      });
      });
      function likeStatus(photoid,liker,username,likes,action) {
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        var numlike = parseInt(likes) + 1;
      	ajax.onreadystatechange = function() {
      		if(ajaxReturn(ajax) == true) {
      			if(ajax.responseText == "like_ok"){
      				_("like_button_div_"+photoid).innerHTML = '<img id="like_button" class="likebutton" onclick="unlikeStatus(\''+photoid+'\',\''+liker+'\',\''+username+'\',\''+numlike+'\',\'unlike\')" src="resources/likefull.png" />';
              _("like_number_"+photoid).innerHTML = numlike+" likes";
      			} else {
      				alert(ajax.responseText);
      			}
      		}
      	}
      	ajax.send("photoid="+photoid+"&liker="+liker+"&username="+username+"&action="+action);
      }
      function unlikeStatus(photoid,liker,username,likes,action) {
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        var numlike = likes - 1;
        var div = "like_button_div_";
      	ajax.onreadystatechange = function() {
      		if(ajaxReturn(ajax) == true) {
      			if(ajax.responseText == "unlike_ok"){
              _("like_number_"+photoid).innerHTML = numlike+" likes";
              _("like_button_div_"+photoid).innerHTML = '<img id="like_button" class="likebutton" onclick="likeStatus(\''+photoid+'\',\''+liker+'\',\''+username+'\',\''+numlike+'\',\'like\')" src="resources/likeempty.png" />';
      			} else {
      				alert(ajax.responseText);
      			}
      		}
      	}
      	ajax.send("photoid="+photoid+"&liker="+liker+"&username="+username+"&action="+action);
      }
    </script>
  </body>
</html>
