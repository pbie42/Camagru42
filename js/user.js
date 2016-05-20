function friendToggle(type, user, elem) {
  var conf = confirm("Press Ok to confirm the '"+type+"' action for user ");
  if (conf != true) {
    return false;
  }
  _(elem).innerHTML = 'please wait...';
  var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
  ajax.onreadystatechange = function () {
    if (ajaxReturn(ajax) == true) {
      if (ajax.responseText == "friend_request_sent") {
        _(elem).innerHTML = 'Ok Friend Request Sent';
      } else if (ajax.responseText == "unfriend_ok") {
        _(elem).innerHTML = '<button class="request_button" onclick="friendToggle(\'friend\',\'<?php echo $u;?>\',\'friendBtn\')">Request As Friend</button>';
      } else {
        alert(ajax.responseText);
        _(elem).innerHTML = 'Try again later';
      }
    }
  }
  ajax.send("type="+type+"&user="+user);
}

function blockToggle(type, user, elem) {
  var conf = confirm("Press OK to confrim the '"+type+"' action on user <?php echo $u;?>");
  if (conf != true) {
    return false;
  }
  _(elem).innerHTML = 'please wait...';
  var ajax = ajaxObj("POST", "php_parsers/block_system.php");
  ajax.onreadystatechange = function () {
    if (ajaxReturn(ajax) == true) {
      if (ajax.responseText == "blocked_ok") {
        _(elem).innerHTML = '<button class="request_button" onclick="blockToggle(\'unblock\',\'<?php echo $u;?>\',\'blockBtn\')">Unblock User</button>';
      } else if (ajax.responseText == "unblocked_ok") {
        _(elem).innerHTML = '<button class="request_button" onclick="blockToggle(\'block\',\'<?php echo $u;?>\',\'blockBtn\')">Block User</button>';
      } else {
        alert(ajax.responseText);
        _(elem).innerHTML = 'Try again later';
      }
    }
  }
  ajax.send("type="+type+"&blockee="+blockee);
}
