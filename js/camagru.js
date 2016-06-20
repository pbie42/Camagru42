function _(x) {
  return document.getElementById(x);
}

function toggleElement(x) {
  var x = _(x);
  if (x.style.display == 'block') {
    x.style.display = 'none';
  } else {
    x.style.display = 'block';
  }
}

function trim1 (str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function camera() {
  navigator.getUserMedia = (navigator.getUserMedia ||
          navigator.webkitGetUserMedia ||
          navigator.mozGetUserMedia ||
          navigator.msGetUserMedia);

window.URL = (window.URL || window.mozURL || window.webkitURL);
  navigator.getUserMedia({ video: true }, function(stream) {
  //Do stuff with the video stream here...
  //Get the video element
  var video = document.getElementById('myVideo');

  //Set it to receive input from the webcam
  video.src = window.URL.createObjectURL(stream);

  //Get the canvas element
  var canvas = document.getElementById('myCanvas');

  //Wait 50 milliseconds; this is to allow the webcam to capture some video
  setTimeout(function () {

    //Set the canvas height/width to the size of the video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    //Draw the video frame to the canvas
    canvas.getContext('2d').drawImage(video, 0, 0);
  }, 50);
}, function (){console.warn("Error getting audio stream from getUserMedia")});
}


function snap(){
  navigator.getUserMedia({ video: true }, function(stream) {
  //Do stuff with the video stream here...
  //Get the video element
  var video = document.getElementById('myVideo');

  //Set it to receive input from the webcam
  video.src = window.URL.createObjectURL(stream);

  //Get the canvas element
  var canvas = document.getElementById('myCanvas');

  var canvas2 = document.getElementById("myCanvas2");
  var ctx = canvas2.getContext("2d");
  var canvasLeft = canvas2.offsetLeft;
  var canvasTop = canvas2.offsetTop;

  function startLightBox () {
    var lbBg = document.getElementById('lightBoxBg');
    var lb = document.getElementById('lightBox');
    var mv = document.getElementById('myVideo');
    var sd = document.getElementById('snapdiv');
    var myC2 = document.getElementById('myCanvas2');
    var myC = document.getElementById('myCanvas');

    myC.style.display= "none";
    myC2.style.display = "none";
    myC2.style.zIndex = 4;
    myC2.style.marginTop = 0;
    lbBg.style.display = "block";
    lb.style.display = "block";
    mv.style.display = "none";
    sd.style.display = "none";
  };
  //Wait 50 milliseconds; this is to allow the webcam to capture some video
  setTimeout(function () {
    startLightBox();
    //Set the canvas height/width to the size of the video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    //Draw the video frame to the canvas
    canvas.getContext('2d').drawImage(video, 0, 0);
    var myC2 = document.getElementById('myCanvas2');
    var can3 = document.getElementById('myCanvas3');
    can3.width = video.videoWidth;
    can3.height = video.videoHeight;
    var ctx3 = can3.getContext('2d');

    ctx3.drawImage(canvas, 0, 0);
    ctx3.drawImage(myC2, 0, 0);
  }, 100);
}, function (){console.warn("Error getting audio stream from getUserMedia")});
};

function dismiss() {
  var lbBg = document.getElementById('lightBoxBg');
  var lb = document.getElementById('lightBox');
  var mv = document.getElementById('myVideo');
  var sd = document.getElementById('snapdiv');
  //location.href = "index.php";
  var myC2 = document.getElementById('myCanvas2');
  var myC = document.getElementById('myCanvas');

  myC.style.display = "";
  myC2.style.display = "";
  myC2.style.zIndex = "";
  myC2.style.marginTop = "";
  myC2.style.position = "absolute";

  lbBg.style.display = "none";
  lb.style.display = "none";
  mv.style.display = "block";
  sd.style.display = "block";
}

function addPhoto () {
  var apd = document.getElementById('addphotodiv');
  var cp = document.getElementById('container_photo');
  var sd = document.getElementById('snapdiv');
  console.log("This working?");
  camera();
  apd.style.display = "none";
  cp.style.display = "block";
  sd.style.display = "block";
}

function dismissPhoto () {
  var apd = document.getElementById('addphotodiv');
  var cp = document.getElementById('container_photo');
  var sd = document.getElementById('snapdiv');

  apd.style.display = "block";
  cp.style.display = "none";
  sd.style.display = "none";
}
