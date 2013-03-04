<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');
require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417716448314092',
  'secret' => '414b0ae5116a8c04679455a5553795aa',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

// Request Variables
$song_id = $_REQUEST['song_id'];
$vote_value = $_REQUEST['vote_value'];

// Get our database connection
$mysqli = new mysqli('localhost','piq','piq13','piq');

function alreadySelected($fbid, $song, $mysqli) {
  $query = $mysqli->prepare('SELECT COUNT(id) as votes FROM piq_votes WHERE song_id = ? AND facebook_id = ?');
  $query->bind_param('si', $song, $fbid);
  $query->execute();
  $query->bind_result($value);
  $query->fetch();
  $query->close();
  return $value > 0 ? true : false;
}

function vote($fbid, $song, $vote, $mysqli) {
  $query = $mysqli->prepare('INSERT INTO piq_votes (facebook_id, song_id, vote) VALUES (?, ?, ?)');
  $query->bind_param('isi', $fbid, $song, $vote );
  $query->execute();
  $query->close();
}

function popularity($song, $mysqli) {
  $query = $mysqli->prepare('SELECT SUM(vote) as popularity FROM piq_votes WHERE song_id = ?');
  $query->bind_param('s', $song);
  $query->execute();
  $query->bind_result($popularity);
  $query->fetch();
  $query->close();
  return $popularity;
}


if ($user && $song_id && $vote_value) {
  //
  // Check if user has already liked the song
  //
  header('Content-type: application/json');
  
  if (alreadySelected($user, $song_id, $mysqli))
  {
    //header("Location: mobile.php");
    echo json_encode(array('response' => 'Already Selected', 'votes' => popularity($song_id, $mysqli)));
  } else {
    vote($user, $song_id, $vote_value, $mysqli);
    //header("Location: mobile.php");
    echo json_encode(array('response' =>  'Refresh', 'votes' => popularity($song_id, $mysqli)));
  }
  
} else {
?>


<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Piq Vote</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>Login With Facebook to Vote</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

  </body>
</html>
<?php
}
?>
