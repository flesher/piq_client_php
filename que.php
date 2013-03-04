<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
));

require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417716448314092',
  'secret' => '414b0ae5116a8c04679455a5553795aa',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

// Get our database connection
$mysqli = new mysqli('localhost','piq','piq13','piq');

function alreadyPiqd($song, $mysqli) {
  $query = $mysqli->prepare('SELECT COUNT(id) as piqd FROM piq_que WHERE song_id = ?');
  $query->bind_param('s', $song);
  $query->execute();
  $query->bind_result($piqd);
  $query->fetch();
  $query->close();
  return $piqd > 0 ? true : false;
}

function queUp($fbid, $song, $mysqli) {
  $query = $mysqli->prepare('INSERT INTO piq_que (facebook_id, song_id, piq_time) VALUES (?, ?, NOW())');
  $query->bind_param('is', $fbid, $song);
  $query->execute();
  $query->close();
}

function voteUp($fbid, $song, $mysqli) {
  // Add a Vote so the redis reque will work
  $query = $mysqli->prepare('INSERT INTO piq_votes (facebook_id, song_id, vote) VALUES (?, ?, 0)');
  $query->bind_param('is', $fbid, $song);
  $query->execute();
  $query->close();
}


if ($user) 
{

  if (isset($_GET['song'])) {
  	$song_file = $_GET['song'];
  	$song_info = $_GET['info'];
  	
  	if (alreadyPiqd($song_file, $mysqli))
  	{
    	echo "This song has been already piq'd";
  	} else {
      //add the song to the redis queue
    	$redis->rpush('queue:5110c1e2aca8b5cbd20a278285de6b91', $song_file);
    	$redis->publish('status:5110c1e2aca8b5cbd20a278285de6b91', 'added-song');
    	
      voteUp($user, $song_file, $mysqli);
    	
    	queUp($user, $song_file, $mysqli);
    	
    	header("Location: mobile.php");
  	}
  }
}

?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <body>
    <fb:login-button></fb:login-button>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
  </body>
</html>
