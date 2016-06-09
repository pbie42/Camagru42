<?php
include_once 'php_includes/check_login_status.php';
?>
<?php
$feedstring = "";
$sql = "SELECT * FROM photos ORDER BY uploaddate ASC";
$query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
  $photoid = $row["id"];
  $username = $row["user"];
  $filename = $row["filename"];
  $uploaddate = $row["uploaddate"];
  include_once 'classes/time_ago.php';
  $timeAgoObject = new convertToAgo;
  $now = time();
  $dtNow = new DateTime("@$now");
  $phpnow = $dtNow->format('Y-m-d H:i:s');
  $NowStr = $phpnow;
  $NowZoneNameFrom = "UTC";
  $NowZoneNameTo = "Europe/Amsterdam";
  $NowZoneGood = date_create($NowStr, new DateTimeZone($NowZoneNameFrom))->setTimezone(new DateTimeZone($NowZoneNameTo))->format("Y-m-d H:i:s");
  $convertedNow = ($timeAgoObject -> convert_datetime($NowZoneGood));
  $convertedTime = ($timeAgoObject -> convert_datetime($uploaddate));
  $whenpost = ($timeAgoObject -> makeAgo($convertedNow, $convertedTime));
  $sql = "SELECT * FROM status WHERE osid='$photoid' AND account_name='$username' AND type='a' LIMIT 1";
  $query = mysqli_query($db_conx, $sql);
  $statusnumrows = mysqli_num_rows($query);
  while ($rowcomm = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $statusid = $rowcomm["id"];
    $account_name = $rowcomm["account_name"];
    $author = $rowcomm["author"];
    $postdate = $rowcomm["postdate"];
    $data = $rowcomm["data"];
    $data = nl2br($data);
    $data = str_replace("&amp;","&",$data);
  	$data = stripslashes($data);
    $status_replies = "";
  	$query_replies = mysqli_query($db_conx, "SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
  	$replynumrows = mysqli_num_rows($query_replies);
      if($replynumrows > 0){
        while ($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)) {
  				$statusreplyid = $row2["id"];
  				$replyauthor = $row2["author"];
  				$replydata = $row2["data"];
  				$replydata = nl2br($replydata);
  				$replypostdate = $row2["postdate"];
  				$replydata = str_replace("&amp;","&",$replydata);
  				$replydata = stripslashes($replydata);
  				$replyDeleteButton = '';
  				if($replyauthor == $log_username || $account_name == $log_username ){
  					$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'" class="username"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">X</a></span>';
  				}
  				include_once 'classes/time_ago.php';
  				$timeAgoObject = new convertToAgo;
  				$now = time();
  				$dtNow = new DateTime("@$now");
  				$phpnow = $dtNow->format('Y-m-d H:i:s');
  				$NowStr = $phpnow;
  				$NowZoneNameFrom = "UTC";
  				$NowZoneNameTo = "Europe/Amsterdam";
  				$NowZoneGood = date_create($NowStr, new DateTimeZone($NowZoneNameFrom))->setTimezone(new DateTimeZone($NowZoneNameTo))->format("Y-m-d H:i:s");
  				$convertedNow = ($timeAgoObject -> convert_datetime($NowZoneGood));
  				$convertedTime = ($timeAgoObject -> convert_datetime($replypostdate));
  				$whenreply = ($timeAgoObject -> makeAgo($convertedNow, $convertedTime));
  				$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes"><div><b><a href="user.php?u='.$replyauthor.'"><span class="username">'.$replyauthor.'</span></a> '.$whenreply.':</b> '.$replyDeleteButton.'<br />'.$replydata.'</div></div>';
  	    }
      }
      include_once 'classes/time_ago.php';
  		$timeAgoObject = new convertToAgo;
  		$now = time();
  		$dtNow = new DateTime("@$now");
  		$phpnow = $dtNow->format('Y-m-d H:i:s');
  		$NowStr = $phpnow;
  		$NowZoneNameFrom = "UTC";
  		$NowZoneNameTo = "Europe/Amsterdam";
  		$NowZoneGood = date_create($NowStr, new DateTimeZone($NowZoneNameFrom))->setTimezone(new DateTimeZone($NowZoneNameTo))->format("Y-m-d H:i:s");
  		$convertedNow = ($timeAgoObject -> convert_datetime($NowZoneGood));
  		$convertedTime = ($timeAgoObject -> convert_datetime($postdate));
  		$when = ($timeAgoObject -> makeAgo($convertedNow, $convertedTime));
  		$statuslist .= '<div id="status_'.$statusid.'" class="status_boxes"><div class="status_plus_delete"><div class="commentmade"><b><a class="username" href="user.php?u='.$author.'"><span class="username">'.$author.'</span></a> '.$when.':</b> '.$data.' <br /></div>'.$statusDeleteButton.'</div>'.$status_replies.'</div>';
  		if($isFriend == true || $log_username == $username){
  	    $statuslist .= '<textarea id="replytext_'.$statusid.'" class="replytext textbox" onkeyup="statusMax(this,250)" placeholder="Add a reply?"></textarea><button id="replyBtn_'.$statusid.'" class="replyBtn" onclick="replyToStatus('.$statusid.',\''.$username.'\',\'replytext_'.$statusid.'\',this)">Reply</button>';
  		}
  }
  $feedstring .= '<div id="message_section"><div class="main_feed_area welcome_font"><div class=""><img src="user/all/'.$filename.'" /></div></div></div>';
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
          <div id="post_<?php echo $photoid ?>" class="main_feed_area welcome_font">
            <div class="postheader">
              <div class="feed_username">
                <a href="#">
                  <h4 class="username"><?php echo $username; ?></h4>
                </a>
              </div>
              <div class="feed_date">
                <h4><?php echo $whenpost ?></h4>
              </div>
            </div>
            <div class="post_photo">
              <img src="user/all/ThuJun917285120161479.png" />
            </div>
            <div id="statusarea">
              <?php echo $statuslist; ?>
            </div>
            <div id="statusui">
              <?php echo $status_ui; ?>
            </div>
          </div>
        </div>
      </div>
      <?php include_once 'php_includes/footer.php'; ?>
    </div>
    <script type="text/javascript">
    function replyToStatus(sid,user,ta,btn){
    	var data = _(ta).value;
    	if(data == ""){
    		alert("Please type a reply");
    		return false;
    	}
    	_("replyBtn_"+sid).disabled = true;
    	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
    	ajax.onreadystatechange = function() {
    		if(ajaxReturn(ajax) == true) {
    			var datArray = ajax.responseText.split("|");
    			if(datArray[0] == "reply_ok"){
    				var rid = datArray[1];
    				data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
    				_("status_"+sid).innerHTML += '<div id="reply_'+rid+'" class="reply_boxes"><div><b>Reply by you just now:</b><span id="srdb_'+rid+'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''+rid+'\',\'reply_'+rid+'\');" title="DELETE THIS COMMENT">X</a></span>'+data+'</div></div>';
    				_("replyBtn_"+sid).disabled = false;
    				_(ta).value = "";
    			} else {
    				alert(ajax.responseText);
    			}
    		}
    	}
    	ajax.send("action=status_reply&sid="+sid+"&user="+user+"&data="+data);
    }
    function deleteReply(replyid,replybox){
    	var conf = confirm("Press OK to confirm deletion of this reply");
    	if(conf != true){
    		return false;
    	}
    	var ajax = ajaxObj("POST", "php_parsers/status_system.php");
    	ajax.onreadystatechange = function() {
    		if(ajaxReturn(ajax) == true) {
    			if(ajax.responseText == "delete_ok"){
    				_(replybox).style.display = 'none';
    			} else {
    				alert(ajax.responseText);
    			}
    		}
    	}
    	ajax.send("action=delete_reply&replyid="+replyid);
    }
    function statusMax(field, maxlimit) {
    	if (field.value.length > maxlimit){
    		alert(maxlimit+" maximum character limit reached");
    		field.value = field.value.substring(0, maxlimit);
    	}
    }
    </script>
  </body>
</html>
