<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');
$host = 'localhost';
$port = 6600;
$password = null;
require_once('mpd-class/mpd.class.php');
require_once('simple_html_dom.php');
$mpd = new mpd($host,$port,$password);

require 'Predis/Autoloader.php';
Predis\Autoloader::register();
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => 'localhost',
    'port'   => 6379,
));

$current_playlist = array();

foreach ($redis->lrange('queue:5110c1e2aca8b5cbd20a278285de6b91', 0, -1) as $index => $item) {
    //echo "[$index] $item\n";
    array_push($current_playlist ,$mpd->Search(MPD_SEARCH_FILE, $item));
}

function get_album_art($file) {
	$track = substr($file, 14);
	$html = file_get_html('http://open.spotify.com/track/'.$track);
	foreach ($html->find('#big-cover') as $img) {
		return str_replace('/300/', '/140/', $img->src);
	}
}

require 'facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417716448314092',
  'secret' => '414b0ae5116a8c04679455a5553795aa',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl(array('next'=>'http://piq.fm/try/fb_logout.php'));
} else {
  $loginUrl = $facebook->getLoginUrl();
}


?>
<!DOCTYPE html> 
<html> 
<head> 
  <title>Piq</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
	<script type="text/javascript" src="//use.typekit.net/icg6edh.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head> 
<body> 
  
  <div id="page"> 
		<div id="top">
			<a id="back" href="#"><img src="img/back-arrow-3.png" alt="back"/></a>
			<h1 id="room" class="tk-nimbus-sans-condensed">piq</h1>
      <!-- <a id="login" class="tk-nimbus-sans-condensed" href="#">login with facebook</a> -->

		</div>
    <div id="shade"></div>
      <!--start add song -->
    <div id="add-song">

      <a href="#" id="close-search" class="tk-nimbus-sans-condensed"/>close</a>
      <div id="fetch">
        <input type="text" id="term" value="enter song or artist" onblur="if (this.value == '') {this.value = 'enter song or artist';}"  onfocus="if (this.value == 'enter song or artist') {this.value = '';}"/>
        <button id="search"><img src="img/right-arrow.png" /></button>
      </div>

        <!-- songs/tracks -->
        <div class="slide" id="track-group">
          <header id="track">
            <h1 class="tk-nimbus-sans-condensed">Tracks</h1>
            <button class="more"><img src="img/more-icon.png" /></button>
          </header>
          <ul id="resultsTrack" class="results tk-nimbus-sans-condensed">
          </ul>
        </div> 
        <!-- end songs/tracks -->

      <!-- start artists -->
      <div class="slide" id="artist-group">
        <header id="artist">
          <h1 class="tk-nimbus-sans-condensed">Artists</h1>
          <button class="more2"><img src="img/more-icon.png" /></button>
        </header>
          <ul id="resultsArtist" class="results tk-nimbus-sans-condensed">
          </ul>
        </div>
        <!-- end artists -->

      </div><!-- end search-->

 <ol> 
      <?php $i = 1; ?>
      <?php foreach($current_playlist as $key=>$val): ?>
        <li <?php echo $key == 0 ? 'data-theme="e"' : '' ?> <?php if($i == 1) echo 'class="expand"'?>>
          <div data-trackid="<?php echo $current_playlist[$key][0]['file'] ?>" class="track-info">
  					<img class="album-art" data-file="<?php echo $current_playlist[$key][0]['file'] ?>" src="" />
            <h1 class="tk-nimbus-sans-condensed"><?php echo $current_playlist[$key][0]['Title'] ?>
            <span><?php echo $current_playlist[$key][0]['Artist'] ?></span></h1>
            <div class="social-scoring"> 
              <a class="vote-up" href="#">vote-up</a>
              <p class="tk-nimbus-sans-condensed vote-count">23</p>
              <a class="vote-down" href="#">vote-down</a>
            </div>
            <!-- <p><?php echo sprintf('%d:%02d',floor($current_playlist[$key][0]['Time'] / 60),$current_playlist[$key][0]['Time'] % 60) ?></p> -->
          </div>
          <div class="pos-bar"><div class="fill-pos" data-pos="1200"></div></div>
          <div data-userid="fb263342" class="social-info">
            <img class="user-pic" src="http://www.sofreakinannoying.com/media/images/generic_user_image.jpg" alt="profile-pic"/> 
            <h2 class="tk-franklin-gothic-urw-cond">Lauren Ipsum<em> piq'd this song</em></h2>
            <a class="share" href="https://twitter.com/intent/tweet?text=I%20am%20listening%20to%20<?php echo $current_playlist[$key][0]['Title'] ?>%20by%20<?php echo $current_playlist[$key][0]['Artist'] ?>%20on%20@piqFM">
            <img src="img/share-button.png"></a>
          </div>
        </li>
      <?php $i++;?>
      <?php endforeach; ?>  	
    </ol>
  </div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/1.0.1/lodash.min.js"></script>
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<script src="/try/js/scripts.js"></script>
<script src="js/search.js"></script>
<script>
$("ol").append('<div style="height: 52px;"></div>');
  	$(document).ready(function(){
			nobrowser();
			expand();
			vote();	
			// addSong();		
    });
