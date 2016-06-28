<?php
include_once '../php_includes/check_login_status.php';
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
  $query_avatar = $db_conx2->prepare("SELECT avatar FROM users WHERE username='$log_username' LIMIT 1");
  $query_avatar->execute();
  $row = $query_avatar->fetch(PDO::FETCH_NUM);
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
  $query_avatar_update = $db_conx2->prepare("UPDATE users SET avatar='$db_file_name' WHERE username='$log_username' LIMIT 1");
  $query_avatar_update->execute();
  $db_conx2 = null;
  header("location: ../user.php?u=$log_username");
  exit();
}
?>
<?php
if (isset($_POST['cam']) && $_POST['cam'] != "") {
  $gallery = $log_username;
  $cam = $_POST['cam'];
	if (isset($_POST['cam1']))
		$cam = implode('', array($cam, $_POST['cam1']));
	list($type, $cam) = explode(';', $cam);
	list(, $cam) = explode(',', $cam);
	$cam = imagecreatefromstring(base64_decode($cam));
	$fileExt = "png";
  $db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt;
	$photo_path = "../user/all/$db_file_name";
	imagepng($cam, $photo_path);
  $photo_path_user = "../user/$log_username/$db_file_name";
  imagepng($cam, $photo_path_user);
  include_once '../php_includes/image_resize.php';
  $target_file = "../user/$log_username/$db_file_name";
  $resized_file = "../user/$log_username/$db_file_name";
  $target_file_all = "../user/all/$db_file_name";
  $resized_file_all = "../user/all/$db_file_name";
  $wmax = 300;
  $hmax = 300;
  image_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
  image_resize($target_file_all, $resized_file_all, $wmax, $hmax, $fileExt);
  $query_photo_insert = $db_conx2->prepare("INSERT INTO photos(user, gallery, filename, uploaddate) VALUES ('$log_username','$gallery','$db_file_name',now())");
  $query_photo_insert->execute();
  $query_photo_id = $db_conx2->prepare("SELECT id FROM photos WHERE filename='$db_file_name'");
  $query_photo_id->execute();
  $row = $query_photo_id->fetch(PDO::FETCH_ASSOC);
  $realid = $row["id"];
  $testcomment = $_POST['comment_camagru'];
  if (strlen($testcomment) < 1) {
    $query_no_comment = $db_conx2->prepare("INSERT INTO status(account_name, author, type, data, postdate) VALUES('$log_username','$log_username','a',now(),now())");
    $query_no_comment->execute();
    $id = $db_conx2->lastInsertId();
    $query_status_id_no_comment = $db_conx2->prepare("UPDATE status SET osid='$realid' WHERE id='$id' LIMIT 1");
    $query_status_id_no_comment->execute();
  } else {
    $comment = htmlentities($_POST['comment_camagru']);
    $query_comment = $db_conx2->prepare("INSERT INTO status(account_name, author, type, data, postdate) VALUES('$log_username','$log_username','a','$comment',now())");
    $query_comment->execute();
    $id = $db_conx2->lastInsertId();
    $query_status_id = $db_conx2->prepare("UPDATE status SET osid='$realid' WHERE id='$id' LIMIT 1");
    $query_status_id->execute();
  }
  $friends = array();
  $fquery1 = $db_conx2->prepare("SELECT user1 FROM friends WHERE user2='$log_username' AND accepted='1'");
  $fquery1->execute();
  while ($frow = $fquery1->fetch(PDO::FETCH_ASSOC)) {
    array_push($friends, $frow["user1"]);
  }
  $fquery2 = $db_conx2->prepare("SELECT user2 FROM friends WHERE user1='$log_username' AND accepted='1'");
  $fquery2->execute();
  while ($frow = $fquery2->fetch(PDO::FETCH_ASSOC)) {
    array_push($friends, $frow["user2"]);
  }
  for ($i=0; $i < count($friends); $i++) {
    $friend = $friends[$i];
    $app = "Post";
    $note = '<span class="username">'.$log_username.'</span> made a new post!<br /><a href="feed.php#post_'.$realid.'">Click here to view the post</a>';
    $query_friend_notify = $db_conx2->prepare("INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$friend','$log_username','$app','$note',now())");
    $query_friend_notify->execute();
  }
  header("location: ../feed.php");
  exit();
}
?>
<?php
if (isset($_POST["deletepost"]) && $_POST["id"] != "") {
  $id = preg_replace('#[^0-9]#', '', $_POST["id"]);
  $query_user_file = $db_conx2->prepare("SELECT user, filename FROM photos WHERE id='$id' LIMIT 1");
  $query_user_file->execute();
  $row_user_file = $query_user_file->fetch(PDO::FETCH_NUM);
  $user = $row_user_file[0];
  $filename = $row_user_file[1];
  if ($user == $log_username) {
    $picurl = "../user/$log_username/$filename";
    $picurl2 = "../user/all/$filename";
    if (file_exists($picurl) || file_exists($picurl2)) {
      unlink($picurl);
      unlink($picurl2);
      $query_delete_photo = $db_conx2->prepare("DELETE FROM photos WHERE id='$id' LIMIT 1");
      $query_delete_photo->execute();
      $query_delete_status = $db_conx2->prepare("DELETE FROM status WHERE osid='$id'");
      $query_delete_status->execute();
      echo "post_deleted_ok";
    }
  }
  $db_conx2 = null;
  exit();
}
?>
