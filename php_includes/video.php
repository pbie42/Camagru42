<div id="lightBoxBg" onclick="dismiss()">

</div>
<div id="addphotodiv" onclick="addPhoto()">
  <button id="addphoto" class="addphotobutton" type="button" name="button">Want to add a photo?</button>
</div>

<div id="container_photo">
    <video id="myVideo" autoplay="true" id="videoElement">
    </video>
    <canvas id="myCanvas2" width="640" height="480" ondrop="add_img(event)" ondragover="event.preventDefault()"></canvas>
    <div id="lightBox">
      <canvas id="myCanvas" width="640" height="480"></canvas>
      <canvas id="myCanvas3" width="640" height="480"></canvas>
      <div class="acceptdecline">
        <form id="pic_form" action="php_parsers/photo_system.php" method="post"></form>
          <input id="snap_comment" type="text" class="snapcomment" name="comment_camagru" placeholder=" Add a comment about this photo?" />

        <button class="snapbutton" onclick="screenshot()" type="button" name="button">Use it!</button>
        <button class="snapbutton" onclick="dismiss()" type="button" name="button">No thanks!</button>
        <p id="pleaseaddphoto" class="camagrufont">
          Please add an image to your photo in order to proceed !
        </p>

      </div>

    </div>
</div>
<?php include_once 'photoscopy.php'; ?>
<div id="snapdiv">
  <button class="snapbutton" type="button" onclick="snap()" name="button">Take a photo!</button>
  <button class="snapbutton" type="button" onclick="addPhotoMenu()" name="button">Add a photo?</button>
  <button class="snapbutton bottombutton" onclick="dismissPhoto()" type="button" name="button">Nevermind...</button>
  <div id="toolbox">
    <?php
      $i = 0;
      while (++$i < 52) {
        echo 	'<img id="img_'.$i.'" class="mask" src="masks/'.$i.'.png" draggable="true" ondragstart="select_img(event)"></img>';
      }
    ?>
  </div>
  <p class="camagrufont">
    Once added to canvas double click to enlarge, right click to make smaller, scroll click to delete.
  </p>
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
  }


  function dragstart(ev) {
      ev.dataTransfer.setData("Text", ev.target.id);
  }

  function drop(ev) {
      ev.preventDefault();
      var dropX = ev.clientX - canvasLeft - startOffsetX;
      var dropY = ev.clientY - canvasTop - startOffsetY;
      var id = ev.dataTransfer.getData("Text");
      var dropElement = document.getElementById(id);
      // draw the drag image at the drop coordinates

      ctx.drawImage(dropElement, dropX, dropY)


  }
}

var obj = [];
var dragonce = false;
var canvas = document.getElementById("myCanvas2");
var camagru = document.getElementById('myCanvas3');
var video = document.querySelector("#myVideo");
var ctx = canvas.getContext("2d");
var width = 640;
var height = 480;
var myphoto = false;
//function moveIt() {


var dragok = false;

function select_img(e)
{
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

	tmp = {img: new Image(), size: 0, dragok: false, x: 250, y: 250};
	tmp.img.src = img_src;
	tmp.size = tmp.img.width > 250 ? 250 / tmp.img.width : 1;
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

function screenshot()
{
	var pic_form = document.querySelector('#pic_form');
  var comment = document.getElementById('snap_comment').value;
  _("snap_comment").style.display = "none";
	var data, post;

	if (!obj[0]) {
    _("pleaseaddphoto").style.display = "block";
    return ;
  }


	data = camagru.toDataURL('image/png');

  var comment_post = '<input id="snap_comment" type="text" class="snapcomment" name="comment_camagru" placeholder=" Add a comment about this photo?" value="'+comment+'" />';
	if (data.length > 500000)
	{
		post = '<input class="camagru_data" type="text" name="cam" value="'+data.substr(0, 500000)
			+'"></input><input class="camagru_data" type="text" name="cam1" value="'+data.slice(500000)
			+'"></input>';
	}
	else
		post = '<input class="camagru_data" type="text" name="cam" value="'+data+'"></input>';
	pic_form.innerHTML = comment_post + post;
	pic_form.submit();
}

setInterval(draw, 20);


</script>
