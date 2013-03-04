<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');
$host = 'localhost';
$port = 6600;
$password = null;
require_once('mpd-class/mpd.class.php');
require_once('simple_html_dom.php');
$mpd = new mpd($host,$port,$password);

$current_playlist = $mpd->playlist;

print_r($current_playlist);

foreach ($current_playlist as $key=>$val) {
  $current_id = $current_playlist[$key]['Id'];
  if ($mpd->current_track_id == $current_id) {
    echo "die";
    die();
  } else {
    echo $key;
    $mpd->PLRemove($current_id);
  }
} 

$mpd->Disconnect();
?>