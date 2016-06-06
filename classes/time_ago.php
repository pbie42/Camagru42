<?php
/**
 *
 */
class convertToAgo{

  function convert_datetime($str){
    list($date, $time) = explode(' ', $str);
    list($year, $month, $day) = explode('-', $date);
    list($hour, $minute, $second) = explode(':', $time);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    return $timestamp;
  }
  function makeAgo($now, $timestamp){
    $difference = $now - $timestamp;
    $periods = array("s", "m", "h", "d", "w", "m", "y", "dec");
    $lengths = array("60","60","24","7","4.35","12","10");
    for ($i=0; $difference >= $lengths[$i]; $i++)
      $difference /= $lengths[$i];
      $difference = round($difference);
      $text = "$difference$periods[$i]";
      return $text;
  }
}
?>
