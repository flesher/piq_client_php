<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
header('Content-type: application/json');

$song_id = $_REQUEST['song_id'];

// Get our database connection
$mysqli = new mysqli('localhost','piq','piq13','piq');

function getPiqd($song, $mysqli) {
  $query = $mysqli->prepare('SELECT facebook_id FROM piq_que WHERE song_id = ?');
  $query->bind_param('s', $song);
  $query->execute();
  $query->bind_result($facebook_id);
  $query->fetch();
  $query->close();
  return $facebook_id;
}

require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417716448314092',
  'secret' => '414b0ae5116a8c04679455a5553795aa',
));

// See if there is a user from a cookie
$user = $facebook->getUser(getPiqd($song_id));

if ($user && $song_id) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    
    
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}


if ($song_id)
{
  echo json_encode(array('popularity' => popularity($song_id, $mysqli), 'votes' => voteCount($song_id, $mysqli)));
} else {
  echo json_encode(array('err' => 'No Song Id'));
}

?>