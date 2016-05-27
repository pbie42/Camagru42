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
//TODO Finish video at 20:58
?>
<?php?>
<?php?>
