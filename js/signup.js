//Might need to put all of these between <script> tags to include at the bottom of the
//index page if signup has been called.

function restrict(elem){
  var tf = _(elem);
  var rx = new RegExp;
  if (elem == "email") {
    rx = /[' "]/gi;
  } else if (elem == "username") {
    rx = /[^a-z0-9]/gi;
  }
  tf.value.replace(rx, "");
}

function emptyElement(x) {
  _(x).innerhtml = "";
}

function checkusername() {
  var u = _("username").value;
  if (u != "") {
    //In this method below you can replace 'checking...' with a gif html code
    _("unamestatus").innerHTML = 'checking...';
    var ajax = ajaxObj("POST", "signup.php");
    ajax.onreadystatechange = function() {
      if (ajaxReturn(ajax) == true) {
        _("unamestatus").innerHTML = ajax.responseText;
      }
    }
    ajax.send("usernamecheck="+u);
  }
}

function signup() {
  var u = _("username").value;
  var e = _("email").value;
  var p1 = _("pass1").value;
  var p2 = _("pass2").value;
  var c = _("country").value;
  var status = _("status");
  if (u == "" || e == "" || p1 == "" || p2 == "" || c == "") {
    status.innerHTML = "Fill out all of the form data";
  } else if (p1 != p2) {
    status.innerHTML = "Your password fields do not match";
  } else {
    _("signupbtn").style.display = "none";
    //Again in this method below we can replace 'Please wait...' with gif html code
    status.innerHTML = 'Please wait...';
    var ajax = ajaxObj("POST", "signup.php");
    ajax.onreadystatechange = function() {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText != "signup_success") {
          status.innerHTML = ajax.responseText;
          _("signupbtn").style.display = "block";
        } else {
          window.scrollTo(0, 0);
          _("signupform").innerHTML = "OK "+u+", check your email inbox and junk mail box at "+e+" in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you have successfully activated your account.";
        }
      }
    }
    ajax.send("u="+u+"&e="+e+"&p="+p1+"&c="+c);
  }
}

//Use the below function if you want to establish event listeners
//rather than write them inline.
function addEvents() {
  _("elemID").addEventListener("click", func, false);
}