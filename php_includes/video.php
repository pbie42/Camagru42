<div id="lightBoxBg" onclick="dismiss()">

</div>
<div id="addphotodiv" onclick="addPhoto()">
  <button id="addphoto" class="snapbutton" type="button" name="button">Want to add a photo?</button>
</div>

<div id="container_photo">
    <video id="myVideo" autoplay="true" id="videoElement">
    </video>
    <canvas id="myCanvas2" width="360" height="270"></canvas>
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
  <div id="toolbox">
    <?php
      $i = 0;
      while (++$i < 44) {
        echo 	'<img id="img_'.$i.'" class="mask" src="masks/'.$i.'.png" draggable="true" onmouseover="camagru(\''.$i.'\')"></img>';
      }
    ?>
  </div>
</div>
<script type="text/javascript">
function addPhotoMenu() {
  _("snapdiv").style.display = "none";
  _("container_photo").style.display = "none";
  _("photos_section").style.display = "block";
}
function camagru(imgno) {
  var canvas = document.getElementById("myCanvas2");
  var ctx = canvas.getContext("2d");
  var canvasLeft = canvas.offsetLeft;
  var canvasTop = canvas.offsetTop;
  var canvasLeft = 1010;
  var canvasTop = 116;
  canvas.ondrop = drop;
  canvas.ondragover = allowDrop;

  console.log(canvasLeft);
  console.log(canvasTop);

    var img = document.getElementById("img_"+imgno);
    img.onmousedown = mousedown;
    img.ondragstart = dragstart;
  // this is the mouse position within the drag element
  var startOffsetX, startOffsetY;

  function allowDrop(ev) {
      ev.preventDefault();
  }

  function mousedown(ev) {
      startOffsetX = ev.offsetX;
      startOffsetY = ev.offsetY;
      console.log("startOffsetX");
      console.log("startOffsetY");
      console.log(startOffsetX);
      console.log(startOffsetY);
  }


  function dragstart(ev) {
      ev.dataTransfer.setData("Text", ev.target.id);
  }

  function drop(ev) {
      ev.preventDefault();
      console.log("ev.clientX");
      console.log("ev.clientY");
      console.log(ev.clientX);
      console.log(ev.clientY);
      var dropX = ev.clientX - canvasLeft - startOffsetX;
      var dropY = ev.clientY - canvasTop - startOffsetY;
      var id = ev.dataTransfer.getData("Text");
      var dropElement = document.getElementById(id);
      console.log("dropX");
      console.log("dropY");
      console.log(dropX);
      console.log(dropY);
      // draw the drag image at the drop coordinates

      if (ctx.drawImage(dropElement, dropX, dropY)) {
        console.log("supposedly it worked");
      }
      console.log(dropElement);
  }
}

</script>
