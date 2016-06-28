function trim1 (str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function logincheck() {
  var u = _("username").value;
  if (u != "") {
    //In this method below you can replace 'checking...' with a gif html code
    _("lognamestatus").innerHTML = 'checking...';
    var ajax = ajaxObj("POST", "login.php");
    ajax.onreadystatechange = function() {
      if (ajaxReturn(ajax) == true) {
        _("lognamestatus").innerHTML = ajax.responseText;
      }
    }
    ajax.send("logincheck="+u);
  }
}

function login() {
  var u = _("username").value;
  var p = _("password").value;
  if (u == "" || p == "") {
    _("loginstatus").innerHTML = "Please fill out all of the form fields";
  } else {
    _("login_button").style.display = "none";
    //Once again I could change out the please wait... for a gif
    _("loginstatus").innerHTML = 'please wait...';
    var ajax = ajaxObj("POST", "login.php");
    ajax.onreadystatechange = function () {
      console.log("not going in if statement");
      if (ajaxReturn(ajax) == true) {
        var response = ajax.responseText;
        var cleanresponse = trim1(response);
        console.log(cleanresponse);
        if (cleanresponse == "login_failed") {
          _("loginstatus").innerHTML = "Login unsuccessful, please try again.";
          _("login_button").style.display = "block";
        } else {
          console.log(cleanresponse);
          //I can direct the user to any page I want by adding any variables
          //I want below. So as to use the php to show certain things in my index page.
          window.location = "user.php?u="+ajax.responseText;
        }
      }
    }
    ajax.send("u="+u+"&p="+p);
  }
}
