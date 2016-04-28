<?php
  session_start();
  include_once 'connect.php';
 ?>

<div id="login_section">
  <div class="main_area">
    <h1 class="welcome_font">Welcome to</h1>
    <h1 id="login_logo" class="logo_font">Camagru</h1>
    <h3 class="welcome_font">Please log in to see photos<br/> from you and your friends</h3>
    <form id="login_form" action="index.php" method="post">
      <input class="login_input" type="text" name="username" placeholder="Username"><br />
      <input class="login_input" type="text" name="passwd" placeholder="Password"><br>
      <button id="login_button" class="welcome_font" type="submit" value="login" name="submit">Log In</button>
    </form>
    <h4 id="login_forgot">Forgot your username or password?</h4>
  </div>
  <div id="login_signup">
    <h4 class="welcome_font">Don't have an Account?</h4>
    <form class="" action="index.php" method="post">
      <button id="login_button" class="welcome_font" type="submit" value="signup" name="submit">Sign Up</button>
    </form>

  </div>
</div>
