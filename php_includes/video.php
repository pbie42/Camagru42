<div id="lightBoxBg" onclick="dismiss()">

</div>
<div id="addphotodiv" onclick="addPhoto()">
  <button id="addphoto" class="snapbutton" type="button" name="button">Want to add a photo?</button>
</div>

<div id="container_photo">
    <video id="myVideo" autoplay="true" id="videoElement">
    </video>
    <canvas id="myCanvas2" width="360" height="271" ondrop="add_img(event)" ondragover="event.preventDefault()"></canvas>
    <div id="lightBox">
      <canvas id="myCanvas" width="360" height="270"></canvas>

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
      while (++$i < 50) {
        echo 	'<img id="img_'.$i.'" class="mask" src="masks/'.$i.'.png" draggable="true" ondragstart="select_img(event)"></img>';
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

      ctx.drawImage(dropElement, dropX, dropY)


  }
}

var obj = [];
var dragonce = false;
var canvas = document.getElementById("myCanvas2");
var ctx = canvas.getContext("2d");
var width = 360;
var height = 270;
//function moveIt() {


var dragok = false;

function select_img(e)
{
	console.log(e.target.src);
	e.dataTransfer.setData("text", e.target.src);
}

function add_img(e)
{
	e.preventDefault();
	init_drag(e.dataTransfer.getData("text"));
}

function init_drag(img_src)
{
	var tmp;

	tmp = {img: new Image(), size: 0, dragok: false, x: 50, y: 50};
	tmp.img.src = img_src;
	tmp.size = tmp.img.width > 150 ? 150 / tmp.img.width : 1;
	obj.push(tmp);
	canvas.onmousedown = myDown;
	canvas.onmouseup = myUp;
	canvas.ondblclick = myZoomIn;
	canvas.oncontextmenu = myZoomOut;
	canvas.onmousemove = myMove;
}

function draw()
{
	ctx.clearRect(0, 0, width, height);
	obj.forEach(function(item, i)
	{
		ctx.drawImage(item.img, item.x - item.img.width * item.size / 2, item.y - item.img.height
			* item.size / 2, item.img.width * item.size, item.img.height * item.size);
	});
}

function myMove(e)
{
	var curs = false;
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas.offsetLeft && e.pageX > item.x - 50 +
			canvas.offsetLeft && e.pageY < item.y + 50 + canvas.offsetTop &&
			e.pageY > item.y - 50 + canvas.offsetTop)
			curs = true;
		canvas.style.cursor = curs ? 'pointer' : 'default';
		if (item.dragok)
		{
			item.x = e.pageX - canvas.offsetLeft;
			item.y = e.pageY - canvas.offsetTop;
		}
	});
}

function myZoomIn(e)
{
	e.preventDefault();
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas.offsetLeft && e.pageX > item.x - 50 +
			canvas.offsetLeft && e.pageY < item.y + 50 + canvas.offsetTop &&
			e.pageY > item.y - 50 + canvas.offsetTop)
			item.size *= 1.2;
	});
}

function myZoomOut(e)
{
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas.offsetLeft && e.pageX > item.x - 50 +
			canvas.offsetLeft && e.pageY < item.y + 50 + canvas.offsetTop &&
			e.pageY > item.y - 50 + canvas.offsetTop)
			item.size /= 1.2;
	});
	e.preventDefault();
}

function myDown(e)
{
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas.offsetLeft && e.pageX > item.x - 50 +
			canvas.offsetLeft && e.pageY < item.y + 50 + canvas.offsetTop &&
			e.pageY > item.y - 50 + canvas.offsetTop)
		{
			if (e.button == 0 && !dragonce)
			{
				dragonce = true;
				item.dragok = true;
			}
			if (e.button == 1)
				obj.splice(i, 1);
		}
	});
}

function myUp()
{
	obj.forEach(function(item, i)
	{
		item.dragok = false;
	});
	dragonce = false;
	canvas.style.cursor = 'default';
}


setInterval(draw, 10);


</script>
