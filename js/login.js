function login() {
  var e = _("email").value;
  var p = _("password").value;
  if (e == "" || p == "") {
    _("status").innerHTML = "Please fill out all of the form fields";
  } else {
    _("login_button").style.display = "none";
    //Once again I could change out the please wait... for a gif
    _("status").innerHTML = 'please wait...';
    var ajax = ajaxObj("POST", "login.php");
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == "login_failed") {
          _("status").innerHTML = "Login unsuccessful, please try again.";
          _("login_button").style.display = "block";
        } else {
          //I can direct the user to any page I want by adding any variables
          //I want below. So as to use the php to show certain things in my index page.
          window.location = "user.php?u="+ajax.responseText;
        }
      }
    }
    ajax.send("e="+e+"&p="+p);
  }
}
