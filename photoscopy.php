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
  $photo_form .= '<h3>Hi <span class="username">'.$u.'</span>! <br />Want to add a photo from your computer to your feed?</h3>';
  //$photo_form .= ' &nbsp; &nbsp; &nbsp; <b>Choose Photo:</b> ';
  //$photo_form .= '<input type="file" name="photo" accept="image/*" required />';
  //$photo_form .= '<p><input type="submit" value="Upload Photo Now" /></p>';
  $photo_form .= '<p><h3>Simply drag and drop an image file from your computer here!!</h3></p>';
  $photo_form .= '<p><input id="nevermind_photo_btn" class="inputfile" /><label id="choose_photo_label" for="nevermind_photo_btn" onclick="backToPhotoMenu()">Nevermind...</label></p>';
  $photo_form .= '</form>';
}
//Select the user galleries
$gallery_list = "";
$query_gallery = $db_conx2->prepare("SELECT DISTINCT gallery FROM photos WHERE user='$u'");
$query_gallery->execute();
$query_gallery_num_rows = $query_gallery->fetchColumn();
if ($query_gallery_num_rows < 1) {
  $gallery_list = "This user has not uploaded any photos yet.";
} else {
  $query_gallery2 = $db_conx2->prepare("SELECT DISTINCT gallery FROM photos WHERE user='$u'");
  $query_gallery2->execute();
  while ($row = $query_gallery2->fetch(PDO::FETCH_ASSOC)) {
    $gallery = $row["gallery"];
    $countquery = $db_conx2->prepare("SELECT COUNT(id) FROM photos WHERE user='$u' AND gallery='$gallery'");
    $countquery->execute();
    $countrow = $countquery->fetch(PDO::FETCH_NUM);
    $count = $countrow[0];
    $filequery = $db_conx2->prepare("SELECT filename FROM photos WHERE user='$u' AND gallery='$gallery' ORDER BY RAND() LIMIT 1");
    $filequery->execute();
    $filerow = $filequery->fetch(PDO::FETCH_NUM);
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
    <div id="drop-target">
      <div id="photo_form">
        <?php echo $photo_form; ?>
      </div>
    </div>

    <div id="photos">

    </div>
    <div id="picbox">

    </div>
  </div>
</div>
<div id="container_upload" width="640" height="480">
  <canvas id="myUploadCanvas3" width="640"></canvas>
  <canvas id="myUploadCanvas" width="640"></canvas>
  <canvas id="myUploadCanvas2" height="200" width="200" ondrop="add_img2(event)" ondragover="event.preventDefault()"></canvas>
</div>
<div id="acceptdeclineupload">
  <div id="toolbox">
    <?php
      $i = 0;
      while (++$i < 52) {
        echo 	'<img id="img_'.$i.'" class="mask" src="masks/'.$i.'.png" draggable="true" ondragstart="select_img2(event)"></img>';
      }
    ?>
  </div>
  <p class="camagrufont">
    Once added to canvas double click to enlarge, right click to make smaller, scroll click to delete.
  </p>
  <form id="pic_form2" action="php_parsers/photo_system.php" method="post"></form>
    <input id="snap_comment2" type="text" class="snapcomment" onkeydown="enterCheck(event,this);" name="comment_camagru" placeholder=" Add a comment about this photo?" />

  <button class="snapbutton" onclick="screenshot2()" type="button" name="button">Use it!</button>
  <button class="snapbutton" onclick="dismissupload()" type="button" name="button">No thanks!</button>
  <span id="uploadcamagru" class="camagrufont"></span>
</div>
    <script type="text/javascript">
    function enterCheck(e,btn) {
    	var keycode = e.keyCode;
    	if (keycode == 13) {
        return false;
    	}
    }
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
    var MAX_WIDTH = 600;
function render(src){
	var image = new Image();
	image.onload = function(){
		var canvas3 = document.getElementById("myUploadCanvas");
		if(image.width > MAX_WIDTH) {
			image.height *= MAX_WIDTH / image.width;
			image.width = MAX_WIDTH;
		}
    var containerwidth = image.width;
    var containerheight = image.height;
    var realheight = containerheight - 1;
    _("container_upload").style.display = "block";
    _("container_upload").style.height = realheight + "px";
    _("container_upload").style.width = containerwidth + "px";
    _("acceptdeclineupload").style.display = "block";
    _("photos_section").style.display = "none";

    var width = _("container_upload").style.width;
    var height = _("container_upload").style.height;

    _("myUploadCanvas").style.height = image.height;
    _("myUploadCanvas").style.width = image.width;
    _("myUploadCanvas2").height = realheight;
    _("myUploadCanvas2").width = containerwidth;
		var ctx3 = canvas3.getContext("2d");
		ctx3.clearRect(0, 0, canvas3.width, canvas3.height);
		canvas3.width = image.width;
		canvas3.height = image.height;
		ctx3.drawImage(image, 0, 0, image.width, image.height);
	};
	image.src = src;
}
function loadImage(src){
	//	Prevent any non-image file type from being read.
	if(!src.type.match(/image.*/)){
		console.log("The dropped file is not an image: ", src.type);
		return;
	}

	//	Create our FileReader and run the results through the render function.
	var reader = new FileReader();
	reader.onload = function(e){
		render(e.target.result);
	};
	reader.readAsDataURL(src);
}
var target = document.getElementById("drop-target");
target.addEventListener("dragover", function(e){e.preventDefault();}, true);
target.addEventListener("drop", function(e){
	e.preventDefault();
	loadImage(e.dataTransfer.files[0]);
}, true);
function dismissupload() {

  _("container_upload").style.display = "none";
  _("acceptdeclineupload").style.display = "none";
  _("photos_section").style.display = "block";
  _("uploadcamagru").style.display = "none";

}
var obj2 = [];
var dragonce2 = false;
var canvas2 = document.getElementById("myUploadCanvas2");
//var camagru = document.getElementById('myCanvas3');
//var video = document.querySelector("#myVideo");
var ctx2 = canvas2.getContext("2d");
var width2;
var height2;
var myphoto2 = false;
//function moveIt() {


var dragok2 = false;

function select_img2(e)
{
	e.dataTransfer.setData("text", e.target.src);
}

function add_img2(e)
{
	e.preventDefault();
	init_drag2(e.dataTransfer.getData("text"));
}

function init_drag2(img_src)
{
	var tmp2;
	tmp2 = {img: new Image(), size: 0, dragok: false, x: 50, y: 50};
	tmp2.img.src = img_src;
	tmp2.size = tmp2.img.width > 100 ? 100 / tmp2.img.width : 1;
	obj2.push(tmp2);
	canvas2.onmousedown = myDown2;
	canvas2.onmouseup = myUp2;
	canvas2.ondblclick = myZoomIn2;
	canvas2.oncontextmenu = myZoomOut2;
	canvas2.onmousemove = myMove2;
}

function draw2()
{
  width2 = _("myUploadCanvas").width;
  height2 = _("myUploadCanvas").height;

	ctx2.clearRect(0, 0, width2, height2);
	obj2.forEach(function(item, i)
	{
		ctx2.drawImage(item.img, item.x - item.img.width * item.size / 2, item.y - item.img.height
			* item.size / 2, item.img.width * item.size, item.img.height * item.size);
	});
}

function myMove2(e)
{
	var curs2 = false;
	obj2.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
			curs2 = true;
		canvas2.style.cursor = curs2 ? 'pointer' : 'default';
		if (item.dragok2)
		{
			item.x = e.pageX - canvas2.offsetLeft;
			item.y = e.pageY - canvas2.offsetTop;
		}
	});
}

