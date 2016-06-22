<!-- TODO Make it so comment id's are linked to photos uploaded rather than the conversations having unique id's themselves. -->
<div id="feed_section">
  <div class="main_feed_area">
    <div class="postheader">
      <div class="feed_username">
        <a href="#">
          <h4 class="username">username</h4>
        </a>
      </div>
      <div class="feed_date">
        <h4>1 day ago</h4>
      </div>
    </div>

    <div id="photo" class="photo">
      <img src="resources/apero.JPG" alt="" />
    </div> <!-- Photo -->
    <div class="feed_comments">
      <div class="feed_likes">
        <h4 class="likes">10 likes</h4>
      </div> <!-- Likes -->
      <ul class="comments">
        <li class="comment">
          <a href="#">
            <h4 id="usernameh"><span class="username"><strong>username1</strong></span><span class="username_comment">Super dope photo bruh
            I can't believe you were there when this happened that's fucking crazy as shit bro like god damn my god!</span></h4>
          </a>
        </li>
      </ul> <!-- Comments -->
      <div class="add_comments">
        <a href="#" role="button"><img class="heart" src="resources/emptyheart.png" alt="" /></a>
        <form class="comment_form" action="index.html" method="post">
          <input class="add_comment" type="text" name="name" value="" placeholder="Add a comment...">
        </form>
      </div> <!-- Add Comments -->
    </div> <!-- Likes/Comments Area -->
  </div> <!-- Post -->
</div> <!-- Post Area -->
