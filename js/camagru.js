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

  function startLightBox () {
    var lbBg = document.getElementById('lightBoxBg');
    var lb = document.getElementById('lightBox');
    var mv = document.getElementById('myVideo');
    var sd = document.getElementById('snapdiv');

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
  }, 100);
}, function (){console.warn("Error getting audio stream from getUserMedia")});
};

function dismiss() {
  var lbBg = document.getElementById('lightBoxBg');
  var lb = document.getElementById('lightBox');
  var mv = document.getElementById('myVideo');
  var sd = document.getElementById('snapdiv');
  //location.href = "index.php";

  lbBg.style.display = "none";
  lb.style.display = "none";
  mv.style.display = "block";
  sd.style.display = "block";
}

function addPhoto () {
  var apd = document.getElementById('addphotodiv');
  var cp = document.getElementById('container_photo');
  var sd = document.getElementById('snapdiv');

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
