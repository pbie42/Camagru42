<?php
$message = "No Message";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if ($msg == "activation_failure") {
  $message = '<h2>Activation Error</h2> Sorry there seems to have been a problem with your activation. We have notified ourselves of this issue and we will contact you via email when we have solved this problem';
} elseif ($msg == "activation_success") {
  $message = '<h2>Activation Successful</h2> Your account is now activated. <a href="index.php?submit=login"></a>';
} else {
  $message = $msg;
}
?>
<div>
  <?php echo $message; ?>
</div>
