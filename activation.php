<?php
if (isset($_GET['id']) && isset($_GET['u']) && isset($_GET['e']) && isset($_GET['p'])) {
	// Connect to database and sanitize incoming $_GET variables
    include_once("php_includes/db_conx.php");
    $id = preg_replace('#[^0-9]#i', '', $_GET['id']);
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
	$e = mysqli_real_escape_string($db_conx, $_GET['e']);
  //Below I changed the $p to take the spaces out of what has been received
  //The email activation was putting random spaces inside the hashed password
  //To use urldecode I had to make sure I used urlencode when sending the email
  $p = str_replace(' ', '', urldecode($_GET['p']));
	//$p = mysqli_real_escape_string($db_conx, $_GET['p']);
	// Evaluate the lengths of the incoming $_GET variable
	if($id == "" || strlen($u) < 3 || strlen($e) < 5 || strlen($p) < 5){
		// Log this issue into a text file and email details to yourself
		header("location: message.php?msg=activation_string_length_issues");
    	exit();
	}
	// Check their credentials against the database
	$query_credentials = $db_conx2->prepare("SELECT * FROM users WHERE id='$id' AND username='$u' AND email='$e' AND token='$p' LIMIT 1");
    $query_credentials->execute();
	$numrows_credentials = $query_credentials->fetchColumn();
	// Evaluate for a match in the system (0 = no match, 1 = match)
	if($numrows_credentials == 0){
		// Log this potential hack attempt to text file and email details to yourself
		header("location: message.php?msg=Your credentials are not matching anything in our system");
    	exit();
	}
	// Match was found, you can activate them
	$query_activation = $db_conx2->prepare("UPDATE users SET activated='1' WHERE id='$id' LIMIT 1");
    $query_activation->execute();
	// Optional double check to see if activated in fact now = 1
	$query_activation_check = $db_conx2->prepare("SELECT * FROM users WHERE id='$id' AND activated='1' LIMIT 1");
    $query_activation_check->execute();
	$numrows_activation_check = $query_activation_check->fetchColumn();
	// Evaluate the double check
    if($numrows_activation_check == 0){
		// Log this issue of no switch of activation field to 1
        header("location: message.php?msg=activation_failure");
    	exit();
    } else if($numrows_activation_check > 0) {
		// Great everything went fine with activation!
        header("location: message.php?msg=activation_success");
    	exit();
    }
    header("location: message.php?msg=something_else");
  exit();
} else {
	// Log this issue of missing initial $_GET variables
	header("location: message.php?msg=missing_GET_variables");
    exit();
}
?>
