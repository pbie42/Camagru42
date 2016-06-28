function forgotpass() {
    var e = _("email").value;
    if (e == "") {
        _("status").innerHTML = "Please type in your email address";
    } else {
        _("forgotpassbtn").style.display = "none";
        var ajax = ajaxObj("POST", "forgot_pass.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) == true) {}
            var response = ajax.responseText;
            if (response == "success") {
                _("forgotpass_form").innerHTML = "<h2 class='welcome_font thankyoured'>Password Created!</h2><p class='welcome_font'>Please check your email <strong class='thankyoured'>inbox</strong> and <strong class='thankyoured'>junk mail</strong> box at <strong class='thankyoured'>" + e + "</strong> in a moment to receive your temporary password</p>";
            } else if (response == "no_exist") {
                _("forgotpass_form").innerHTML = "<h2 class='welcome_font thankyoured'>Sorry!</h2><p class='welcome_font'>The email <strong class='thankyoured'>" + e + "</strong> is not in our system</p>";
            } else if (response == "email_send_failed") {
                _("forgotpass_form").innerHTML = "<h2 class='welcome_font thankyoured'>Sorry!</h2><p class='welcome_font'>The email sent to <strong class='thankyoured'>" + e + "</strong> did not go through</p>";
            } else {
                _("forgotpass_form").innerHTML = "<h2 class='welcome_font thankyoured'>Sorry!</h2><p class='welcome_font'>An unknown error occured</p>";
            }
        }
    }
    ajax.send("e=" + e);
}
