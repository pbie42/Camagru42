<?php
include_once 'classes/time_ago.php'; //We created a separate folder for using classes
$timeAgoObject = new convertToAgo; //This creates an object for the time conversion functions
//Here is where we would query the database in order to get the timestamp
$ts = "2016-06-06 17:59:42";
echo time();
echo "       |      ";
echo ($timeAgoObject -> convert_datetime($ts));
echo "       |      ";
$now = time();
$dtNow = new DateTime("@$now");
$phpnow = $dtNow->format('Y-m-d H:i:s');
$NowStr = $phpnow;
$NowZoneNameFrom = "UTC";
$NowZoneNameTo = "Europe/Amsterdam";
$NowZoneGood = date_create($NowStr, new DateTimeZone($NowZoneNameFrom))->setTimezone(new DateTimeZone($NowZoneNameTo))->format("Y-m-d H:i:s");
echo $NowZoneGood;
echo "       |      ";


$convertedNow = ($timeAgoObject -> convert_datetime($NowZoneGood));
$convertedTime = ($timeAgoObject -> convert_datetime($ts)); //Convert the date time
$when = ($timeAgoObject -> makeAgo($convertedNow, $convertedTime)); //Then we convert to ago time
?>

<h2><?php echo $when; ?></h2>
