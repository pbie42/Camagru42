<?php
include_once 'php_includes/check_login_status.php';
//Need to make sure the _GET "u" is set and then we need to sanitize it
$u = $log_username;
//if (isset($_GET["u"])) {
//  $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
//} else {
//  header("location: index.php");
//  exit();
//}
$photo_form = "";
//We check to see if the viewer is the account owner
$isOwner = "no";
if ($u == $log_username && $user_ok == true) {
  $isOwner = "yes";
  $photo_form = '<form id="photo_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
  $photo_form .= '<h3>Hi <span class="username">'.$u.'</span>! <br />Add a photo from your computer to your feed?</h3>';
  //$photo_form .= ' &nbsp; &nbsp; &nbsp; <b>Choose Photo:</b> ';
  //$photo_form .= '<input type="file" name="photo" accept="image/*" required />';
  $photo_form .= '<input id="choose_photo" class="inputfile" type="file" name="photo" data-multiple-caption="{count} files selected" multiple required/><label id="choose_photo_label" for="choose_photo"><span>Choose Photo</span></label>';
  $photo_form .= '<br /><br /><b>Add a comment</b>';
  $photo_form .= '<input id="comment_input" type="text" name="comment" /><br />';
  //$photo_form .= '<p><input type="submit" value="Upload Photo Now" /></p>';
  $photo_form .= '<p><input id="change_photo_btn" class="inputfile" type="submit" value="Upload"/><label id="choose_photo_label" for="change_photo_btn">Upload</label></p>';
  $photo_form .= '<p><input id="nevermind_photo_btn" class="inputfile" /><label id="choose_photo_label" for="nevermind_photo_btn" onclick="backToPhotoMenu()">Nevermind...</label></p>';
  $photo_form .= '</form>';
}
//Select the user galleries
$gallery_list = "";
$sql = "SELECT DISTINCT gallery FROM photos WHERE user='$u'";
$query = mysqli_query($db_conx, $sql);
if (mysqli_num_rows($query) < 1) {
  $gallery_list = "This user has not uploaded any photos yet.";
} else {
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $gallery = $row["gallery"];
    $countquery = mysqli_query($db_conx, "SELECT COUNT(id) FROM photos WHERE user='$u' AND gallery='$gallery'");
    $countrow = mysqli_fetch_row($countquery);
    $count = $countrow[0];
    $filequery = mysqli_query($db_conx, "SELECT filename FROM photos WHERE user='$u' AND gallery='$gallery' ORDER BY RAND() LIMIT 1");
    $filerow = mysqli_fetch_row($filequery);
    $file = $filerow[0];
    $gallery_list .= '<div>';
    $gallery_list .= '<div onclick="showGallery(\''.$gallery.'\',\''.$u.'\')">';
    $gallery_list .= '<img src="user/'.$u.'/'.$file.'" alt="cover photo" />';
    $gallery_list .= '</div>';
    $gallery_list .= '<b>'.$gallery.'</b> ('.$count.')';
    $gallery_list .= '</div>';
  }
}
?>
        <div id="photos_section">
          <div class="main_area_photo welcome_font">
            <div id="photo_form">
              <?php echo $photo_form; ?>
            </div><!--<div id="galleries">
            <h2 id="section_title"><?php echo $u; ?>&#39;s Photo Galleries</h2>

              <?php echo $gallery_list; ?>
            </div> -->
            <div id="photos">

            </div>
            <div id="picbox">

            </div>
          </div>
        </div>
    <script type="text/javascript">
    function showGallery(gallery,user) {
      _("galleries").style.display = "none";
      _("section_title").innerHTML = user+'&#39;s '+gallery+' Gallery &nbsp; <button onclick="backToGalleries()">Go back to all galleries</button>';
      _("photos").style.display = "block";
      _("photos").innerHTML = 'loading photos...';
      var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
      ajax.onreadystatechange = function () {
        if (ajaxReturn(ajax) == true) {
          _("photos").innerHTML = '';
          var pics = ajax.responseText.split("|||");
          for (var i = 0; i < pics.length; i++) {
            var pic = pics[i].split("|");
            _("photos").innerHTML += '<div><img onclick="photoShowcase(\''+pics[i]+'\')" src="user/'+user+'/'+pic[1]+'" alt="pic"></div>';
          }
          _("photos").innerHTML += '<p style="clear:left;"></p>';
        }
      }
      ajax.send("show=galpics&gallery="+gallery+"&user="+user);
    }
    function backToGalleries() {
      _("photos").style.display = "none";
      _("section_title").innerHTML = "<?php echo $u; ?>&#39;s Photo Galleries";
      _("galleries").style.display = "block";
    }
    function photoShowcase(picdata) {
      var data = picdata.split("|");
      _("section_title").style.display = "none";
      _("photos").style.display = "none";
      _("picbox").style.display = "block";
      _("picbox").innerHTML = '<button onclick="closePhoto()">x</button>';
      _("picbox").innerHTML += '<img src="user/<?php echo $u; ?>/'+data[1]+'" alt="photo">';
      if ("<?php echo $isOwner ?>" == "yes") {
        _("picbox").innerHTML += '<p id="deletelink"><a href="#" onclick="return false;" onmousedown="deletePhoto(\''+data[0]+'\')">Delete this photo <?php echo $u; ?></a></p>';
      }
    }
    function closePhoto() {
      _("picbox").innerHTML = '';
      _("picbox").style.display = "none";
      _("photos").style.display = "block";
      _("section_title").style.display = "block";
    }
    function deletePhoto(id) {
      var conf = confirm("Press OK to confirm the delete action on this photo.");
      if (conf != true) {
        return false;
      }
      _("deletelink").style.visibility = "hidden";
      var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
      ajax.onreadystatechange = function () {
        if (ajaxReturn(ajax) == true) {
          if (ajax.responseText == "deleted_ok") {
            alert("This picture has been deleted successfully. We will now refresh the page for you.");
            window.location = "photos.php?u=<?php echo $u; ?>";
          }
        }
      }
      ajax.send("delete=photo&id="+id);
    }
    function backToPhotoMenu() {
      _("snapdiv").style.display = "block";
      _("container_photo").style.display = "block";
      _("photos_section").style.display = "none";
    }
    var inputs = document.querySelectorAll( '.inputfile' );
    Array.prototype.forEach.call( inputs, function( input )
    {
    var label	 = input.nextElementSibling,
      labelVal = label.innerHTML;

    input.addEventListener( 'change', function( e )
    {
      var fileName = '';
      if( this.files && this.files.length > 1 )
        fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
      else
        fileName = e.target.value.split( '\\' ).pop();

      if( fileName ) {
        var len = fileName.length;
        var dots = '';
        if (len > 10) {
          dots = '...';
        }
        label.querySelector( 'span' ).innerHTML = fileName.substring(0, 10) + dots;
      } else
        label.innerHTML = labelVal;
    });
    });
    </script>
  </body>
</html>
