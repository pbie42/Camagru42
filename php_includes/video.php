<div id="lightBoxBg" onclick="dismiss()">

</div>
<div id="addphotodiv" onclick="addPhoto()">
  <button id="addphoto" class="snapbutton" type="button" name="button">Want to add a photo?</button>
</div>

<div id="container_photo">
    <video id="myVideo" autoplay="true" id="videoElement">
    </video>

    <div id="lightBox">
      <canvas id="myCanvas" width="360" height="360"></canvas>
      <div class="acceptdecline">
        <button class="snapbutton" type="button" name="button">Use it!</button>
        <button class="snapbutton" onclick="dismiss()" type="button" name="button">No thanks!</button>
      </div>

    </div>
</div>
<?php include_once 'photoscopy.php'; ?>
<div id="snapdiv">
  <button class="snapbutton" type="button" onclick="snap()" name="button">Take a photo!</button>
  <button class="snapbutton" type="button" onclick="addPhotoMenu()" name="button">Add a photo?</button>
  <button class="snapbutton" onclick="dismissPhoto()" type="button" name="button">Nevermind...</button>

</div>
<script type="text/javascript">
function addPhotoMenu() {
  _("snapdiv").style.display = "none";
  _("container_photo").style.display = "none";
  _("photos_section").style.display = "block";
}
</script>