function myZoomIn2(e)
{
	e.preventDefault();
	obj2.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
			item.size *= 1.2;
	});
}

function myZoomOut2(e)
{
	obj2.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
			item.size /= 1.2;
	});
	e.preventDefault();
}

function myDown2(e)
{
	obj2.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
		{
			if (e.button == 0 && !dragonce2)
			{
				dragonce2 = true;
				item.dragok2 = true;
			}
			if (e.button == 1)
				obj2.splice(i, 1);
		}
	});
}

function myUp2()
{
	obj2.forEach(function(item, i)
	{
		item.dragok2 = false;
	});
	dragonce2 = false;
	canvas2.style.cursor = 'default';
}

function screenshot2()
{
  useIt();
  var camagru2 = document.getElementById('myUploadCanvas3');
	var pic_form2 = document.querySelector('#pic_form2');
  var comment2 = document.getElementById('snap_comment2').value;
  _("snap_comment2").style.display = "none";
	var data2, post2;

	if (!obj2[0]) {
    _("uploadcamagru").style.display = "block";
    _("uploadcamagru").innerHTML = "Please add one of our images to your photo in order to continue.";
    return ;
  }


	data2 = camagru2.toDataURL('image/png');

  var comment_post2 = '<input id="snap_comment2" type="text" class="snapcomment" name="comment_camagru" placeholder=" Add a comment about this photo?" value="'+comment2+'" />';
	if (data2.length > 500000)
	{
		post2 = '<input class="camagru_data" type="text" name="cam" value="'+data2.substr(0, 500000)
			+'"></input><input class="camagru_data" type="text" name="cam1" value="'+data2.slice(500000)
			+'"></input>';
	}
	else
		post2 = '<input class="camagru_data" type="text" name="cam" value="'+data2+'"></input>';
	pic_form2.innerHTML = comment_post2 + post2;
	pic_form2.submit();
}

function useIt() {
  canvas.getContext('2d').drawImage(video, 0, 0);
  var myUC = document.getElementById('myUploadCanvas');
  var myUC2 = document.getElementById('myUploadCanvas2');
  var ucan3 = document.getElementById('myUploadCanvas3');

  ucan3.width = _("myUploadCanvas2").width;
  ucan3.height = _("myUploadCanvas2").height;
  var uctx3 = ucan3.getContext('2d');

  uctx3.drawImage(myUC, 0, 0);
  uctx3.drawImage(myUC2, 0, 0);
}

setInterval(draw2, 10);
    </script>
  </body>
</html>
