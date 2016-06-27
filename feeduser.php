<?php
include_once 'php_includes/check_login_status.php';
?>

<?php
$feedstring = "";
$query = $db_conx2->prepare("SELECT * FROM photos WHERE user='$u' ORDER BY uploaddate DESC");
$query->execute();
$i = 0;
//$countquery = mysqli_query($db_conx, "SELECT COUNT(id) FROM photos");
//$countrow = mysqli_fetch_row($countquery);
//$count = $countrow[0];
//$rowquery = mysqli_query($db_conx, "SELECT * FROM photos");
//$rowcount = mysqli_num_rows($rowquery);
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
  $photoid = $row["id"];
  $username = $row["user"];
  $filename = $row["filename"];
  $uploaddate = $row["uploaddate"];
  $likes = $row["likes"];
  if ($likes == "") {
    $likes = 0;
  }
  if ($username == $log_username && $user_ok == true) {
    $delete_post = '<button class="delete_post_btn" onclick="deletePost2(\''.$photoid.'\',\''.$log_username.'\')">Delete This Post?</button>';
  }
  //The part below is to deal with blocking checks
  $isFriend = false;
  $ownerBlockViewer = false;
  $viewerBlockOwner = false;
  if ($username != $log_username && $user_ok == true) {
    //This part below is to see if the person viewing the profile is logged in and to
    //see if they are friends already with that person
    $friend_check = $db_conx2->prepare("SELECT id FROM friends WHERE user1='$log_username' AND user2='$username' AND accepted='1' OR user1='$username' AND user2='$log_username' AND accepted='1' LIMIT 1");
    $friend_check->execute();
    $friend_num_rows = $friend_check->fetchColumn();
    if ($friend_num_rows > 0) {
      $isFriend = true;
    }
    //This next part checks if the owner of the profile that the viewer is looking
    //at has blocked this viewer or not
    $block_check1 = $db_conx2->prepare("SELECT id FROM blockedusers WHERE blocker='$username' AND blockee='$log_username' LIMIT 1");
    $block_check1->execute();
    $block_check1_num_rows = $block_check1->fetchColumn();
    if ($block_check1_num_rows > 0) {
      $ownerBlockViewer = true;
    }
    //This part is to check if the viewer has blocked the owner of the profile
    //that they are viewing
    $block_check2 = $db_conx2->prepare("SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$username' LIMIT 1");
    $block_check2->execute();
    $block_check2_num_rows = $block_check2->fetchColumn();
    if ($block_check2_num_rows > 0) {
      $viewerBlockOwner = true;
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
  $convertedTime = ($timeAgoObject -> convert_datetime($uploaddate));
  $whenpost = ($timeAgoObject -> makeAgo($convertedNow, $convertedTime));
  $query1 = $db_conx2->prepare("SELECT * FROM status WHERE osid='$photoid' AND account_name='$username' AND type='a' LIMIT 1");
  $query1->execute();
  $query1bis = $db_conx2->prepare("SELECT * FROM status WHERE osid='$photoid' AND account_name='$username' AND type='a' LIMIT 1");
  $query1bis->execute();
  $statusnumrows = $query1bis->fetchColumn();
  while ($rowcomm = $query1->fetch(PDO::FETCH_ASSOC)) {
    ++$i;
    $statusid = $rowcomm["osid"];
    $statuslist = "";
    $account_name = $rowcomm["account_name"];
    $author = $rowcomm["author"];
    $postdate = $rowcomm["postdate"];
    $data = $rowcomm["data"];
    $data = nl2br($data);
    $data = str_replace("&amp;","&",$data);
  	$data = stripslashes($data);
    $status_replies = "";
    $query_replies = $db_conx2->prepare("SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
    $query_replies->execute();
    $replynumrows = $query_replies->fetchColumn();
      if($replynumrows > 0){
        $query_replies2 = $db_conx2->prepare("SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
        $query_replies2->execute();
        while ($row2 = $query_replies2->fetch(PDO::FETCH_ASSOC)) {
  				$statusreplyid = $row2["id"];
  				$replyauthor = $row2["author"];
  				$replydata = $row2["data"];
  				$replydata = nl2br($replydata);
  				$replypostdate = $row2["postdate"];
  				$replydata = str_replace("&amp;","&",$replydata);
  				$replydata = stripslashes($replydata);
  				$replyDeleteButton = '';
  				if($replyauthor == $log_username || $account_name == $log_username ){
  					$replyDeleteButton = '<span id="srdb_'.$statusreplyid.'" class="username replyDeleteButton"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">X</a></span>';
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
  				$status_replies .= '<div id="reply_'.$statusreplyid.'" class="reply_boxes"><div class="status_plus_delete"><div class="status_length"><b><a href="user.php?u='.$replyauthor.'"><span class="username">'.$replyauthor.'</span></a> '.$whenreply.': '.$replydata.'</b></div> '.$replyDeleteButton.'</div></div>';
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
  		$statuslist .= '<div id="status_'.$statusid.'" class="status_boxes"><div class="status_plus_delete"><div class="commentmade"><b><a class="username" href="user.php?u='.$author.'"><span class="username">'.$author.'</span></a>: '.$data.' </b> <br /></div>'.$statusDeleteButton.'</div>'.$status_replies.'</div>';
  		if($isFriend == true || $log_username == $username){
  	    $statuslist .= '<textarea id="replytext_'.$statusid.'" class="replytext textbox" onkeyup="statusMax(this,250)" onkeydown="enterReplyStatus(event,'.$statusid.',\''.$log_username.'\',\'replytext_'.$statusid.'\',this)" placeholder="Add a comment..."></textarea><button id="replyBtn_'.$statusid.'" class="replyBtn" onclick="replyToStatus('.$statusid.',\''.$username.'\',\'replytext_'.$statusid.'\',this)">Reply</button>';
  		}
  }
  $likesbutton = "";
  $likesquery = $db_conx2->prepare("SELECT * FROM likes WHERE osid='$photoid' AND liker='$log_username' LIMIT 1");
  $likesquery->execute();
  $likesquery_num_rows = $likesquery->fetchColumn();
  if ($likesquery && ($likesquery_num_rows > 0)) {
    $likesbutton = '<img id="like_button" class="likebutton" onclick="unlikeStatus(\''.$photoid.'\',\''.$log_username.'\',\''.$username.'\',\''.$likes.'\',\'unlike\')" src="resources/likefull.png" />';
  } else {
    $likesbutton = '<img id="like_button" class="likebutton" onclick="likeStatus(\''.$photoid.'\',\''.$log_username.'\',\''.$username.'\',\''.$likes.'\',\'like\')" src="resources/likeempty.png" />';
  }
  $feedstring .= '<div id="message_section">
    <div id="post_'.$photoid.'" onmouseover="showDelete(\''.$photoid.'\',\''.$log_username.'\',\''.$username.'\')" onmouseout="hideDelete(\''.$photoid.'\',\''.$log_username.'\',\''.$username.'\')" class="main_feed_area welcome_font">
      <div class="postheader">
        <div class="feed_username">
          <a href="user.php?u='.$author.'">
            <h4 class="username">'.$username.'</h4>
          </a>
        </div>
        <div class="feed_date">
          <h4>'.$whenpost.'</h4>
        </div>
      </div>
      <div class="post_photo">
        <img src="user/all/'.$filename.'" />
      </div>
      <div class="post_likes">
        <div id="like_button_div_'.$photoid.'">
          '.$likesbutton.'
        </div>
        <h4 id="like_number_'.$photoid.'" class="number_likes">'.$likes.' likes</h4>
        <div id="delete_post_'.$photoid.'" class="delete_post_div">
          '.$delete_post.'
        </div>
      </div>
      <div id="statusarea">
        '.$statuslist.'
      </div>
      <div id="statusui">
        '.$status_ui.'
      </div>
    </div>
  </div>';
}
?>
        <?php echo $feedstring; ?>

    <script type="text/javascript">
    function replyToStatus(sid,user,ta,btn){
    	var data = _(ta).value;
      console.log(data);
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
    				_("status_"+sid).innerHTML += '<div id="reply_'+rid+'" class="reply_boxes"><div class="status_plus_delete"><div class="status_length"><span class="username"><b>'+user+':</b></span> '+data+'</div><span id="srdb_'+rid+'" class="username replyDeleteButton"><a href="#" onclick="return false;" onmousedown="deleteReply(\''+rid+'\',\'reply_'+rid+'\');" title="DELETE THIS COMMENT">X</a></span></div></div>';
    				_("replyBtn_"+sid).disabled = false;
    				_(ta).value = "";
    			} else {
    				alert(ajax.responseText);
    			}
    		}
    	}
    	ajax.send("action=status_reply&sid="+sid+"&user="+user+"&data="+data);
    }
    //replyToStatus('.$statusid.',\''.$username.'\',\'replytext_'.$statusid.'\',this)
    function enterReplyStatus(e,sid,user,ta,btn) {
    	var keycode = e.keyCode;
    	if (keycode == 13) {

    		replyToStatus(sid, user, ta, btn);
    	}
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
    function showDelete(photoid,logusername,username) {
      if (logusername != username) {
        return ;
      }
      else {
        _("delete_post_"+photoid).style.display = "block";
      }
    }
    function hideDelete(photoid,logusername,username) {
      if (logusername != username) {
        return ;
      }
      else {
        _("delete_post_"+photoid).style.display = "none";
      }
    }
    function deletePost2(id,logusername) {
      var conf = confirm("Press OK to confirm the delete action on this post.");
      if (conf != true) {
        return false;
      }
      var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
      ajax.onreadystatechange = function () {
        if (ajaxReturn(ajax) == true) {
          if (ajax.responseText == "post_deleted_ok") {
            alert("This picture has been deleted successfully. We will now refresh the page for you.");
            window.location = "user.php?u="+logusername;
          } else {
            console.log(ajax.responseText);
          }
        }
      }
      ajax.send("deletepost=post&id="+id);
    }
    </script>
