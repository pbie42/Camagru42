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
          <canvas id="myUploadCanvas" width="640"></canvas>
          <canvas id="myUploadCanvas2" ondrop="add_img2(event)" ondragover="event.preventDefault()"></canvas>
        </div>
        <div id="acceptdeclineupload">
          <div id="toolbox">
            <?php
              $i = 0;
              while (++$i < 50) {
                echo 	'<img id="img_'.$i.'" class="mask" src="masks/'.$i.'.png" draggable="true" ondragstart="select_img2(event)"></img>';
              }
            ?>
          </div>
          <form id="pic_form" action="php_parsers/photo_system.php" method="post">
            <input id="snap_comment" type="text" class="snapcomment" name="comment_camagru" placeholder=" Add a comment about this photo?" />
          </form>
          <button class="snapbutton" onclick="screenshot()" type="button" name="button">Use it!</button>
          <button class="snapbutton" onclick="dismissupload()" type="button" name="button">No thanks!</button>
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

var MAX_WIDTH = 640;
function render(src){
	var image = new Image();
	image.onload = function(){
		var canvas = document.getElementById("myUploadCanvas");
		if(image.width > MAX_WIDTH) {
			image.height *= MAX_WIDTH / image.width;
			image.width = MAX_WIDTH;
		}
    console.log(image.height);
    console.log(image.width);
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
    console.log(height);
    console.log(width);

    _("myUploadCanvas").style.height = image.height;
    _("myUploadCanvas").style.width = image.width;
    _("myUploadCanvas2").style.height = containerheight + "px";
    _("myUploadCanvas2").style.width = containerwidth + "px";
		var ctx = canvas.getContext("2d");
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		canvas.width = image.width;
		canvas.height = image.height;
		ctx.drawImage(image, 0, 0, image.width, image.height);
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

}
var obj = [];
var dragonce = false;
var canvas2 = document.getElementById("myUploadCanvas2");
var camagru = document.getElementById('myCanvas3');
var video = document.querySelector("#myVideo");
var ctx = canvas2.getContext("2d");
var width = 640;
var height = 480;
var myphoto = false;
//function moveIt() {


var dragok = false;

function select_img2(e)
{
	console.log(e.target.src);
	e.dataTransfer.setData("text", e.target.src);
}

function add_img2(e)
{
  console.log("We are getting here");
	e.preventDefault();
	init_drag2(e.dataTransfer.getData("text"));
}

function init_drag2(img_src)
{
	var tmp;
  console.log("init_drag2");
	tmp = {img: new Image(), size: 0, dragok: false, x: 0, y: 0};
	tmp.img.src = img_src;
	tmp.size = tmp.img.width > 150 ? 150 / tmp.img.width : 1;
	obj.push(tmp);
  console.log("init_drag2 getting here");
	canvas2.onmousedown = myDown2;
	canvas2.onmouseup = myUp2;
	canvas2.ondblclick = myZoomIn2;
	canvas2.oncontextmenu = myZoomOut2;
	canvas2.onmousemove = myMove2;
}

function draw2()
{
  console.log("draw2");
	ctx.clearRect(0, 0, width, height);
	obj.forEach(function(item, i)
	{
		ctx.drawImage(item.img, item.x - item.img.width * item.size / 2, item.y - item.img.height
			* item.size / 2, item.img.width * item.size, item.img.height * item.size);
	});
}

function myMove2(e)
{
  console.log("myMove2");
	var curs = false;
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
			curs = true;
		canvas2.style.cursor = curs ? 'pointer' : 'default';
		if (item.dragok)
		{
			item.x = e.pageX - canvas2.offsetLeft;
			item.y = e.pageY - canvas2.offsetTop;
		}
	});
}

function myZoomIn2(e)
{
  console.log("myZoomIn2");
	e.preventDefault();
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
			item.size *= 1.2;
	});
}

function myZoomOut2(e)
{
  console.log("myZoomOut2");
	obj.forEach(function(item, i)
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
  console.log("myDown2");
	obj.forEach(function(item, i)
	{
		if (e.pageX < item.x + 50 + canvas2.offsetLeft && e.pageX > item.x - 50 +
			canvas2.offsetLeft && e.pageY < item.y + 50 + canvas2.offsetTop &&
			e.pageY > item.y - 50 + canvas2.offsetTop)
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

function myUp2()
{
  console.log("myUp2");
	obj.forEach(function(item, i)
	{
		item.dragok = false;
	});
	dragonce = false;
	canvas2.style.cursor = 'default';
}

function screenshot2()
{
	var pic_form = document.querySelector('#pic_form');
  var comment = document.getElementById('snap_comment').value;
  console.log(comment);
	var data, post;

	if (!obj[0])
		return ;

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

setInterval(draw2, 10);
    </script>
  </body>
</html>
