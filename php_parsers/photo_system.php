<?php
include_once '../php_includes/check_login_status.php';
?>
<?php
//TODO use this function below and figure out how to convert it for use with a feed
if (isset($_POST["show"]) && $_POST["show"] == "galpics") {
  $picstring = "";
  $gallery = preg_replace('#[^a-z0-9]#i', '', $_POST["gallery"]);
  $user = preg_replace('#[^a-z0-9]#i', '', $_POST["user"]);
  $sql = "SELECT * FROM photos WHERE user='$user' AND gallery='$gallery' ORDER BY uploaddate ASC";
  $query = mysqli_query($db_conx, $sql);
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $id = $row["id"];
    $filename = $row["filename"];
    $description = $row["description"];
    $uploaddate = $row["uploaddate"];
    $picstring .= "$id|$filename|$description|$uploaddate|||";
  }
  mysqli_close($db_conx);
  $picstring = trim($picstring, "|||");
  echo $picstring;
  exit();
}
?>
<?php
if ($user_ok != true || $log_username == "") {
  exit();
}
?>
<?php
//Below we see the use of tmp_name. This is not something we have to make in
//our database. This is part of a temporary folder that exists to hold our temporary
//php data. It also auto flushes itself.
if (isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"] != "") {
  $fileName = $_FILES["avatar"]["name"];
  $fileTmpLoc = $_FILES["avatar"]["tmp_name"];
  $fileType = $_FILES["avatar"]["type"];
  $fileSize = $_FILES["avatar"]["size"];
  $fileErrorMsg = $_FILES["avatar"]["error"];
  $exploded = explode(".", $fileName);
  $fileExt = end($exploded);
  list($width, $height) = getimagesize($fileTmpLoc);
  if ($width < 10 || $height < 10) {
    header("location: ../message.php?msg=ERROR: That image has no dimensions");
    exit();
  }
  //Below we set up a database file name for the imge. We use a randomizer to come up
  //with the name. This way we just have a string that refers to the name of the file
  //and then on the server in the folder system is where we actually store the media.
  $db_file_name = rand(100000000000, 999999999999).".".$fileExt;
  if ($fileSize > 1048576) {
    header("location: ../message.php?msg=ERROR: Your image file is larger than 1mb");
    exit();
  } else if (!preg_match("/\.(gif|jpg|png)$/i", $fileName)) {
    header("location: ../message.php?msg=ERROR: Your image file is not of type jpg, gif, or png");
    exit();
  } else if ($fileErrorMsg == 1) {
    header("location: ../message.php?msg=ERROR: An unknown error occured");
    exit();
  }
  $sql = "SELECT avatar FROM users WHERE username='$log_username' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);
  $avatar = $row[0];
  if ($avatar != "") {
    $picurl = "../user/$log_username/$avatar";
    if (file_exists($picurl)) { unlink($picurl); }
  }
  $moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
  if ($moveResult != true) {
    header("location: ../message.php?msg=ERROR: File upload failed");
    exit();
  }
  include_once '../php_includes/image_resize.php';
  $target_file = "../user/$log_username/$db_file_name";
  $resized_file = "../user/$log_username/$db_file_name";
  $wmax = 300;
  $hmax = 300;
  image_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
  $sql = "UPDATE users SET avatar='$db_file_name' WHERE username='$log_username' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  mysqli_close($db_conx);
  header("location: ../user.php?u=$log_username");
  exit();
}
?>
<?php
if (isset($_FILES["photo"]["name"])) {
  $sql = "SELECT COUNT(id) FROM photos WHERE user='$log_username'";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_row($query);

  //TODO get rid of this part below when I make the feed.
  if ($row[0] > 14) {
    header("location: ../message.php?msg=This user has uploaded too many photos");
    exit();
  }
  //$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["gallery"]);
  $gallery = $log_username;
  $fileName = $_FILES["photo"]["name"];
  $fileTmpLoc = $_FILES["photo"]["tmp_name"];
  $fileType = $_FILES["photo"]["type"];
  $fileSize = $_FILES["photo"]["size"];
  $fileErrorMsg = $_FILES["photo"]["error"];
  $exploded = explode(".", $fileName);
  $fileExt = end($exploded);
  //We use the line below to create a unique file name for each image.
  //That way even if two people uploaded at the exact same time there wouldnt be
  //a problem with the time stamp we give it.
  $db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt; //Will look something like WebFeb272120452013RAND.jpg
  list($width, $height) = getimagesize($fileTmpLoc);
  if ($width < 10 || $height < 10) {
    header("location: ../message.php?msg=ERROR: The image provided has no dimensions");
    exit();
  }
  if ($fileSize > 1048576) {
    header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
    exit();
  } else if (!preg_match("/\.(gif|jpg|png)$/i", $fileName)) {
    header("location: ../message.php?msg=ERROR: Your image file was not a file of type jpg, gif, or png");
    exit();
  } elseif ($fileErrorMsg == 1) {
    header("location: ../message.php?msg=ERROR: An unknown error occurred");
    exit();
  }
  if ($moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name")) {
    copy("../user/$log_username/$db_file_name", "../user/all/$db_file_name");
  }

  if ($moveResult != true) {
    header("location: ../message.php?msg=ERROR: File upload failed");
    exit();
  }
  include_once '../php_includes/image_resize.php';
  $wmax = 300;
  $hmax = 300;
  if ($width > $wmax || $height > $hmax) {
    $target_file = "../user/$log_username/$db_file_name";
    $resized_file = "../user/$log_username/$db_file_name";
    image_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
    $target_file = "../user/all/$db_file_name";
    $resized_file = "../user/all/$db_file_name";
    image_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
  }
  $sql = "INSERT INTO photos(user, gallery, filename, uploaddate) VALUES ('$log_username','$gallery','$db_file_name',now())";
  $query = mysqli_query($db_conx, $sql);
  $sql = "SELECT id FROM photos WHERE filename='$db_file_name'";
  $query = mysqli_query($db_conx, $sql);
  $row = mysqli_fetch_array($query);
  $realid = $row["id"];
  $testcomment = $_POST["comment"];
  if (strlen($testcomment) < 1) {
    $sql = "INSERT INTO status(account_name, author, type, data, postdate) VALUES('$log_username','$log_username','a',now(),now())";
    $query = mysqli_query($db_conx, $sql);
    $id = mysqli_insert_id($db_conx);
    mysqli_query($db_conx, "UPDATE status SET osid='$realid' WHERE id='$id' LIMIT 1");
  } else {
    $comment = htmlentities($_POST['comment']);
    $comment = mysqli_real_escape_string($db_conx, $comment);
    $anothertestcomment = "fuckin a homie";
    $sql = "INSERT INTO status(account_name, author, type, data, postdate) VALUES('$log_username','$log_username','a','$comment',now())";
    $query = mysqli_query($db_conx, $sql);
    $id = mysqli_insert_id($db_conx);
    mysqli_query($db_conx, "UPDATE status SET osid='$realid' WHERE id='$id' LIMIT 1");
  }
  $friends = array();
  $fquery = mysqli_query($db_conx, "SELECT user1 FROM friends WHERE user2='$log_username' AND accepted='1'");
  while ($frow = mysqli_fetch_array($fquery, MYSQLI_ASSOC)) {
    array_push($friends, $frow["user1"]);
  }
  $fquery = mysqli_query($db_conx, "SELECT user2 FROM friends WHERE user1='$log_username' AND accepted='1'");
  while ($frow = mysqli_fetch_array($fquery, MYSQLI_ASSOC)) {
    array_push($friends, $frow["user2"]);
  }
  for ($i=0; $i < count($friends); $i++) {
    $friend = $friends[$i];
    $app = "Post";
    $note = '<span class="username">'.$log_username.'</span> made a new post!<br /><a href="feed.php#post_'.$realid.'">Click here to view the post</a>';
    mysqli_query($db_conx, "INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$friend','$log_username','$app','$note',now())");
  }
  //header("location: ../photos.php?u=$log_username");
  header("location: ../feed.php");
  exit();
}
?>
<?php
if (isset($_POST["delete"]) && $_POST["id"] != "") {
  $id = preg_replace('#[^0-9]#', '', $_POST["id"]);
  $query = mysqli_query($db_conx, "SELECT user, filename FROM photos WHERE id='$id' LIMIT 1");
  $row = mysqli_fetch_row($query);
  $user = $row[0];
  $filename = $row[1];
  if ($user == $log_username) {
    $picurl = "../user/$log_username/$filename";
    if (file_exists($picurl)) {
      unlink($picurl);
      $sql = "DELETE FROM photos WHERE id='$id' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
    }
  }
  mysqli_close($db_conx);
  echo "deleted_ok";
  exit();
}
?>
