<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
header('Content-type: application/json');

$song_id = $_REQUEST['song_id'];

// Get our database connection
$mysqli = new mysqli('localhost','piq','piq13','piq');

function popularity($song, $mysqli) {
  $query = $mysqli->prepare('SELECT SUM(vote) as popularity FROM piq_votes WHERE song_id = ?');
  $query->bind_param('s', $song);
  $query->execute();
  $query->bind_result($popularity);
  $query->fetch();
  $query->close();
  return $popularity;
}

function voteCount($song, $mysqli) {
  $query = $mysqli->prepare('SELECT COUNT(id) as votes FROM piq_votes WHERE song_id = ?');
  $query->bind_param('s', $song);
  $query->execute();
  $query->bind_result($votes);
  $query->fetch();
  $query->close();
  return $votes;
}

if ($song_id)
{
  echo json_encode(array('popularity' => popularity($song_id, $mysqli), 'votes' => voteCount($song_id, $mysqli)));
} else {
  echo json_encode(array('err' => 'No Song Id'));
}

?>