$(document).ready(function(){
      $('.album-art').each(function() {
        var image = $(this);
        $.ajax({
          url: '/try/artwork.php',
          data: {uri : $(this).data('file')},
          success: function(data) {
            data.url = data.url.split('http:');
            data.url = data.url[1];
            data.url
            image.attr({
              src: data.url,
              title: "Album Art",
              alt: "Album Art"
            });
          }
        });
      });
         
    $('.vote-count').each(function() {
      var counter = $(this).parent().parent().data("trackid");
      console.log(counter);
      var voting = $(this);
         $.ajax({
           url: '/try/votes.php?song_id='+counter,
           success: function(data) {
            if (data.votes < 2){
              texters = data.popularity;
            } else {
              texters = data.popularity;
            }
            voting.html(texters);
          }
        });
      });

      var userId = "<?= $user ?>"
      ,   name = "<?= $user_profile['name'] ?>"
      ,   logoutURL = "<?= $logoutUrl ?>"
      ,   loginURL = "<?= $loginUrl ?>";
      
      console.log(name);

      if (userId == 0){
        var el = '<div id="login"><a class="tk-franklin-gothic-urw-cond" data-ajax="false" href='+loginURL+'><img src="http://dabuttonfactory.com/b.png?t=login%20with%20facebook%20to%20add%20songs&f=DejaVuSansCondensed-Bold&ts=19&tc=ffffff&tshs=1&tshc=222222&it=png&c=0&bgt=gradient&bgc=47c&ebgc=238&hp=20&vp=9" alt="login"/></a><div>';
        $("#add-song").prepend(el);
        $("#fetch").css("display", "none");
      } else {
        $("#fetch").css("display", "block");
        $("#top").append('<div id="logged-in"><a href="'+logoutURL+'"id="logout" class="tk-franklin-gothic-urw-cond">logout</a><p id="user-name" class="tk-franklin-gothic-urw-cond">'+name+'</p></div>');

      }


        // <?php if ($user): ?>
        //   <p class="tk-franklin-gothic-urw-cond" id="login">Hello <?php echo $user_profile['name'] ?>(<a href="<?php echo $logoutUrl; ?>">logout</a>)</p>
        // <?php else: ?>
        //   <a class="tk-franklin-gothic-urw-cond" id="login" data-ajax="false" href="<?php echo $loginUrl; ?>">login to add songs</a>
        // <?php endif ?>

    });
  </script>
  <script src="http://js.pusher.com/1.12/pusher.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    // Enable pusher logging - don't include this in production
    Pusher.log = function(message) {
      if (window.console && window.console.log) window.console.log(message);
    };

    // Flash fallback logging - don't include this in production
    WEB_SOCKET_DEBUG = true;

    var pusher = new Pusher('1b4935077edffe19a865');
    var channel = pusher.subscribe('refresh');
    channel.bind('refresh', function() {
      $.ajax({
        url: "http://piq.fm/try/reque.php"
      }).done(function(){
      window.location.reload();});
  });
  </script>

</body>
</html>
<?php
$mpd->Disconnect();
?>
