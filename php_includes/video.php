<div id="lightBoxBg">

</div>
<div id="addphotodiv" onclick="addPhoto()">
  <button id="addphoto" class="snapbutton" type="button" name="button">Want to add a photo?</button>
</div>

<div id="container_photo">
    <video id="myVideo" autoplay="true" id="videoElement">
    </video>
    <div id="lightBox">
      <canvas id="myCanvas" width="700" height="700"></canvas>
      <div class="acceptdecline">
        <button class="snapbutton" type="button" name="button">Use it!</button>
        <button class="snapbutton" onclick="dismiss()" type="button" name="button">No thanks!</button>

      </div>

    </div>
</div>
<div id="snapdiv">
  <button class="snapbutton" type="button" onclick="snap()" name="button">Take a photo!</button>
  <button class="snapbutton" type="button" onclick="" name="button">Add a photo?</button>
  <button class="snapbutton" onclick="dismissPhoto()" type="button" name="button">Nevermind...</button>
</div>
