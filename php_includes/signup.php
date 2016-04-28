<?php

 ?>

<div id="signup_section">
  <div class="main_area_signup">
    <h1 class="welcome_font">Welcome to</h1>
    <h1 id="login_logo" class="logo_font">Camagru</h1>
    <h3 id="signup_welcome" class="welcome_font">Please sign up to see photos<br/> from you and your friends</h3>
    <form name="signupform" id="signupform" onsubmit="return false;" action="index.php" method="post">
      <input id="username" class="login_input" type="text" onfocus="emptyElement('status')" onblur="checkusername()" onkeyup="restrict('username')" name="username" maxlength="15" placeholder="Username"><br />
      <span id="unamestatus"></span>
      <input id="email" class="login_input" type="text" onfocus="emptyElement('status')" name="email" placeholder="Email"><br>
      <input id="firstname" class="login_input" type="text" onfocus="emptyElement('status')" name="firstname" placeholder="First Name"><br>
      <input id="lastname" class="login_input" type="text" onfocus="emptyElement('status')" name="lastname" placeholder="Last Name"><br>
      <input id="pass1" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Password"><br>
      <input id="pass2" class="login_input" type="password" onfocus="emptyElement('status')" name="password" minlength="5" placeholder="Verify Password"><br>
      <?php include_once 'resources/countries.php'; ?>
      <button id="login_button" class="welcome_font" type="submit" name="submit" value="signup">Sign Up</button>
      <span id="status"></span>
    </form>
  </div>
  <div id="login_signup">
    <h4 class="welcome_font">Already have an Account?</h4>
    <form class="" action="index.php" method="post">
      <button id="login_button" class="welcome_font" type="submit" onclick="signup()" name="submit" value="login">Log In</button>
    </form>

  </div>
</div>
