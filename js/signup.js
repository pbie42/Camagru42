//Might need to put all of these between <script> tags to include at the bottom of the
//index page if signup has been called.

function trim1 (str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function restrict(elem){
  var tf = _(elem);
  var rx = new RegExp;
  if (elem == "email") {
    rx = /[' "]/gi;
  } else if (elem == "username") {
    rx = /[^a-z0-9]/gi;
  }
  tf.value = tf.value.replace(rx, "");
}

function emptyElement(x) {
  _(x).innerHTML = "";
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

function checkemail() {
  var e = _("email").value;
  if (e != "") {
    _("emailstatus").innerHTML = 'checking...';
    var ajax = ajaxObj("POST", "signup.php");
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        _("emailstatus").innerHTML = ajax.responseText;
      }
    }
    ajax.send("emailcheck="+e);
  }
}

function signup() {
  var u = _("username").value;
  var e = _("email").value;
  var f = _("firstname").value;
  var l = _("lastname").value;
  var p1 = _("pass1").value;
  var p2 = _("pass2").value;
  var c = _("country").value;
  var status = _("status");
  if (u == "" || e == "" || p1 == "" || p2 == "" || c == "") {
    status.innerHTML = "Please fill out all fields";
    status.style.display = "block";
  } else if (p1 != p2) {
    status.innerHTML = "Your passwords do not match";
    status.style.display = "block";
  } else {
    _("signupbtn").style.display = "none";
    //Again in this method below we can replace 'Please wait...' with gif html code
    status.innerHTML = 'Please wait...';
    var ajax = ajaxObj("POST", "signup.php");
    console.log("getting here");
    ajax.onreadystatechange = function() {
      if (ajaxReturn(ajax) == true) {
        var response = ajax.responseText;
        var cleanresponse = trim1(response);
        if (cleanresponse != "signup_success") {
          status.innerHTML = cleanresponse;
          console.log(cleanresponse);
          _("signupbtn").style.display = "block";
        } else {
          window.scrollTo(0, 0);
          _("signupform").innerHTML = "<h2 class='welcome_font thankyoured'>Thanks "+u+" !</h2><p class='welcome_font'>Please check your email <strong class='thankyoured'>inbox</strong> and <strong class='thankyoured'>junk mail</strong> box at <strong class='thankyoured'>"+e+"</strong> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you have successfully activated your account.</p>";
          _("signupform").style.height = "250px";
          _("login_signup").style.display = "none";
          status.style.display = "none";
          console.log(cleanresponse);
        }
      }
    }
    console.log(u);
    console.log(e);
    console.log(p1);
    console.log(c);
    console.log(f);
    console.log(l);
    ajax.send("u="+u+"&e="+e+"&p="+p1+"&c="+c+"&f="+f+"&l="+l);
  }
}

//Use the below function if you want to establish event listeners
//rather than write them inline.
function addEvents() {
  _("elemID").addEventListener("click", func, false);
}
